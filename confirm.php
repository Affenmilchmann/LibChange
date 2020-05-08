<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <title>Confirm email</title>
</head>
<body class="glob">
    <?php
        include("funcs.php");
        
        $code_input = $user_nickname = $email = $code = 0;
        $id = 0;
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $code_input = test_input($_POST["code"]);
        }
        
        //checking user cookie
        $check_res = check_user_cookie();
        
        //setting messages values
        $ok_error = "";
        $fatal_error = "";
        $suc_message = "";   
        
        // $check_res codes are in /funcs.php
        if($check_res == $OK) {
            $id = get_id($_COOKIE["LibChangeCookie"]);
                
            $user_info = get_user_info($id);
            $email = $user_info['email'];
            $user_nickname = $user_info['nickname'];
            $code = $user_info['email_confirmed'];
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
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == $OK, $user_nickname);
    ?>
    
    <section class="main">
        <?php
            if ($code_input == $code){
                $res = update("myusers", "email_confirmed=-1", "id=" . $id);
                
                if ($res != $DB_ERROR) {
                    $suc_message = $email_confirmed_message;
                }
                else {
                    $fatal_error = $update_error;
                }
            }
            else if($code == "-1") {
                echo '
                    <h3>Your email is already comfirmed!</h3>
                ';
            }
            else {
                echo '
                    <h3>Code sent to ' . $email . '. Check your spambox.</h3>
                    <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) .' ">
                        <div class="child">
                            Code*:
                        </div>
                        <div class="child">
                            <input type="text" name="code">
                        </div>
                        <br>
                        <input type="submit" name="submit" value="Submit"> 
                    </form>
                    <a href="message_send.php">
                        <button>Send again</button>
                    </a>
                ';
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $ok_error = $wrong_code_error;
                }
            }

            form_error_section($fatal_error, $ok_error, $suc_message);
        ?>
    </section>
</body>