<?php
    //query for coures
    $query = "select * from $COURSE_TABLE";
    $allcourses = mysqli_query($con, $query);

    $username_format = "username@my365.dmu.ac.uk";  //default format for student emails
    if($_GET[$TARGET] == $TUTOR){
        $username_format = "username@dmu.ac.uk"; //default format for tutor emails
    }
    //get the data posted through form
    if(isset($_POST['register'])){
        //mysql_real_escape_string($_POST['email'])
        $name = $_POST['name'];
        $email = strtolower($_POST['email']);  //email is always lowercase
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);  //hash the password
        $course_title = $_POST['course_title'];
        //echo "course title is : $course_title";

        //check if user already exists with this email
        $email_query = "select * from $USER_TABLE where $USER_EMAIL='$email'";
        $email_result = mysqli_query($con, $email_query);
        if(mysqli_num_rows($email_result))
        {
            failAlert("User Already Exists!");
        }else{
            $target_type = $_GET[$TARGET];  //who submitted the form, so handle form according to it
            $username = explode("@", $email)[0];  //get the username part before '@'
            $pattern = "/^p[\d]+$/";  //regex to match p1234..@dmu.ac.uk type email for student only
            $is_ok = 1; // a flag to determine if there was an error with the email according to the user type

            //lets validate the email for both users
            /*
                don't allow a student to choose tutors type email
                and tutor to choose student type email
                This is checked in following two conditions 
                if both conditions become false then
                is_OK becomes false so user is not added to database and a message is printed
                to choose the email according to their user type
             */
            if($target_type == $STUDENT && preg_match($pattern, $username))
                $status_ID = 1;  //student status id
            else if($target_type == $TUTOR && (!preg_match($pattern, $username)))
                $status_ID = 2;  //tutor status id
            else 
            {
                $is_ok = 0; //so there is an error with email validation
                failAlert("Please choose email according to the user type!");
            }

            if($is_ok){ //only create new user if there was no error with email validation
                //create new user
                $query = "insert into $USER_TABLE ($USER_NAME, $USER_EMAIL, $USER_PASS, $USER_STATUS_ID) values ('$name', '$email', '$pass', '$status_ID')";
                $registration_result = mysqli_query($con, $query);
                $user_id = mysqli_insert_id($con);
                //add course data for this user
                foreach($course_title as $ct){
                    //as we passed array for course title because tutor can select more than one coureses
                    //so add all the selected courses to same id in user's courses table
                    $course_id = getIDWhere($allcourses, $COURSE_TITLE, $ct);
                    $query = "insert into $UC_TABLE ($USER_ID, $COURSE_ID) values ('$user_id', '$course_id')";
                    $registration_result = mysqli_query($con, $query);
                }

                if($registration_result)  //if registeration successful, redirect to index page and pass message for success of registeration
                    redirectTo("/&$REGISTERATION_SUCCESS");
            }
        }
    }
?>

<div class="d-flex align-items-center justify-content-center m-5">
    <div class="text-white rounded p-5 form-box">
        <div class="text-center">
            <i class="fas fa-graduation-cap logo-cap" ></i>
            <h1>Sign up</h1>
        </div>
        <form method="post" oninput="validateCheckboxes()">
            <div class="form-group">
                <label for="text">Name:</label>
                <input type="name"  class="form-control" placeholder="Enter name" name="name" id='name' oninput="validateName('name')" required>
            </div>
            <br>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" placeholder="<?php echo $username_format?>" name="email" id="email" oninput="validateEmail('email', <?php echo $_GET[$TARGET]?>)" required>
            </div>
            <br>
            <div class="form-group">
                <label for="pass">Password:</label>
                <input type="password" name="pass" id="pass" class="form-control" placeholder="Enter password" oninput="validatePassword('pass')" required
                title="Password must be at least 8 characters, must include at least one number, must include at least one special character.">
            </div>
            <br>
            <div class="form-group">
                <label for="pass2">Confirm Password:</label>
                <input type="password" name="pass2" id="pass2" class="form-control" placeholder="Enter password again" oninput="confirmPassword('pass', 'pass2')" required>
            </div>
            <br>
            <div class="form-group ">
                <label>Select Course: </label>
                <?php
                    $target_type = $_GET[$TARGET];
                    if($target_type == $STUDENT)
                    {
                        echo "<select class='form-control form-select' name='course_title[]' required>";
                        while($course = mysqli_fetch_assoc($allcourses)){
                            $course_title = $course[$COURSE_TITLE];
                            echo "<option>$course_title</option>";
                        }
                        echo "</select>";  //end selector
                    }else{
                        $num = mysqli_num_rows($allcourses);
                        //create an array of checkboxes ids to pass as array for validation
                        echo "<script>let checkboxes = new Array($num)</script>";
                        $count = 0;
                        while($course = mysqli_fetch_assoc($allcourses)){
                            $course_title = $course[$COURSE_TITLE];
                            echo "
                            <div class='form-group'>
                                <input type='checkbox' id='checkbox$count' name='course_title[$count]' value='$course_title'>
                                <label>$course_title</lable>
                            </div>
                            <script>checkboxes[$count] = 'checkbox$count'; </script>
                            ";
                            $count++;
                        }
                        echo "<script>
                            function validateCheckboxes(){
                                var checkeda = false;
                                var checkbox;
                                for(let i = 0; i < checkboxes.length; i++){
                                    checkbox = document.getElementById(checkboxes[i]);
                                    if(checkbox.checked)
                                        checkeda = true;
                                }
                                checkbox.setCustomValidity(!checkeda? \"Please select the courses you teach\": \"\");
                            }
                            </script>";
                    }
                ?>
            </div>
            <br>
            <div class="form-group py-2">
                <input type="checkbox" id="agree_terms" required>
                <a href=""><label>I agree to terms and conditions </label></a>
            </div>
            <br>
            <button class="btn btn-primary btn-lg col-12" type="submit"  name="register">Sign up</button>
            <br>
            <br>
            <div class="form-group">
                <label>Already have an account?</label>
                <a href="<?php echo "?$LOGIN_PAGE"?>">Login</a>
            </div>
        </form>
    </div>
</div>