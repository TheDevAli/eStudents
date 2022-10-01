<?php
    //first we check that if the user submitted the new post, so we upload it to the database
    //then show the result
    if(isset($_POST[$SUBMIT_POST])) {
        $sub_form_id = $_POST[$SUBMIT_POST];  //the publish buttons decides which form to post submit the post in, so we are storing the sub forum id in it while submitting the forum

        //we escape the special characters in strings
        //and it will also help in securing the sql injections
        $post_title = mysqli_real_escape_string($con, $_POST[$POST_TITLE]);
        $post_content = mysqli_real_escape_string($con, $_POST[$POST_CONTENT]);
        //if input contains <script> tags (disables javascript usage and Cross-Site Scripting)
        if (strpos($post_title, "<script>") || strpos($post_content, "<script>")) {
            //do nothing, deny the submission of the post
        } else { //submit the post to the server and display on the UI
            $user_id = $_SESSION[$LOGIN_ID]; //retrieve the id of logged in customer from session
            $post_insert_query = "insert into $POST_TABLE ($POST_TITLE, $POST_CONTENT, $SUB_FORUM_ID, $USER_ID) values ('$post_title', '$post_content', '$sub_form_id', $user_id)";
            $result = mysqli_query($con, $post_insert_query);
            $post_id = mysqli_insert_id($con);  //get the id of post inserted just now, because its auto increment
    
            //$files = array_filter($_FILES['upload']['name']); //something like that to be used before processing files.
    
            // Count # of uploaded files in array
            $total_files = count($_FILES['upload']['name']);
    
            // Loop through each file
            for( $i=0 ; $i < $total_files ; $i++ ) {
    
            //Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
    
                //Make sure we have a file path
                if ($tmpFilePath != ""){
                    $filename = mysqli_real_escape_string($con, $_FILES['upload']['name'][$i]);
                    //Setup our new file path
                    $newFilePath = "./$UPLOADS_DIR/" . $_FILES['upload']['name'][$i];
    
                    //Upload the file into the temp dir
                    if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $time = round(microtime(true) * 1000); 
                        $unq_filename = "$time-$filename";
                        rename("$UPLOADS_DIR/$filename", "$UPLOADS_DIR/$unq_filename");
                        // if file uploaded then insert a record in database
                        $insert_query = "insert into $UPLOADS_TABLE ($POST_ID, $FILENAME, $FILENAME_UNIQUE) values ('$post_id', '$filename', '$unq_filename')";
                        mysqli_query($con, $insert_query);
                    }
                }
            }
        }
    }
?>
<!-- this form is visible when user clicks on the new post button-->
<div>
    <form method="post" enctype="multipart/form-data" class="popup-form" id="new_post_form">
        <h2 class="text-center">
            Create New Post
        </h2>
        <div class="form-group">
            <label for="text"><strong>Post Title</strong></label>
            <input type="name"  class="form-control" placeholder="Post Title" name="post_title" required>
        </div>
        <br>
        <div class="form-group">
            <label for="text"><strong>Post Content</strong></label>
            <textarea name="post_content" class="form-control" id="postContentArea" rows="5" placeholder="Post content here" required></textarea>
        </div>
        <div class="form-group">
            <input class="form-control my-1" name="upload[]" type="file" multiple/>
        </div>
        <div class="form-group">
            <div class="container d-flex pt-4">
                <button class="col btn btn-primary m-1" type="submit" name="<?php echo $SUBMIT_POST?>" value="<?php echo $_GET[$SUB_FORUM_ID]?>">Publish</button>
                <button class="col btn btn-danger m-1" onclick="closePopupForm('new_post_form')">Discard</button>
            </div>
        </div>
    </form>
</div>
<div>

    <div class="rounded bg-light p-3 m-5" style="overflow-x : auto; min-height : 500px">
        <?php
            $target_type = $_GET[$TARGET];
            if(($target_type == $TUTOR && $_SESSION[$LOGIN_STATUS_ID] == $TUTOR) || $target_type == $STUDENT){
                //show this button only if the the logged in user is tutor
                //OR the target of page is student, so student can create posts in this section
                echo "
                <div class='btn-group my-2' role='group' aria-label=''>
                    <button type='button' class='btn btn-primary' disabled>&#9998</button>
                    <button type='button' class='btn btn-primary' onclick='openPopupForm(\"new_post_form\")'>New Post</button>
                </div>
                ";
            }
            $sub_forum_id = $_GET[$SUB_FORUM_ID];
            //get subforum name
            $sub_forum_query = "select * from $SUB_FORUM_TABLE where $SUB_FORUM_ID='$sub_forum_id'";
            $sub_forum_result = mysqli_query($con, $sub_forum_query);
            $sub_forum_title = mysqli_fetch_assoc($sub_forum_result)[$SUB_FORUM_TITLE];
        ?>

        <div class='container bg-primary text-white p-2' style="max-width: 100%">
            <h5><?php echo $sub_forum_title?></h5>
        </div>
        <table class='table table-hover'>
            <tbody>

        <?php
        //get posts data
        //Join the post table with user table to get info of both
        $posts_query = "select * from $POST_TABLE inner join $USER_TABLE on $POST_TABLE.$USER_ID = $USER_TABLE.$USER_ID where $SUB_FORUM_ID='$sub_forum_id'";
        $posts_result = mysqli_query($con, $posts_query);
        // print the rows of posts
        while($post = mysqli_fetch_assoc($posts_result)){
            $post_id = $post[$POST_ID];
            $post_title = $post[$POST_TITLE];
            $post_content = $post[$POST_CONTENT];
            $user_id = $post[$USER_ID];
            $datetime  = $post[$DATE_TIME];
            $user_name = $post[$USER_NAME];

            //number of replies for the current post
            $n_of_replies = countEntriesDB($con, $REPLY_TABLE, "where $POST_ID='$post_id'");
            $n_of_likes = countLikes($post_id, 0);  //second argument is of reply id which is not needed here

            echo"
                <tr onclick='location.href=\"?$REPLIES_PAGE&$SUB_FORUM_ID=$sub_forum_id&$POST_ID=$post_id&$TARGET=$target_type\"'>
                    <td scope='row' style='font-size: 40px; background-color: gainsboro;'>
                        <div class='d-flex my-0'>
                            <span class='fas fa-folder-open' style='margin: auto;'></span>
                        </div>
                    </td>
                    <td>
                        <h4>$post_title</h4>
                        <p>Started By: $user_name</p>
                    </td>
                    <td>
                        $n_of_replies Replies
                    </td>
                    <td>
                        $n_of_likes Likes
                    </td>
                    <td>
                        Date of Creation: $datetime
                    </td>
                </tr>
            ";
        }
        ?>
        </tbody> <!--table body ends when php has printed all the rows of table-->
    </table>
    <?php
    //check if there posts exists for this forum
    if(!mysqli_num_rows($posts_result)){
        echo '<h1 class="d-flex justify-content-center text-muted">No Posts Yet!</h1>';
    }
    ?>
    </div>
</div>