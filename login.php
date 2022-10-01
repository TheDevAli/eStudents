<?php
    //get the data posted through form
    if(isset($_POST['login'])){
        //mysql_real_escape_string($_POST['email'])
        $email = strtolower($_POST['email']);  //email is always lowercase
        $pass = $_POST['pass'];

        //check if user already exists with this email
        $email_query = "select * from $USER_TABLE where $USER_EMAIL='$email'";
        $email_result = mysqli_query($con, $email_query);
        if(mysqli_num_rows($email_result))
        {
            $user = mysqli_fetch_assoc($email_result);  //system is designed in such a way that every user email is unique, so always one user in result array
            $hashedPass = $user[$USER_PASS];  //there is always hashed password in db
            $user_id = $user[$USER_ID];
            $status_id = $user[$USER_STATUS_ID];
            $user_name = $user[$USER_NAME];
            $user_email = $user[$USER_EMAIL];
            $profile_img = $user[$PROFILE_IMG];
            if(password_verify($pass, $hashedPass)){
                //set the values of login data in sessions array
                $_SESSION[$LOGIN_EMAIL] = $email;
                $_SESSION[$LOGIN_ID] = $user_id;
                $_SESSION[$LOGIN_STATUS_ID] = $status_id;
                $_SESSION[$LOGIN_NAME] = $user_name;
                $_SESSION[$LOGIN_EMAIL] = $user_email;
                $_SESSION[$LOGIN_PROFILE_IMG] = $profile_img;
                echo "<script>window.open('index.php', '_self');</script>"; //redirect to homepage
            }
            else
                failAlert("Invalid User Credentials - Try Again");
        }else   
            failAlert("User not found!");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="d-flex align-items-center justify-content-center m-5" style="height: 100%">
            <div class="text-white rounded p-5 form-box" >
                <div class="text-center">
                    <i class="fas fa-graduation-cap logo-cap" ></i>
                    <h1>Login</h1>
                </div>
                <form method="post">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" id="email" required>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" name="pass" class="form-control" placeholder="Enter password" id="pass" required>
                    </div>
                    <br>
                    <div class="form-group py-2">
                        <a href="">Forgot password</a>
                    </div>
                    <br>
                    <div class="form-group">
                        <button class="btn btn-primary btn-lg col-12" onclick="" name="login">Login</button>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Not have an account?</label>
                        <a href="<?php echo "?$REGISTER_TYPE_PAGE"?>">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
