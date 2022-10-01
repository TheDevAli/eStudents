<?php
    $login_id = $_SESSION[$LOGIN_ID];
    $login_type = $_SESSION[$LOGIN_STATUS_ID] == 1? "Student" : "Tutor";  //is this profile of tutor or student
    $login_name = $_SESSION[$LOGIN_NAME];
    $login_email = $_SESSION[$LOGIN_EMAIL];
    $pid = explode("@", $login_email)[0]; //first part before @ is always pid

    $courses = array();

    $count_posts = countEntriesDB($con, $POST_TABLE, "where $USER_ID='$login_id'");
    $count_replies = countEntriesDB($con, $REPLY_TABLE, "where $USER_ID='$login_id'");
    $count_likes = countEntriesDB($con, $LIKE_TABLE, "where $USER_ID='$login_id'");

    if(isset($_POST['change_password'])){

        $oldpass = $_POST['old_pass']; //hash the password

        $email_query = "select * from $USER_TABLE where $USER_ID='$login_id'";
        $email_result = mysqli_query($con, $email_query);
        
        $user = mysqli_fetch_assoc($email_result);  //system is designed in such a way that every user email is unique, so always one user in result array
        $hashedPass = $user[$USER_PASS];  //there is always hashed password in db
        if(password_verify($oldpass, $hashedPass)){
            //if old pass is verified, update the new password
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);  //hash the new password
            $update_query = "update $USER_TABLE set $USER_PASS = '$pass'";
            mysqli_query($con, $update_query);
            successAlert("Password changed successfully!");
        }else{
            failAlert("Incorrect Password");
        }
    }
    else if(isset($_POST['abc'])){
        $new_img = $_FILES['profile_img']['name'];  // getting profile img name from
        if(!empty($new_img)){  //if img is selected by user then it must not be empty, so upload it
            $new_img = getUploadProfileImg();  //upload the new image and gets its unique name
            $update_query = "update $USER_TABLE set $PROFILE_IMG ='$new_img' WHERE $USER_ID='$login_id'";
            mysqli_query($con, $update_query);
        }
    }

    //get the profile image of logged in user
    $query = "select $PROFILE_IMG from $USER_TABLE where $USER_ID='$login_id'";
    $result = mysqli_query($con, $query);
    $profile_img = mysqli_fetch_assoc($result)[$PROFILE_IMG];
?>
<div class="rounded p-3 m-5">
    <div class="row bg-light rounded">
        <div class="col-4 p-2 bg-light rounded gray-separator">
            <img src="src/<?php echo $profile_img?>" onerror="this.src='src/user_donut_img.png'"  class="rounded-circle border d-block mx-auto" style="width: 5em;" alt="logo">
            <h1 class="text-center"><?php echo $pid?></h1>
            <hr>
            <table>
                <tr>
                    <td><strong>Name: </strong><?php echo $login_name?></td>
                </tr>
                <tr>
                    <td><strong>Email: </strong><?php echo $login_email?></td>
                </tr>
                <tr>
                    <td><strong>User Type: </strong><?php echo $login_type?></td>
                </tr>
            </table>
            <form action="" method="post" enctype="multipart/form-data">
                <br>
                <div class="form-group bg-primary text-white my-1 p-1">
                    <b>Update Profile</b>
                </div>
                <b>Change Profile Picture</b>
                <div class="form-group">
                <input  type="file" accept="image/*" class="form-control" id="profile_img" name="profile_img">
                </div>
                <button type="submit" name="abc" class="btn btn-primary my-1 form-control">Upload</button>
            </form>
            <!---password form below-->
            <form action="" method="post">
                <b>Change Password</b>
                <div class="form-group">
                    <input type="password" class="form-control my-1" placeholder="Old Password" id="old_pass" name='old_pass' required>
                    <input type="password" class="form-control my-1" placeholder="New Password" id="pass" name='pass' oninput="validatePassword('pass')" required>
                    <input type="password" class="form-control my-1" placeholder="Confirm New Password" id="pass2" oninput="confirmPassword('pass', 'pass2')" required>
                    <input type="hidden" name="change_password">
                </div>
                <button type="submit" class="btn btn-primary form-control">Confirm</button>
            </form>
        </div>
        <div class="col-8 bg-light rounded">
            <div>
                <div class="container bg-primary text-white my-1 p-1" id="userStatisticsHeader">
                    <h4>User Statistics</h4>
                </div>
                <table class="table">
                    <tr>
                        <td><strong>Posts: </strong><?php echo $count_posts?></td>
                    </tr>
                    <tr>
                        <td><strong>Replies: </strong><?php echo $count_replies?></td>
                    </tr>
                    <tr>
                        <td><strong>Likes: </strong><?php echo $count_likes?></td>
                    </tr>
                </table>
            </div>

            <div>  <!--Start of course info-->
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
                    //there may be more than one courses if this is a tutor 
                    if(mysqli_num_rows($user_course_result)){
                        while($user_course = mysqli_fetch_assoc($user_course_result)){
                            array_push($courses, $user_course_id);
                            $user_course_id = $user_course[$COURSE_ID];
                            $course_title = $user_course[$COURSE_TITLE];
                            echo "
                            <tr>
                                <td><b>Course: </b> $course_title</td>
                            </tr>
                            ";
                            $course_count++;
                        }
                    }
                    ?>
                </table>
            </div>  <!--End of course info-->                
        </div>
    </div>
</div>