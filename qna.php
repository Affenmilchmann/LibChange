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
        
        $password_min_len = 8;
        $user_nickname = "NULL";
        $user_id = -1;
        
        //checking user cookie
        $check_res = check_user_cookie();
        
        //setting messages values
        $ok_error = "";
        $fatal_error = "";
        $suc_message = ""; 

        // $check_res codes are in /funcs.php
        if($check_res == 1) {
            $user_id = get_id($_COOKIE["LibChangeCookie"]);
            $user_nickname = get_nickname($user_id);
            if (is_int($user_nickname)) {
                delete_user_cookie("LibChangeCookie");
            }
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $fatal_error = $cookie_select_error; 
        }
        
        if($check_res == 1) {
            $user_id = (int)($_COOKIE["LibChangeID"]);
            $question = get_question($user_id);
            $answer = strtolower($_POST["answer"]);
            $hash = get_answer_hash($user_id);
            
            if(is_int($hash) and $hash == -1) {
                $fatal_error = $select_error;
            }
            else if (gettype($question) == "NULL") {
                $ok_error = $question_empty_error;
            }
            else if (!password_verify($answer, $hash)) {
                $ok_error = $incorrect_answer_error;
            }
            else {
                $new_password = getKey(10);
                
                $res = update("`myusers`", "`password` = '" . password_hash($new_password, PASSWORD_DEFAULT) . "'", "`id`=" . $user_id);
                
                delete_user_cookie("LibChangeID");
                
                if (!is_int($res) and $res != -1 or $res == 1) {
                    $headers = "From: support@libchange.ru \n";
                    mail (get_email($user_id), "LibChange email", "Your new password is: " . $new_password . ". You can change it in your profile. \n \n You are not to reply", $headers);
                    
                    $sec_message = $password_sent_message;
                }
                else {
                    $fatal_error = $update_error;
                }
            }
        }
        else {
            header("Location: /recovery.php");
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == 1, $user_nickname);
    ?>
    
    <section class="main">
        <section>
            <?php
                if (gettype($question) != "NULL" and !is_int($question)) {
                    echo '
                        <h2 class="mini_heading">Recovery</h2>
                        <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <div>
                                <div>
                                    <h3>' . str_replace("_", " ", $question) . '</h3>
                                </div>
                                <div>
                                    <input type="text" name="answer">
                                </div>
                            </div>
                            <br>
                            <input type="submit" name="submit" value="Next">  
                        </form>
                    ';
                }
                
                if ($fatal_error != "")
                    echo '<p class="fatal_error">' . $fatal_error . '</p>';
                
                if ($ok_error != "")
                    echo '<p class="ok_error">' . $ok_error . '</p>';
                    
                if ($suc_message != "")
                    echo '<p class="success">' . $suc_message . '</p>';
            ?>
        </section>
    </section>
</body>            