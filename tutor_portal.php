<?php
    $login_id = $_SESSION[$LOGIN_ID];
    $login_type = $_SESSION[$LOGIN_STATUS_ID] == 1? $STUDENT : $TUTOR;  //check if profile is tutor or student
    $login_name = $_SESSION[$LOGIN_NAME];
    $login_email = $_SESSION[$LOGIN_EMAIL];
    $pid = explode("@", $login_email)[0]; //first part before @ is always pid
    if($login_type == $STUDENT){
        //STUENTS NOT ALLOWED HERE
        echo "<script>window.open('index.php', '_self');</script>";
    }

    $courses = array();

    $count_posts = countEntriesDB($con, $POST_TABLE, "where $USER_ID='$login_id'");
    $count_replies = countEntriesDB($con, $REPLY_TABLE, "where $USER_ID='$login_id'");
    $count_likes = countEntriesDB($con, $LIKE_TABLE, "where $USER_ID='$login_id'");
?>
<div class="rounded p-3 m-5">
    <div class="row bg-light rounded"> 
        <h2 class='text-center'>Tutor Portal</h2>
        <div class="col bg-light rounded">
            <div>  <!--Start of course info-->
                <div class="row p-1 p-1 text-left">
                    <div class="container bg-primary text-white my-1 p-1">
                        <h4>Course Info</h4>
                    </div>
                <table class="table">
                    <?php
                    //getting users course info
                    $user_course_query = "select * from $UC_TABLE inner join $COURSE_TABLE on $UC_TABLE.$COURSE_ID = $COURSE_TABLE.$COURSE_ID where $USER_ID= '$login_id'";
                    $user_course_result = mysqli_query($con, $user_course_query);
                    $user_course_id = 1; // initially this
                    $course_count = 1;
                    //there may be more than one courses if this is a teacher 
                    if(mysqli_num_rows($user_course_result)){
                        while($user_course = mysqli_fetch_assoc($user_course_result)){
                            $user_course_id = $user_course[$COURSE_ID];
                            array_push($courses, $user_course_id);
                            $course_title = $user_course[$COURSE_TITLE];
                            echo "
                            <tr>
                                <td><strong>Course #$course_count:</strong> $course_title</td>
                            </tr>
                            ";
                            $course_count++;
                        }
                    }
                    ?>
                </table>
            </div>  <!--End of course info-->

            <!--start of other tutors-->
            <div class="row p-1 p-1 text-left">
                <div class="container bg-primary text-white my-1 p-1">
                    <h4>Other Tutors</h4>
                </div>

                <?php
                    foreach($courses as $user_course_id){
                        showOtherUsersForCourse($user_course_id, 2);
                    }
                ?>
            </div>
            <!---End of other tutors-->

            <!--start of my Students-->
            <div class="row p-1 text-left">
                <div class="container bg-primary text-white my-1 p-1">
                    <h4>My Students</h4>
                </div>

                <?php
                    foreach($courses as $user_course_id){
                        showOtherUsersForCourse($user_course_id, 1);
                    }
                ?>
            </div>
            <!---End of my students-->

            <div> <!--start of top forums info-->
                <div class="row p-1 p-1 text-left">                      
                    <div class="container bg-primary text-white my-1 p-1">
                        <h4>Top Forums</h4>
                    </div>
                </div>
                <table class="table">
                    <?php
                        //getting users course info
                        /*the following query selects the likes and group them by post id, so we count total likes in a group, 
                        then order the posts by number of likes and then limit it to 5 to show top 5 posts*/
                        $posts_query = "select * , count(*) as numberOfPosts from $SUB_FORUM_TABLE inner join $POST_TABLE on $SUB_FORUM_TABLE.$SUB_FORUM_ID = $POST_TABLE.$SUB_FORUM_ID GROUP BY $SUB_FORUM_TABLE.$SUB_FORUM_ID order by numberOfPosts DESC limit 5";
                        $posts_result = mysqli_query($con, $posts_query);
                        $rank = 1;  // rank by number of likes, mosts likes post is number 1 and then 2 and so on...
                        //there may be more than one courses if this is a tutor 
                        if(mysqli_num_rows($posts_result)){
                            while($post = mysqli_fetch_assoc($posts_result)){
                                $post_title = $post[$SUB_FORUM_TITLE];
                                $numberOfPosts = $post['numberOfPosts'];
                                echo "
                                <tr>
                                    <td><strong>$rank. </strong> <i>$post_title</i> : $numberOfPosts Posts</td>
                                </tr>
                                ";
                                $rank++;
                            }
                        }
                    ?>
                </table>
            </div> <!--End of top forums info-->

            <div> <!--start of top posts info-->
                <div class="row p-1 p-1 text-left">                      
                    <div class="container bg-primary text-white my-1 p-1">
                        <h4>Top Posts</h4>
                        <div class="d-flex">
                            <div class="flex-shrink-0 dropdown align-self-end">
                                <a href="#" class="d-block text-white text-decoration-none dropdown-toggle" id="profiledropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <font font-size="30px">View By</font>
                                </a>
                                <ul class="dropdown-menu text-small shadow" aria-labelledby="profiledropdown">
                                <li><a class="dropdown-item" href="?tutor_portal&by_likes">Number of Likes</a></li>
                                <li><a class="dropdown-item" href="?tutor_portal&by_replies">Number of Replies</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table">
                    <?php
                    if(isset($_GET["by_likes"])){
                        //getting users course info
                        /*the following query selects the likes and group them by post id, so we count total likes in a group, 
                        then order the posts by number of likes and then limit it to 5 to show top 5 posts*/
                        $posts_query = "select * , count(*) as numberOfLikes from $POST_TABLE inner join $LIKE_TABLE on $POST_TABLE.$POST_ID = $LIKE_TABLE.$POST_ID where $LIKE_TABLE.$REPLY_ID = 0 GROUP BY $POST_TABLE.$POST_ID order by numberOfLikes DESC limit 5";
                        $posts_result = mysqli_query($con, $posts_query);
                        $rank = 1;  // rank by number of likes, mosts likes post is number 1 and then 2 and so on...
                        //there may be more than one courses if this is a tutor 
                        if(mysqli_num_rows($posts_result)){
                            while($post = mysqli_fetch_assoc($posts_result)){
                                $post_title = $post[$POST_TITLE];
                                $numberOfLikes = $post['numberOfLikes'];
                                echo "
                                <tr>
                                    <td><strong>$rank.</strong> <i>$post_title</i> : Likes ($numberOfLikes) </td>
                                </tr>
                                ";
                                $rank++;
                            }
                        }
                    }else{
                        //getting users course info
                        /*the following query selects the replies and group them by post id, so we count total likes in a group, 
                        then order the posts by number of likes and then limit it to 5 to show top 5 posts*/
                        $posts_query = "select * , count(*) as numberOfReplies from $POST_TABLE inner join $REPLY_TABLE on $POST_TABLE.$POST_ID = $REPLY_TABLE.$POST_ID WHERE DATEDIFF(CURDATE(), $REPLY_TABLE.datetime) < 7 GROUP BY $POST_TABLE.$POST_ID order by numberOfReplies DESC limit 5";
                        $posts_result = mysqli_query($con, $posts_query);
                        $rank = 1;  // rank by number of likes, mosts likes post is number 1 and then 2 and so on...
                        //there may be more than one courses if this is a tutor 
                        if(mysqli_num_rows($posts_result)){
                            while($post = mysqli_fetch_assoc($posts_result)){
                                $post_title = $post[$POST_TITLE];
                                $numberOfReplies = $post['numberOfReplies'];
                                echo "
                                <tr>

                                <td><strong>$rank.</strong> <i>$post_title</i> : Replies ($numberOfReplies) </td>

                                </tr>
                                ";
                                $rank++;
                            }
                        }else{
                            echo "<h5 class='text-muted text-center'>No Activity <br> Top posts of last 7 days are shown here.</h5>";
                        }
                    }
                    ?>
                </table>
            </div> <!--End of tops post info-->
            </div>
        </div>
    </div>
</div>