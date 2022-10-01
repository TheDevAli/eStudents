<?php
  //$chat_with_id = $_GET[$CHAT_WITH];
  $chat_with_img = "";
  if(isset($_POST[$NEW_CHAT])){
    $pid = $_POST[$NEW_CHAT];
    $user_query = "select * from $USER_TABLE where $USER_EMAIL like '$pid@%'";
    $user_result = mysqli_query($con, $user_query);
    if(mysqli_num_rows($user_result)){
      $user = mysqli_fetch_assoc($user_result);
      $pid = $user[$USER_ID];
      $login_id = $_SESSION[$LOGIN_ID];
      $newChat_query = "insert into $CHAT_TABLE ($SENDER_ID, $RECEIVER_ID) values ('$login_id','$pid')";
      mysqli_query($con, $newChat_query);
    }else{
      //results not found!
      echo "
        <script>
          alert('User Not Found!');
        </script>
      ";
    }
  }
  $rec_profile_img = 'null.png';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body><br>
  <div class="d-flex row justify-content-center align-items-center rounded p-3 mx-5 echat-container" style="height: 85%;">
      <div class="row bg-light rounded" style=" height: 100%; padding: 2em 1em;">
        <div class="col-4 py-2 gray-separator" style="background-color: white; height : 100%; overflow-y: scroll;">
          <!--Start global Chat-->
          <a href='<?php echo "?$E_CHAT_PAGE&$CHAT_ID=0"; ?>' class='simple-link'>
          <div class="d-flex align-items-center hover-gray p-1 my-1">
            <img src="src/dmu-icon.jpg" onerror="this.src='src/user_donut_img.png'"  class="rounded-circle d-block img-reponsive" width="50"  alt="logo">
            <h5 class="mx-2">Global Chat</h5>
          </div>
          </a>
          <hr>  <!--Break after global chats-->
          <div class='d-flex align-items-center'>
            <h4>Private Chats</h4>
            <button class="btn btn-primary mx-1" style="font-size:1rem; weight: bold; border-radius : 10rem" onclick="toggleCssDisplay('new_chat')">
              +
            </button><!-- full width + sign using unicode-->
          </div>
          <form method="post" action="?echat">
            <input class="form-control my-1" style="display : none" id="new_chat" name="new_chat" placeholder="Enter ID" type="text">
          </form>
          <!--end Global chats-->
          <?php 
            //select chats for the logged in user
            $user_id = $_SESSION[$LOGIN_ID];
            $query = "select * from $CHAT_TABLE where $SENDER_ID='$user_id' or $RECEIVER_ID='$user_id'";
            $chat_results = mysqli_query($con, $query);
            if(mysqli_num_rows($chat_results)){
              while($chat = mysqli_fetch_assoc($chat_results)){
                //get the receiver ids with whom chats were started by this user
                $chat_id = $chat[$CHAT_ID];

                /*we have a common chat table for both sender and receiver
                Now lets see then who is receiver for the current logged in session
                If the logged in user id equals the sender id then the receiver is actual receiver in the chat table
                else if the logged in user id equals the receiver id then the sender is the actual receiver of the chat for the current logged in session
                So, as a result the opposite users are displayed in side bar of chat, which is receiver
                */
                $receiver_id = ($chat[$SENDER_ID] == $_SESSION[$LOGIN_ID]) ? $chat[$RECEIVER_ID] : $chat[$SENDER_ID];
                $query = "select * from $USER_TABLE where $USER_ID='$receiver_id'";
                $receiver_result = mysqli_query($con, $query);
                
                //now get the info of receiver from the user table
                $receiver = mysqli_fetch_assoc($receiver_result);
                $rec_user_id = $receiver[$USER_ID];
                $receiver_name = $receiver[$USER_NAME];
                $username = getUsernameByID($con, $rec_user_id);
                $rec_profile_img = $receiver[$PROFILE_IMG];
                if(isset($_GET[$CHAT_WITH]) && $username == $_GET[$CHAT_WITH]){
                  $chat_with_img = $rec_profile_img;
                  //currently chatting with this user.
                }

                echo "
                <a href='?$E_CHAT_PAGE&$CHAT_ID=$chat_id&$CHAT_WITH=$username' class='simple-link'>
                  <div class='d-flex py-1 border-bottom border-secondary hover-gray'>   
                      <img src='src/$rec_profile_img' onerror='this.src=\"src/user_donut_img.png\"'  class='rounded-circle border d-block img-reponsive mx-2' width='40' height='40'  alt='logo'>
                      <div>
                          <h5>$username <sub class='text-muted'></sub></h5>
                          <p class='text-muted'>$receiver_name</p>
                      </div>
                  </div>
                </a>
                ";
              }  //end while
            }  //end if
          ?>
          <!--Start left users-->

        </div>
        <div class="col-8" style="position: relative;">
        <!--Receiver Header-->
          <div class="d-flex container-fluid align-items-center p-1 my-1">
            <img src="src/<?php echo $chat_with_img?>" onerror="this.src='src/dmu-icon.jpg'"  class="rounded-circle d-block img-reponsive" width="50"  alt="logo">
            <h5 class="mx-2"><?php echo isset($_GET[$CHAT_WITH])? $_GET[$CHAT_WITH]:"eChat - Interact with other students, message your tutors for additional support."; ?></h5>
          </div>
        <!-- Receiver header ends-->
          <hr>
          <div id="msg_box" style="height : 35rem; overflow-y: scroll;">
            <!--Here content is set when we fetch chats-->
          </div>

          <div style="position: absolute; bottom: 5; display:flex; width: 90%; background-color: #f8f9fa;">
              <textarea  type="text" class="msg-input form-control" id="msg_input" name="msg_input" placeholder="Write Message Here"></textarea>
              <button class="emoji-btn btn btn-primary">☺️</button>
          </div>
        </div>       
      </div>
    </div>    
  </body>
</html>

<script type="text/javascript">
  //last msg id is the id of last message, we use it to get know if there is need to print new message
  var last_msg_id = 0;  // refresh page only if the reponse text includes the new msgs
  let parser = new DOMParser();

  function getAjaxChats(chat_id) {  //get live chats using json from php
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.responseType = 'json';
    xmlhttp.onload = function() {
      var msg_box = document.getElementById("msg_box");
      var new_msgs = xmlhttp.response;  //response is in json
      var json_msgs = JSON.parse(new_msgs);  //create json object of data
      
      //document.getElementById("msg_box").innerHTML = json_msgs;
      for(let i = 0; i < json_msgs.length; i++)
      {
        if(json_msgs[i].msgID > last_msg_id)
        {
          //create the html for every msg
          //var user_id = getUsernameById(json_msgs[i].senderID);
          var msgs_html = `
          <div class='d-flex'>   
            <img src='src/${json_msgs[i].profile_img}' onerror='this.src=\"src/user_donut_img.png\"'  class='rounded-circle d-block img-reponsive mx-2' width='40' height='40'  alt='logo'>
            <div>
                <h5>${json_msgs[i].username} <sub class='text-muted'>${json_msgs[i].datetime}</sub></h5>
                <p>${json_msgs[i].msg_text}</p>
              </div>
          </div>
          `;
          msg_box.innerHTML += msgs_html;
          //scroll to the latest message
          last_msg_id = json_msgs[i].msgID;
          msg_box.scrollTop = msg_box.scrollHeight;
        }
      }
    };
    xmlhttp.open("GET", `handle_chat.php?get_messages&chatID=${chat_id}`, true);
    xmlhttp.send();
  }

  function sendMsg(msg_text, chat_id){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", `handle_chat.php?send_message=${msg_text}&chatID=${chat_id}`, true);
    xmlhttp.send();  
    getAjaxChats();  //reload chats from database after sending new message
  }

  var msg_input = document.getElementById("msg_input");
  msg_input.addEventListener("keyup", function(event){
    if(event.keyCode == 13){  // 13 is keycode for enter button
      sendMsg(msg_input.value, <?php echo isset($_GET[$CHAT_ID])? $_GET[$CHAT_ID] : 0; ?>);  //if enter is pressed, send the message
      msg_input.value = "";
    }
  });

  //check for new messages every 1 second
  setInterval( function() { 
    getAjaxChats(<?php echo isset($_GET[$CHAT_ID])? $_GET[$CHAT_ID] : 0; ?>); 
    }, 1000);
</script>