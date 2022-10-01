<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="d-flex align-items-center justify-content-center" style="height: 100%">
            <div class="text-center text-white rounded p-5 form-box">
                <?php
                if(isset($_GET[$REGISTERATION_SUCCESS])){
                    successAlert("Successfully registered, login to access eStudents");
                }
                ?>
                <i class="fas fa-graduation-cap logo-cap"></i>
                <h1>eStudents</h1>
                <p>A hybrid social & learning interaction platform for students</p>
                <a href="<?php echo "?$LOGIN_PAGE"?>"><button class="btn btn-primary btn-lg btn-big">Login</button></a>
                <a href="<?php echo "?$REGISTER_TYPE_PAGE"?>"><button class="btn btn-primary btn-lg btn-big">Register</button></a>
            </div>
        </div>      
    </body>
</html>
