<?php
    session_start();
    include("required/db.php");
    include("required/constants.php");
    include("required/functions.php");
?>
<html>
    <head>
        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>eStudents</title>

        <!--Bootstrap CSS-->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

        <!--Site CSS custom-->
        <link rel="stylesheet" href="custom/css/site.css">

        <!--fonts-->
        <link rel="stylesheet" href="font_awesome/css/all.css">
    </head>
    <body>
        <?php
            if(!isset($_SESSION[$LOGIN_EMAIL])){
                /*
                if user not logged in, they can access the following pages only
                First we check which page is requested using if conditions
                then include that page using php file
                 */
                if(isset($_GET[$LOGIN_PAGE])){
                    include("$LOGIN_PAGE.php");
                }else if(isset($_GET[$REGISTER_PAGE])){
                    include("$REGISTER_PAGE.php");
                }else if(isset($_GET[$REGISTER_TYPE_PAGE])){
                    include("$REGISTER_TYPE_PAGE.php");
                }else{
                    include("$ENTER_PAGE.php"); //by default goto to entrance page
                }
            }//login check ends
            else{
                /*HEADER */
                include("$HEADER.php"); //header is always required for logged in session

                if(isset($_GET[$FORUMS_PAGE])){
                    include("$FORUMS_PAGE.php");
                }
                else if(isset($_GET[$POSTS_PAGE])){
                    include("$POSTS_PAGE.php");
                }
                else if(isset($_GET[$REPLIES_PAGE])){
                    include("$REPLIES_PAGE.php");
                }
                else if(isset($_GET[$E_CHAT_PAGE])){
                    include("$E_CHAT_PAGE.php");
                }
                else if(isset($_GET[$PROFILE_PAGE])){
                    include("$PROFILE_PAGE.php");
                }
                else if(isset($_GET[$TUTOR_PORTAL_PAGE])){
                    include("$TUTOR_PORTAL_PAGE.php");
                }
                else
                    include("$HOMEPAGE.php");  //By default goto homepage
            }
        ?>

        <!-- JS FILES-->
        <script src="custom/js/site.js"></script>
        <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- UIkit JS -->
        <script src="https://cdn.jsdelivr.net/npm/uikit@3.9.4/dist/js/uikit.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/uikit@3.9.4/dist/js/uikit-icons.min.js"></script>

        <!--FG emoji picker script-->
        <script src="vanilla-js-emoji-picker/vanillaEmojiPicker.js"></script>

        <!-- TinyMCE HTML Editor script -->
        <script src="https://cdn.tiny.cloud/1/d6s72oioc2g1cucnl4jko3pgcvynubytdanld84g4e4n2ya4/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> 

    <script>
        new EmojiPicker({
            trigger: [
                {
                    selector: '.emoji-btn',
                    insertInto: '.msg-input'
                }
            ],
            position: ['bottom', 'left'],
            closeButton: true,
            //specialButtons: green
        });

        
        tinymce.init({
            selector: '#postContentArea',
            
            plugins: [
            'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
            'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
            , 'table', 'template', 'help'
            ],
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help',
            menu: {
            favs: { title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons' }
            },
            menubar: 'favs file edit view insert format tools table help'
        }); 
    </script>

    </body>
</html>