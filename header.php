<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <header class="p-3 mb-3 border-bottom bg-light">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <a href="index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
              <h2><i class="fas fa-graduation-cap"></i>eStudents</h2>
          </a>

          <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <?php
              $space = "pl-1";
              if($_SESSION[$LOGIN_STATUS_ID] == $STUDENT)  //students forums are visible to students only
              {
                echo "
                <li><a href='#' class='nav-link v-break pl-1'></a></li> <!--vertical break only-->
                <li><a href='?$FORUMS_PAGE&$TARGET=$STUDENT' class='nav-link px-2 link-dark hover-gray rounded'>Student Forums</a></li>
                ";
                $space = "px-0";
              }
            ?>
            <li><a href="#" class="nav-link v-break <?php echo $space?>"></a></li> <!--vertical break only-->
            <li><a href="<?php echo "?$FORUMS_PAGE&$TARGET=$TUTOR";?>" class="nav-link px-2 link-dark hover-gray rounded">Tutor Forums</a></li>
            <li><a href="#" class="nav-link v-break px-0"></a></li> <!--vertical break only-->
            <li><a href="<?php echo "?$E_CHAT_PAGE";?>" class="nav-link px-2 link-dark hover-gray rounded">eChat</a></li>
            <li><a href="#" class="nav-link v-break px-0"></a></li> <!--vertical break only-->
            <?php
              if($_SESSION[$LOGIN_STATUS_ID] == $TUTOR)  //tutor portal is visible to tutors only
              {
                echo "
                <li><a href='?$TUTOR_PORTAL_PAGE' class='nav-link px-2 link-dark hover-gray rounded'>Tutor Portal</a></li>
                <li><a href='#' class='nav-link v-break px-0'></a></li> <!--vertical break only-->
                ";
              }
            ?>
          </ul>
          
          <div class="flex-shrink-0 dropdown">
              <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="profiledropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="src/<?php echo $_SESSION[$LOGIN_PROFILE_IMG]?>" onerror="this.src='src/user_donut_img.png'" alt="profile_img" width="40" height="40" class="rounded-circle border">
              </a>
              <ul class="dropdown-menu text-small shadow" aria-labelledby="profiledropdown">
                <li><a class="dropdown-item" href="<?php echo "?$PROFILE_PAGE";?>">Profile</a></li>
                <li><a class="dropdown-item" href="<?php echo "$LOGOUT.PHP";?>">Log out</a></li>
              </ul>
          </div>
        </div>
      </div>
    </header>    
  </body>
</html>

