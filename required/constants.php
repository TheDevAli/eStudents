<?php
    /*
        db constants
        such as names of columns
        we are using simple php vaiables as constants
        because they can be easily used in php strings which makes the query making easy
        while real php constants can't be used directly
     */
    //these constants keep the everything in consistent througout the site
    //sessions need to be started after login
    $LOGIN_EMAIL = "login_email"; //passed in array key of session to check if loginSession is Set
    $LOGIN_ID = "login_id";  //required to pass as reference id while creating new posts etc
    $LOGIN_STATUS_ID = "login_status_id"; 
    $LOGIN_NAME = "loign_name";
    $LOGIN_PROFILE_IMG = "login_profile_img";

    //pages start
    $ENTER_PAGE = "enter";
    $LOGIN_PAGE = "login";
    $REGISTER_PAGE = "register";
    $REGISTER_TYPE_PAGE = "register_type";


    $HOMEPAGE = "homepage";
    $LOGOUT = "logout";
    $HEADER = "header";
    $FOOTER = "footer";

    $POSTS_PAGE = "posts";
    $FORUMS_PAGE = "forums";
    $REPLIES_PAGE = "replies";
    $E_CHAT_PAGE = "echat";
    $PROFILE_PAGE = "profile";
    $TUTOR_PORTAL_PAGE = "tutor_portal";

    $LOGIN_CHECK = "login_check";
    //pages end


    //predefined gets
    $REGISTERATION_SUCCESS = "registeration_success";
    $TARGET = "target_type";
    $SUBMIT_POST = "submit_post";
    $SUBMIT_REPLY = "submit_reply";
    $RTOR_INPUT = "rtor_input";  // stores id to reply to reply
    $SUBMIT_FORUM = "submit_forum";  //FOR TUTUOR ONLY

    $DELETE_REPLY = "delete_reply";
    $DELETE_POST = "delete_post";

    $PAGE_NUM = "page_number";
    $NEW_CHAT = "new_chat";
    $CHAT_WITH = "chat_with";

    //constant values
    $STUDENT = 1;
    $TUTOR = 2;

    $OBJECTS_PER_PAGE = 10;  //FOR PAGINATION

    $UPLOADS_DIR = "uploads"; //where to upload files for user posts
    
?>