<?php
    session_start();
    session_destroy();
    //redirect to index
    echo "<script>window.open('index.php','_self')</script>";
?>