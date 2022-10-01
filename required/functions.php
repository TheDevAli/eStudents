<?php
    
    function getIDWhere($result, $col, $value){
        //this is a generic function which gets the id of the any row WHERE matching the passed value in the passed column
        mysqli_data_seek($result, 0);
        $id = 1;
        while($row = mysqli_fetch_assoc($result)){
            if($row[$col] == $value){
                mysqli_data_seek($result, 0);
                return $id;
            }
            $id++;
        }
        mysqli_data_seek($result, 0);
        return 0;  //if does not match anywhere in the column
    }    

    function successAlert($msg){
        echo "<div class='alert alert-success' role='alert'>$msg </div>";
    }

    function failAlert($msg){
        echo "<div class='alert alert-danger m-5' role='alert'>$msg </div>";
    }

    function redirectTo($page){
        echo "<script>window.open('?$page', '_self')</script>"; 
        //use js to redirect the user to any page
    }

    function get_target_type_name($typeINT){
        if($typeINT == 1)
            return "Student";
        else if($typeINT == 2)
            return "Tutor";
        else   
            return "unknown";
    }

    function countEntriesDB($con, $table, $where){
        //counts the rows from passed table using where clause passed in method
        $query = "select * from $table $where";
        $result = mysqli_query($con, $query);
        return mysqli_num_rows($result);
    }

    function deleteWhereDB($con, $table, $where){
        //one line delete function
        $query = "delete from $table $where";
        $result = mysqli_query($con, $query);
        return $result;
    }

    function userHasCourse($con, $user_id, $course_id){
        //checks if the passed user has the passed course
        $query = "select * form users_courses where userID='$user_ID' and courseID='$course_id'";
        $result = mysqli_query($con, $query);
        return mysqli_num_rows($result);
    }

    function getUsernameByID($con, $user_id){
        include("db.php");
        //get the user info such as username
        $query = "select * from $USER_TABLE where $USER_ID='$user_id'";
        $result = mysqli_query($con, $query);
        $email = "unknown@dmu.ac.uk";  //if user not found
        if(mysqli_num_rows($result)){
            $email = mysqli_fetch_assoc($result)[$USER_EMAIL];
        }
        $username = explode("@", $email)[0];
        return $username;
    }

    function getRecByIdForLoginUser($con, $login_id, $chat_sender_id, $chat_receiver_id){
        /*we have a common chat table for both sender and receiver
        Now lets see then who is receiver for the current logged in session
        If the logged in user id equals the sender id then the receiver is actual receiver in the chat table
        else if the logged in user id equals the receiver id then the sender is the actual receiver of the chat for the current logged in session
        So, as a result the opposite users are displayed in side bar of chat, which is receiver
        */
        $receiver_id = ($chat_sender_id == $login_id) ? $chat_receiver_id : $chat_receiver_id;
        $query = "select * from $USER_TABLE where $USER_ID='$receiver_id'";
        $receiver_result = mysqli_query($con, $query);

        return mysqli_fetch_assoc($receiver_result);
    }

    function countLikes($post_id, $reply_id){
        include("db.php");
        $query = "select * from $LIKE_TABLE where $POST_ID='$post_id' and $REPLY_ID='$reply_id'";
        $result = mysqli_query($con, $query);
        return mysqli_num_rows($result);
    }

    function hasUserLiked($login_id, $post_id, $reply_id){
        include("db.php");
        $query = "select * from $LIKE_TABLE where $USER_ID='$login_id' and $POST_ID='$post_id' and $REPLY_ID='$reply_id'";
        $result = mysqli_query($con, $query);
        return mysqli_num_rows($result);
    }

    function showOtherUsersForCourse($user_course_id, $user_type){
        include("db.php");
        $courseName = $user_course_id == 1? "Computer Science" : "Cyber Security";
        echo '
        <div class="col">
            <div class="border rounded border-primary" style="overflow: scroll;">  <!--Start of Tutors Info info-->
                <div class="container bg-info text-white my-1 p-1">
                    <h5>'.$courseName.'</h5>
                </div>
                <table class="table">';
                    
                    //getting the list of users who have this course
                        $tutor_course_query = "select * from $UC_TABLE where $COURSE_ID='$user_course_id'";
                        $tutor_course_result = mysqli_query($con, $tutor_course_query);
                        if(mysqli_num_rows($tutor_course_result)){
                            //there are tutors who are teaching this course
                            while($tutor_course = mysqli_fetch_assoc($tutor_course_result)){
                                //now selecting users(tutors only because we have common table for users courses for both) from user table to get tutors info
                                $user_id = $tutor_course[$USER_ID];
                                $user_query = "select * from $USER_TABLE where $USER_ID='$user_id' and $USER_STATUS_ID='$user_type'";
                                $user_result = mysqli_query($con, $user_query);
                                if(mysqli_num_rows($user_result)){
                                    $user = mysqli_fetch_assoc($user_result);
                                    $user_name = $user[$USER_NAME];
                                    $user_email = $user[$USER_EMAIL];
                                    echo "
                                    <tr>
                                        <td>$user_name</td>
                                        <td>:</td>
                                        <td><a href='http://www.outlook.com'>$user_email</a></td>
                                    </tr>
                                    ";
                                }
                            }
                        }
                echo '
                </table>
            </div>  <!--End of user\'s info-->
        </div>';
    }

    function getUploadProfileImg(){
        /*This function gets the profile image name which was entered in the registeration form
        Then upload it to the server's directory
        rename it to the unique name
        and at last returns the unique name to upload it to the database */
        //upload the image profile image
        //prepare images
        $profile_img = $_FILES['profile_img']['name'];

        //get absolute path of the images
        $temp_name = $_FILES['profile_img']['tmp_name'];

        //upload the images
        move_uploaded_file($temp_name, "src/$profile_img");

        //rename the image files name to make it unique
        //because different files can have same names

        $time = round(microtime(true) * 1000); 
        /*time is always unique 
        because it gets changed every millisecond*/

        $unq_profile_img = "$time-$profile_img";
        rename("src/$profile_img", "src/$unq_profile_img");
        return $unq_profile_img;
    }

    function printAttachmentsTable($post_id){
        include("db.php");
        include("constants.php");
        echo "
        <div class='container bg-info text-white p-1' id='attachment_heading'>
            <h6>Attachments</h6>
        </div>
        <table class='table table-hover table-sm'>
            <tbody>
        ";
        
        //get posts data
        //Join the file table with user table to get info of both
        $uploads_query = "select * from $UPLOADS_TABLE where $POST_ID='$post_id'";
        $uploads_result = mysqli_query($con, $uploads_query);
        $count = 0;
        // print the rows of posts
        while($file = mysqli_fetch_assoc($uploads_result)){
            $count++;
            $filename = $file[$FILENAME];
            $filename_unq = $file[$FILENAME_UNIQUE];
            echo"
                <tr>
                    <td scope='row' style='font-size: 20px;'>
                        <div class='d-flex my-0'>
                            <span class='fas fa-file' style='margin: auto;'></span>
                        </div>
                    </td>
                    <td>
                        <p><a href='$UPLOADS_DIR/$filename_unq' download>$filename</a></p>
                    </td>
                </tr>
            ";
        }
    
        echo "
            </tbody>
        </table>
        <script>
            if($count == 0){
                //if no files attached, remove the attachment heading from page
                document.getElementById('attachment_heading').style.display = 'none';
            }
        </script>
        ";
    }

    //a recursive funtion to delete all child replies
    function delAllChildReplies($del_reply_id){
        include("db.php");
        //select a reply whose parent is this reply that is going to be deleted
        $query = "select * from $REPLY_TABLE where $REPLY_PARENT_ID='$del_reply_id'";
        $result = mysqli_query($con, $query);
        // if(!mysqli_num_rows($result))  //if there is no child of this reply
        //     return; //then break the loop
        while($reply = mysqli_fetch_assoc($result)){
            $next_reply_to_del = $reply[$REPLY_ID];
            delAllChildReplies($next_reply_to_del);  //first delete child replies for next reply and so on
        }
        deleteWhereDB($con, $REPLY_TABLE, "where $REPLY_ID='$del_reply_id'");  //then delete the requested reply
    }
    
?>