<?php
    $sub_forum_id = $_GET[$SUB_FORUM_ID];  //redirects to posts page
    $target_type = $_GET[$TARGET];
    //first we check that if the user submitted the new post, so we upload it to the database
    //then show the result
    if(isset($_POST[$SUBMIT_REPLY])){
        $post_id = $_POST[$SUBMIT_REPLY];  //the publish buttons decides which form to post submit the post in, so we are storing the sub forum id in it while submitting the forum

        //we escape the special characters in strings, which also helps to prevent mysql injections
        $reply_text = mysqli_real_escape_string($con, $_POST[$REPLY_TEXT]);
        $user_id = $_SESSION[$LOGIN_ID]; //retrieve the id of logged in customer from session
        $reply_insert_query = "insert into $REPLY_TABLE ($POST_ID, $USER_ID, $REPLY_TEXT) values ('$post_id', '$user_id', '$reply_text')";
        if($_POST[$RTOR_INPUT]){  //rtor is the hidden input in form, we sets the value on reply button clicked according the id of pressed reply, so this id becomes the parent of the new reply
            $reply_parent_id = $_POST[$RTOR_INPUT]; 
            $reply_insert_query = "insert into $REPLY_TABLE ($POST_ID, $USER_ID, $REPLY_TEXT, $REPLY_PARENT_ID) values ('$post_id', '$user_id', '$reply_text', '$reply_parent_id')";
        }
        $result = mysqli_query($con, $reply_insert_query);
    }else if(isset($_POST[$DELETE_POST])){
        //handle post delete request
        $del_post_id = $_POST[$DELETE_POST];
        deleteWhereDB($con, $POST_TABLE, "where $POST_ID='$del_post_id'");

        //now delete all the replies for this post
        deleteWhereDB($con, $REPLY_TABLE, "where $POST_ID='$del_post_id'");
        redirectTo("$POSTS_PAGE&$SUB_FORUM_ID=$sub_forum_id&$TARGET=$target_type");

    }else if(isset($_POST[$DELETE_REPLY])){
        //handle reply delete request
        $del_reply_id = $_POST[$DELETE_REPLY];
        //deleteWhereDB($con, $REPLY_TABLE, "where $REPLY_ID='$del_reply_id'");
        //delete all  replies  
        delAllChildReplies($del_reply_id);
    }
?>

<div class="rounded bg-light p-3 m-5" >
    <div>
        <form method="post" name="formtype" value="hi" class="popup-form" id="reply_form">
            <h2 class="text-center">
                Reply to post
            </h2>
            <div class="form-group">
                <label for="text"><strong>Enter your reply:</strong></label>
                <textarea name="<?php echo $REPLY_TEXT?>" class="form-control" id="" rows="5" placeholder="Reply..."></textarea>
                <input type="hidden" name="<?php echo $RTOR_INPUT?>" id="<?php echo $RTOR_INPUT?>" value="0">
            </div>
            <div class="form-group">
                <div class="container d-flex pt-4">
                    <button class="col btn btn-primary m-1" type="submit" name="<?php echo $SUBMIT_REPLY?>" value="<?php echo $_GET[$POST_ID]?>">Reply</button>
                    <button class="col btn btn-danger m-1" type="button" onclick="closePopupForm('reply_form')">Cancel</button>
                </div>
            </div>
        </form>

        <?php
        $post_id = $_GET[$POST_ID];
        $posts_query = "select * from $POST_TABLE where $POST_ID='$post_id'";
        $posts_result = mysqli_query($con, $posts_query);

        //check if the posts exists for this forum
        if(!mysqli_num_rows($posts_result)){
            /*
            This condition can happen only if user deleted the post and pressed back button
            so redirect to index page if user do so
            This condition happens only as mentioned above because this page requires a post to click, which redirects us to this page
            As there was no post so no redirection to this page
             */
            redirectTo("");  //index.php
            echo '<h1 class="d-flex justify-content-center text-muted">POST NOT FOUND!</h1>';
        }
        
        $post = mysqli_fetch_assoc($posts_result);
        $post_id = $post[$POST_ID];
        $post_title = $post[$POST_TITLE];
        $post_content = $post[$POST_CONTENT];
        $datetime = $post[$DATE_TIME];
        $user_id = $post[$USER_ID];
        
        //get user data for every post
        $user_query = "select * from $USER_TABLE where $USER_ID='$user_id'";
        $user_result = mysqli_query($con, $user_query);
        $user = mysqli_fetch_assoc($user_result);
        $user_name = $user[$USER_NAME];
        $email = $user[$USER_EMAIL];
        $profile_img = $user[$PROFILE_IMG];
        $pid = explode("@", $email)[0];  //get the username part before '@'
        $login_id = $_SESSION[$LOGIN_ID];

        //check if user has liked this post already
        $like_status = hasUserLiked($login_id, $post_id, 0)? "blue" :  "black";
        $likes_count = countLikes($post_id, 0);
        //start printing the main post
        echo "
            <div class='container border border-primary mt-2'>
                <div class='row bg-primary text-white p-2'>$datetime</div>
                <div class='row'>
                    <div class='col-md-3 text-center bg-gray reply-sidebar'>
                    <h2>$user_name</h2>
                    <h4 class='text-muted'>$pid</h4>
                    <img src=\"src/$profile_img\" onerror=\"this.src='src/user_donut_img.png'\"  class=\"rounded-circle mx-auto d-block img-reponsive\" style='width=100px; height: 100px'  alt=\"logo\">

                    <div class='d-flex mt-4 align-items-center justify-content-center'>
                    <button type='button' class='btn btn-primary' onclick='openPopupForm(\"reply_form\")'>&#9998 Reply</button>
                    <i class='fa fa-thumbs-up like-btn' style='color : $like_status' id='like-btn' onclick='toggleLike(this, \"likes_count_obj0\", \"$post_id\", \"0\")'><sub id='likes_count_obj0' style='font-size : 1rem'>$likes_count</sub></i>
                    </div>
                    <br>
                    ";
                    //let's see, who is owner of this post
                    //and display the delete button for owner only
                    if($user_id == $_SESSION[$LOGIN_ID])
                    echo "
                    <form method='post' action=''>
                        <button type='submit' name='$DELETE_POST' value='$post_id' class='btn btn-danger my-2'>&#10060 Delete Post</button>
                    </form>
                    ";
                echo "</div>
                    <div class='col-md'>
                        <h4 class='d-flex align-items-center'>$post_title</h4>
                        <hr>
                        <p>$post_content</p>
                    ";
                    printAttachmentsTable($post_id);
                    echo "
                    </div>
                </div>
            </div>
        ";  //end of main post

        //start replies if any
        $replies_query = "select * from $REPLY_TABLE where $POST_ID='$post_id' order by $DATE_TIME DESC";
        $replies_result = mysqli_query($con, $replies_query);
        $total_rows = mysqli_num_rows($replies_result);

        //rewrite the above query according to pagination
        $page = 1;  //first time page is zero
        if(isset($_GET[$PAGE_NUM])){
            $page = $_GET[$PAGE_NUM];
        }
        $total_pages = ceil($total_rows/$OBJECTS_PER_PAGE);  //if rows does not completely divide it means some rows are left so create an extra page using ceil 
        $previous_rows = ($page-1) * $OBJECTS_PER_PAGE;

        //we have joined the user table and reply table because we need info from both tables
        $replies_query = "select * from $REPLY_TABLE inner join $USER_TABLE on $REPLY_TABLE.$USER_ID = $USER_TABLE.$USER_ID where $POST_ID='$post_id' order by $DATE_TIME DESC LIMIT $previous_rows,$OBJECTS_PER_PAGE";
        $replies_result = mysqli_query($con, $replies_query);
        

        //check if there posts exists for this forum
        if(!$total_rows){
            echo '<h1 class="d-flex justify-content-center text-muted">No Replies Yet!</h1>';
        }
        $count_replies = 0;
        while($reply = mysqli_fetch_assoc($replies_result)){
            $count_replies++;
            $reply_text = $reply[$REPLY_TEXT];
            $reply_id = $reply[$REPLY_ID];
            $user_id = $reply[$USER_ID];
            $datetime = $reply[$DATE_TIME];
            $reply_parent_id = $reply[$REPLY_PARENT_ID];
            $user_name = $reply[$USER_NAME];
            $profile_img = $reply[$PROFILE_IMG];
            $email = $reply[$USER_EMAIL];
            $pid = explode("@", $email)[0];  //get the username part before '@'

            //check if user has liked this post already
            $like_status = hasUserLiked($login_id, $post_id, $reply_id)? "blue" :  "black";
            $likes_count = countLikes($post_id, $reply_id);
            echo "
            <div class='container border border-primary mt-4'>
                <div class='row bg-primary text-white p-2'>$datetime</div>
                <div class='row' class='reply-sidebar'>
                    <div class='col-md-3 text-center bg-gray reply-sidebar'>
                    <h2>$user_name</h2>
                    <h4 class='text-muted'>$pid</h4>
                    <img src=\"src/$profile_img\" onerror=\"this.src='src/user_donut_img.png'\"  class=\"rounded-circle mx-auto d-block img-reponsive\" style='width=100px; height: 100px'  alt=\"logo\">
                    <div class='d-flex mt-4 align-items-center justify-content-center'> <!--This div keeps the button and like button on the same line-->
                        <button type='button' class='btn btn-primary' onclick='openReplyPopupForm(\"reply_form\", \"$RTOR_INPUT\", \"$reply_id\")'>&#9998 Reply</button>
                        <i class='fa fa-thumbs-up like-btn' style='color : $like_status' id='like-btn' onclick='toggleLike(this, \"likes_count_obj$count_replies\", \"$post_id\", \"$reply_id\",)'><sub id='likes_count_obj$count_replies' style='font-size : 1rem'>$likes_count</sub></i>
                    </div>
                    <br>
                    ";
                    if($user_id == $_SESSION[$LOGIN_ID])
                    echo "
                        <form method='post' action=''>
                            <button type='submit' name='$DELETE_REPLY' value='$reply_id' class='btn btn-danger my-2'>&#10060 Delete Reply</button>
                        </form>
                    ";
                //resume the content of reply div
                //start echo post title
                echo "
                    </div>
                    <div class='col-md'>
                        <h4 class='d-flex align-items-center'>RE: $post_title</h4>
                        <hr>
                ";  //end echo post title
                    if($reply_parent_id){  //if parent is not 0 that means, this reply has some parent reply, so print the text of parent
                        //we have joined the user table and reply table because we need info from both tables
                        $query = "select * from $REPLY_TABLE inner join $USER_TABLE on $REPLY_TABLE.$USER_ID = $USER_TABLE.$USER_ID where  $REPLY_ID='$reply_parent_id'";
                        $result = mysqli_query($con, $query);
                        $parent = mysqli_fetch_assoc($result);
                        $parent_text = $parent[$REPLY_TEXT];
                        $parent_user_id = $parent[$USER_ID];
                        $parent_user_name = $parent[$USER_NAME];
                        $parent_email = $parent[$USER_EMAIL];
                        $parent_pid = explode("@", $parent_email)[0];  //get the username part before '@'
                        echo "
                        <div class='flex align-items-center rounded bg-gray p-1 border-start border-primary border-3'>
                            <h5>Originally Posted By: $parent_user_name ($parent_pid)</h5>
                            <p>
                                <font size='+2'>&#128630</font>
                                    $parent_text
                                <font size='+2'>&#128632</font>
                            </p>
                        </div>
                        ";
                    }
                //start of reply text
                echo "
                        <hr>
                        <p>$reply_text</p>
                    </div>
                </div>
            </div>
            "; //end of whole div of single reply
        }
        ?>
    </div>
    
    <ul class="pagination py-2 justify-content-end">
        <li class="page-item disabled">
        <a class="page-link">Pages</a>
        </li>
    <?php
        for($i = 1; $i <= $total_pages; $i++)
        {
            $isactive = "";
            if($page == $i)
                $isactive = 'active';  //active is a bootstrap class which shows the index of current page
            echo "<li class='page-item $isactive'>
            <a class='page-link' href='?$REPLIES_PAGE&$SUB_FORUM_ID=$sub_forum_id&$POST_ID=$post_id&$TARGET=$target_type&$PAGE_NUM=$i'>$i</a>
            </li>";
        }
    ?>
    </ul>
</div>

<script type="text/javascript">
    function toggleLike(like_btn, likes_count_obj, postID, replyID){
        //using ajax to like a post so that page is not reloaded after every like
        //if user is liking post, only, then reply id is 0
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", `handle_like.php?postID=${postID}&replyID=${replyID}`, true);
        xmlhttp.send();  

        //update front end
        var like_color =like_btn.style.color;
        var likes_count_obj = document.getElementById(likes_count_obj);
        var like_count = likes_count_obj.innerHTML ;

        /*
        like some other popular websites (such as facebook) 
        we are updating like info on the front end separately and send the request on backend too
        if due to some reason post is not liked, user can like again
         */
        if(like_color == "black"){
            like_btn.style.color = "blue";
            likes_count_obj.innerHTML = parseInt(like_count)+1;
        }else{
            like_btn.style.color = "black";
            likes_count_obj.innerHTML = parseInt(like_count)-1;
        }
    }
</script>