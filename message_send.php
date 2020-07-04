<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Mail-sending page</title>
</head>
<body class="glob">
<?php
    include("funcs.php");
    
    $user_nickname = $email = $code = 0;
    $id = 0;
    
    //checking user cookie
    $check_res = check_user_cookie();
    
    //setting messages values
    $ok_error = "";
    $fatal_error = "";
    $suc_message = "";   
    
    // $check_res codes are in /funcs.php
    if($check_res == $OK) {
        $user_id = get_id($_COOKIE["LibChangeCookie"]);
            
        $user_info = get_user_info($user_id);
        
        $user_nickname = $user_info['nickname'];
        $user_email = $user_info['email'];
        $email_confirm = $user_info['email_confirmed'];
    }
    else if ($check_res == $IP_CONFLICT) {
        direct_to("ip_conflict.php");
    }
    else if ($check_res == $DB_ERROR) {
        $fatal_error = $cookie_select_error;
    }
    else {
        //sending user to the login page if he is not logged and there is no cookie error
        direct_to("log.php");
    }
    
    if (is_requests_amount_ok($_SERVER['REMOTE_ADDR']) == false) {
        $ok_error = $max_requests_achieved;
    }
    else {
        if (send_email("Your security code is: " . $email_confirm . ". \n \n You are not to reply", $user_email) == true) {
            echo "TRUE<br>";
			direct_to("confirm.php");
        }
        else {
            echo "FALSE<br>" . error_get_last()['message'];
			$fatal_error = $email_sending_error;
        }
    }
    form_hat($check_res == $OK, $user_nickname);
    form_error_section($fatal_error, $ok_error, $suc_message);
?>
</body>