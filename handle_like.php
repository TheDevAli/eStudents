<?php
    //this page is handled using ajax;
    session_start();
    include("required/db.php");
    include("required/constants.php");

        $login_id = $_SESSION[$LOGIN_ID];
        $post_id = $_GET[$POST_ID];
        $reply_id = $_GET[$REPLY_ID];
        //first we check if it this post or reply is liked or not!

        $query = "select * from $LIKE_TABLE where $USER_ID='$login_id' and $POST_ID='$post_id' and $REPLY_ID='$reply_id'";
        $result = mysqli_query($con, $query);
        if(mysqli_num_rows($result)){  
            $like_id = mysqli_fetch_assoc($result)[$LIKE_ID];
            //if there is any row, then like already exists, so lets delete this like
            $query = "delete from $LIKE_TABLE where $LIKE_ID='$like_id'";
            $result = mysqli_query($con, $query);
        }else{
            // if rows result is zero, like does not exits already,
            //lets insert like for the is user
            $query = "insert into $LIKE_TABLE ($USER_ID, $POST_ID, $REPLY_ID) values ('$login_id', '$post_id', '$reply_id')";
            $result = mysqli_query($con, $query);
        }
        echo 'here is the result'. $result;
?>