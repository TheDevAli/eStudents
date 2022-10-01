<?php
    //this page is handled using ajax;
    session_start();
    include("required/db.php");
    include("required/constants.php");

    //lets watch which request is send to this page
    if(isset($_GET['get_messages']))
    {
        $json_str = "[";
        $chat_id = $_GET[$CHAT_ID];
        $msg_query =  "select * from $MSG_TABLE inner join $USER_TABLE on $MSG_TABLE.$SENDER_ID = $USER_TABLE.$USER_ID where $CHAT_ID='$chat_id'";
        $msg_result = mysqli_query($con, $msg_query); 
        $num_rows = mysqli_num_rows($msg_result);
        $count = 1; //for counting current json object number
        while($msg = mysqli_fetch_assoc( $msg_result)){
            $msg_text = $msg['msg_text'];
            $msg_id = $msg['msgID'];
            $sender_id = $msg['senderID'];
            $datetime = $msg['datetime'];
            $email = $msg[$USER_EMAIL];
            $username = explode("@", $email)[0];
            $profile_img = $msg[$PROFILE_IMG];

            $json_str .= '{"msgID":'.$msg_id.','.'"msg_text":"'.$msg_text.'", "username" : "'.$username.'", "datetime" : "'.$datetime.'", "profile_img" : "'.$profile_img.'"}';
            //$json_stor .= '}'; //close this json object
            //if this is the last arrow, then no need to put comma for the next json object
            if($count < $num_rows){
                $json_str .= ",";  //there is a next json object expected
            }
            $count++;
        }
        $json_str .= "]"; 
        $json_msg_data = json_encode($json_str);
        echo "$json_msg_data";
    }if(isset($_GET['send_message']))
    {  
        $login_id = $_SESSION[$LOGIN_ID];
        $chat_id = $_GET[$CHAT_ID];
        $msg_text = mysqli_real_escape_string($con, $_GET['send_message']);
        $msg_query =  "insert into $MSG_TABLE (chatID, msg_text, senderID) values ('$chat_id','$msg_text', '$login_id')";
        $msg_result = mysqli_query($con, $msg_query); 
        //echo $msg_result;
    }
?>