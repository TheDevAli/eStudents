<?php
    $user_id = $_SESSION[$LOGIN_ID];
    $main_forums = NULL;//initialization
    $main_forums_query = "select * from $FORUM_TABLE where $TARGET='$TUTOR'";
    $main_forums = mysqli_query($con, $main_forums_query);

    $courses_query = "select * from $UC_TABLE where $USER_ID='$user_id'";
    $courses_result = mysqli_query($con, $courses_query);
    $courses = []; //an array of course ids
    while($course = mysqli_fetch_assoc($courses_result)){
        $courses[] = $course[$COURSE_ID];  //PUSH IDS TO COURSES ARRAY
    }

    //filter courses (for tutors) 
    $allcourses = NULL;
    $allcourses_query = "select * from $COURSE_TABLE";
    $allcourses = mysqli_query($con, $allcourses_query);

    if(isset($_POST[$SUBMIT_FORUM])){
        $forum_title = mysqli_real_escape_string($con, $_POST[$FORUM_TITLE]);
        $all_forums = mysqli_query($con, "select * from $FORUM_TABLE");
        $forum_id = getIDWhere($all_forums, $FORUM_TITLE, $forum_title);
        $sub_forum_title = mysqli_real_escape_string($con, $_POST[$SUB_FORUM_TITLE]);
        $sub_forum_desc = mysqli_real_escape_string($con, $_POST[$SUB_FORUM_DESC]);

        $course_title = $_POST[$COURSE_TITLE]; //get the course title to which this form relates
        $sub_forum_course_id = getIDWhere($allcourses, $COURSE_TITLE, $course_title);
        $insert_forum_query = "insert into $SUB_FORUM_TABLE ($SUB_FORUM_TITLE, $SUB_FORUM_DESC, $FORUM_ID, $COURSE_ID) values ('$sub_forum_title', '$sub_forum_desc', '$forum_id', '$sub_forum_course_id') ";
        mysqli_query($con, $insert_forum_query);
    }
?>
<div class="d-flex container mb-5">
    <div> <!--This form is visible only if the user clicks on new button-->
        <form method="post" class="popup-form" id="new_sub_forum_form">
            <h2 class="text-center">
                Create Forum
            </h2>
            <div class="form-group">
                <label for="text"><strong>Select Main Forum</strong></label>
                <select class="form-control form-select" name="<?php echo $FORUM_TITLE?>" required>
                    <?php  //get the main forums from db such as year1, year2,...
                        while($main_forum = mysqli_fetch_assoc($main_forums)){
                            $forum_title = $main_forum[$FORUM_TITLE];
                            echo "<option>$forum_title</option>";
                        }
                    ?>
                </select>
            </div>
            <br>
            <div class="form-group">
                <label for="text"><strong>Select Course to post in</strong></label>
                <select class="form-control form-select" name="<?php echo $COURSE_TITLE?>" required>
                    <?php 
                        while($course = mysqli_fetch_assoc($allcourses)){
                            $course_title = $course[$COURSE_TITLE];
                            $course_id = $course[$COURSE_ID];
                            if(in_array($course_id, $courses))
                                echo "<option>$course_title</option>";
                        }
                    ?>
                </select>
            </div>
            <br>
            <div class="form-group">
                <label for="text"><strong>Forum Title</strong></label>
                <input type="text" name="<?php echo $SUB_FORUM_TITLE?>" class="form-control"  placeholder="Forum Title" required>
            </div>
            <br>
            <div class="form-group">
                <label for="text"><strong>Forum Description</strong></label>
                <textarea name="<?php echo $SUB_FORUM_DESC?>" class="form-control" id="" rows="2" placeholder="Describe the forum" required></textarea>
            </div>
            <div class="form-group">
                <div class="container d-flex pt-4">
                    <button class="col btn btn-primary m-1" type="submit" name="<?php echo $SUBMIT_FORUM?>" value="">Publish</button>
                    <button class="col btn btn-danger m-1" onclick="closePopupForm('new_sub_forum_form')">Discard</button>
                </div>
            </div>
        </form>
    </div> <!--End of form div-->
    <div class="container-fluid d-flex justify-content-center" style="height: 100%;">
        <div class="rounded bg-light p-5 w-100 mt-5" style="overflow-x : auto;">
        
    <?php
        $target_type = $_GET[$TARGET];  //get the target type of this forums page
        $target_name = get_target_type_name($target_type);
        if ($target_name == "Tutor") {
            if ($_SESSION[$LOGIN_STATUS_ID] == $TUTOR) {
                echo "<h2 class='text-center'>Welcome to your course modules, update content here for your students to view.</h2>";
            } else {
                echo "<h2 class='text-center'>Welcome to your course modules!</h2>";
            }
        }
        else {
            echo "<h2 class='text-center'>Welcome to the $target_name Forums!</h2>";
        }
        $forum_query = "select * from $FORUM_TABLE where $TARGET='$target_type'";
        $forum_result = mysqli_query($con, $forum_query); 

        if($_SESSION[$LOGIN_STATUS_ID] == $TUTOR && $target_type==$TUTOR){
            //show post button only if the user is a tutor AND for tutors forums only
            echo "
            <div class='my-2'>
                <div class='btn-group' role='group'>
                    <button type='button' class='btn btn-primary' disabled>&#9998</button>
                    <button type='button' class='btn btn-primary' onclick='openPopupForm(\"new_sub_forum_form\")'>New Forum</button>
                </div>";
                $login_user_id = $_SESSION[$LOGIN_ID];
                //show the filter courses button if user has multiple courses
                if(countEntriesDB($con, $UC_TABLE, "where $USER_ID='$login_user_id'") > 1){
                echo"
                <div class='flex-shrink-0 dropdown filter-btn'>
                    <button class='btn btn-primary'>
                    <a href='#' class='d-block text-decoration-none dropdown-toggle text-white' id='profiledropdown' data-bs-toggle='dropdown' aria-expanded='false'>
                    Filter Courses
                    </a>
                    <ul class='dropdown-menu text-small shadow' aria-labelledby='profiledropdown'>
                    <li><a class='dropdown-item' href='?$FORUMS_PAGE&$TARGET=$TUTOR'>all</a></li>";
                    //create a list of courses that user can filter on
                    $course_query = "select * from $COURSE_TABLE";
                    $course_result = mysqli_query($con, $course_query);
                    
                    while($course = mysqli_fetch_assoc($course_result)){
                        $course_title = $course[$COURSE_TITLE];
                        $course_id = $course[$COURSE_ID];
                        echo "<li><a class='dropdown-item' href='?$FORUMS_PAGE&$TARGET=$TUTOR&$COURSE_ID=$course_id'>$course_title</a></li>";
                    }
                    //close the lists of courses
                    echo"
                    </ul>
                    </button> 
                </div>";
            }
            //close the button container
            echo "
            </div>
            ";
        }
    while($forum = mysqli_fetch_assoc($forum_result)){
        $forum_id = $forum[$FORUM_ID];
        $forum_title = $forum[$FORUM_TITLE];
        // echo the forum heading, starts
        echo " 
        <div class='container bg-primary text-white p-2'>
            <h5>$forum_title</h5>
        </div>
        <table class='table table-hover'>
            <tbody>
        "; // echo the forum heading ends
        $sub_forum_query = "select * from $SUB_FORUM_TABLE where $FORUM_ID='$forum_id'";
        if(isset($_GET[$COURSE_ID])){ //if user has filtered on some course basis
            $course_id = $_GET[$COURSE_ID];
            $sub_forum_query .= " and $COURSE_ID='$course_id'";
            //this additional conditions selects courses only according to selected course
        } 
        $sub_forum_result = mysqli_query($con, $sub_forum_query);

        while($sub_forum = mysqli_fetch_assoc($sub_forum_result)){
            $sub_forum_id = $sub_forum[$SUB_FORUM_ID];
            $sub_forum_title = $sub_forum[$SUB_FORUM_TITLE];
            $sub_forum_desc = $sub_forum[$SUB_FORUM_DESC];
            $sub_forum_course_id = $sub_forum[$COURSE_ID];
            //echo sub forums starts

            $n_of_posts = countEntriesDB($con, $POST_TABLE, "where $SUB_FORUM_ID='$sub_forum_id'");
            if(($target_type==$TUTOR && in_array($sub_forum_course_id,  $courses)) || $target_type == $STUDENT)
            echo"
                <tr onclick='location.href=\"?$POSTS_PAGE&$SUB_FORUM_ID=$sub_forum_id&$TARGET=$target_type\"'>
                    <td scope='row' style='font-size: 40px; background-color: gainsboro; width : 10%'>
                        <div class='d-flex my-0'>
                            <span class='fas fa-folder-open' style='margin: auto;'></span>
                        </div>
                    </td>
                    <td>
                        <h4>$sub_forum_title</h4>
                        <p>$sub_forum_desc</p>
                    </td>
                    <td>
                        $n_of_posts Posts
                    </td>
                    <td>
                        
                    </td>
                </tr>
            ";//echo sub forums end
        } //inner while loop end
        echo "
            </tbody> <!--table body ends when php has printed all the rows of table-->
        </table><br>"; //table ends everytime the outer loop completes one loop
    }  //outer while loop main forum (table) ends
?>
        </div>
    </div>
</div>