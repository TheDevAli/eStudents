<?php
    $con = mysqli_connect("localhost", "root", "", "estudents");
    if($con->connect_error){
        die("Database Connection Failed!". $con->connect_error);
    }

    /*
        db constants
        such as names of columns
        we are using simple php vaiables as constants
        because they can be easily used in php strings which makes the query making easy
        while real php constants can't be used directly
     */

     //user table starts
    $USER_TABLE = "user";
    $USER_ID = "userID";
    $USER_NAME = "name";
    $USER_EMAIL = "email";
    $USER_PASS = "password";
    $USER_STATUS_ID = "statusID";
    $USER_COURSE_ID = "courseID";
    $PROFILE_IMG = "profile_img";
    //user table ends

    //course table starts
    $COURSE_TABLE = "course";
    $COURSE_TITLE = "course_title";
    $COURSE_ID = "courseID";
    //course table ends

    //courses that belong a user starts
    $UC_TABLE = "users_courses";
    //$USER_ID = "userID"; already declared
    //$COURSE_ID = "courseID";  //ALREADY DECLARED



    //forums table starts
    $FORUM_TABLE = "forum";
    $FORUM_ID = "forumID";
    $FORUM_TITLE = "forum_title";
    $FORUM_DESC = "forum_desc";
    $FORUM_TARGET_TYPE = "target_type";  //who is the main target for the this form 1 for student and 2 for tutor
    //forums table ends


    //sub forums table starts
    $SUB_FORUM_TABLE = "sub_forum";
    $SUB_FORUM_ID = "sub_forumID";
    //$FORUM_ID = "forumID"; this is the key to forum table which is already declared above
    $SUB_FORUM_TITLE = "sub_forum_title";
    $SUB_FORUM_DESC = "sub_forum_desc";

    //sub forums table ends


    //posts start
    $POST_TABLE = "post";
    $POST_ID = "postID";
    //$SUB_FORUM_TITLE = "sub_forum_title"; for referencing to subforum id
    $POST_TITLE = "post_title";
    $POST_CONTENT = "post_content";
    //posts end

    //posts start
    $REPLY_TABLE = "replies";
    $REPLY_ID = "replyID";
    //$POST_ID = "postID";  //ALREADY declared
    //$SUB_FORUM_TITLE = "sub_forum_title"; for referencing to subforum id
    // $USER_ID = "userID";  //ALREADY declared
    $REPLY_TEXT = "reply_text";
    $REPLY_PARENT_ID = "parentID";  //stores the reference to parent reply
    //posts end

    //start messages
    $CHAT_TABLE = "chat";
    $CHAT_ID = "chatID";
    $RECEIVER_ID = "receiverID";
    $SENDER_ID = "senderID";
    //end messages

    //start messages
    $MSG_TABLE = "message";
    $MSG_ID = "msgID";
    $MSG_TEXT = "msg_text";
    //end messages

    //start messages
    $LIKE_TABLE = "likes";
    $LIKE_ID = "likeID";
    //$USER_ID = "userID";
    //$POST_ID = "postID";
    //end messages

    //timetamp
    $DATE_TIME = "datetime";

    //start uploads table
    $UPLOADS_TABLE = "uploads";
    $UPLOAD_ID = "uploadID";
    $FILENAME = "filename";  //this filename is original filename to show to user
    $FILENAME_UNIQUE = "filename_unq";  //this filename is unique, because many users may upload files of same name

    //$POST_ID = "postID";
    //end uploads table
?>