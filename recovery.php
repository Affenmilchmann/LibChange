<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Recovery</title>
</head>
<body class="glob">
    
    <?php
        include 'funcs.php';
    
        //checking user cookie
        $check_res = check_user_cookie();
        
        //setting messages values
        $ok_error = "";
        $fatal_error = "";
        $suc_message = ""; 
        
        // $check_res codes are in /funcs.php
        if ($check_res == $IP_CONFLICT) {
            direct_to("ip_conflict.php");
        }
        else if ($check_res == $DB_ERROR) {
            $fatal_error = $cookie_select_error; 
        }
        
        if ($_POST["email_or_nickname"] != "") {
            //showing message anyway
            $suc_message = $recovery_success_message;
            
            //Genereting recovery link if user exists
            $sel_res = select("email, id", "myusers", "email ='" . $_POST["email_or_nickname"] ."' or nickname ='" . $_POST["email_or_nickname"] . "'");
            if ($sel_res == $DB_ERROR) {
                $fatal_error = $select_error;
            }
            else if ($sel_res != $EMPTY_ANSWER) {
                $key_link = getKey(20);
                
                //recording code to the DB
                $del_res = delete("`rec_links`", "`user_id`=" . $sel_res['id']);
                
                if ($del_res != $DB_ERROR) {
                
                    $res = insert("`rec_links`(`user_id`, `code`)", "(" . $sel_res['id'] . ", '" . $key_link . "')");
                    
                    if ($res != $DB_ERROR) {
                        //sending mail
                        $link = $site_name . htmlspecialchars($_SERVER["PHP_SELF"]) . "?key=" . $key_link;
                        
                        $message = "You tried to change your password! 
                        <br> If you did, just click this link and we will send you your new password. <br>" . 
                        "<a href=\"". $link. "\" title='My Page'>" . $link . "</a> <br>";
                        $message .= "This link is for single use.";
                        
                        if (send_email($message , $sel_res['email']) == false) {
                            $fatal_error = $email_sending_error;
                        }
                    }
                    else {
                        $fatal_error = $insert_error;
                    }
                }
                else {
                    $fatal_error = $delete_error;
                }
            }
        }
        
        if ($_GET["key"] != "") {
            if (is_requests_amount_ok($_SERVER['REMOTE_ADDR']) == false) {
                $ok_error = $max_requests_achieved;
            }
            else {
                //deletting link code
                $sel_res = select("user_id", "rec_links", 'code="' . $_GET["key"] . '"');
                
                if ($sel_res != $DB_ERROR) {
                    
                    $del_res = delete("`rec_links`", '`code`="' . $_GET["key"] . '"');
                    
                    if ($del_res != $DB_ERROR) {
                        if ($sel_res != $EMPTY_ANSWER) {
                            //resetting password
                            $new_password = getKey(10);
                        
                            $upd_res = update("`myusers`", "`password` = '" . password_hash($new_password, PASSWORD_DEFAULT) . "'", "`id`=" . $sel_res['user_id']);
                            
                            if ($upd_res != $DB_ERROR) {
                                $sel_res = get_user_info($sel_res['user_id']);
                                
                                if ($sel_res != $DB_ERROR) {
                                
                                    $message = "You have changed your password! <br>
                                                Your new password is: <b>" . $new_password . "</b><br>
                                                You can change it in your profile settings<br>
                                                Be careful and tell this password nobody!";
                                                
                                    if (send_email($message , $sel_res['email'])) {
                                        $suc_message = $password_sent_message;
                                    }
                                    else {
                                        $fatal_error = $email_sending_error;
                                    }
                                }
                                else {
                                    $fatal_error = $select_error;
                                }
                            }
                            else {
                                $fatal_error = $update_error;
                            }
                        }
                    }
                    else {
                        $fatal_error = $delete_error;
                    }
                }
                else {
                    $fatal_error = $select_error;
                }
            }
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == $OK, $user_nickname);
    ?>
    
    <section class="main">
        <section>
            <h2>Recovery</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <div>
                        Nickname/E-mail:
                    </div>
                    <div>
                        <input type="text" name="email_or_nickname" <?php echo "value='" . $nickname , "'"?>>
                    </div>
                </div>
                <input type="submit" name="submit" value="Next">  
            </form>
            <br>
            <?php
                form_error_section($fatal_error, $ok_error, $suc_message);
            ?>
        </section>
    </section>
</body>