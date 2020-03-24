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
        
        $end_code['class'] = 1;
        $end_code['message'] = '';
        
        $check_res = check_user_cookie();
            
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
            $end_code['class'] = "fatal_error";
            $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
        
        if(isset($_COOKIE["LibChangeID"])) {
            $user_id = (int)($_COOKIE["LibChangeID"]);
            $question = get_question($user_id);
            $answer = strtolower($_POST["answer"]);
            $hash = get_answer_hash($user_id);
            
            if(is_int($hash) and $hash == -1) {
                $end_code['class'] = "fatal_error";
                $end_code['message'] = 'Error. While selecting data from DB. Request code ' . $hash . '. Please, contact support@libchange.ru';
            }
            else if (gettype($question) == "NULL") {
                $end_code['class'] = "ok_error";
                $end_code['message'] = 'You have no question!';
            }
            else if (!password_verify($answer, $hash)){
                $end_code['class'] = "ok_error";
                $end_code['message'] = 'Answer is incorrect.';
            }
            else {
                $new_password = getKey(10);
                
                $res = update("`myusers`", "`password` = '" . password_hash($new_password, PASSWORD_DEFAULT) . "'", "`id`=" . $user_id);

                if (!is_int($res) and $res != -1 or $res == 1) {
                    $headers = "From: support@libchange.ru \n";
                    mail (get_email($user_id), "LibChange email", "Your new password is: " . $new_password . ". You can change it in your profile. \n \n You are not to reply", $headers);
                    
                    $end_code['class'] = "success";
                    $end_code['message'] = 'Password sent to your mail.';
                    
                    delete_user_cookie("LibChangeID");
                }
                else {
                    $end_code['class'] = "fatal_error";
                    $end_code['message'] = 'Error. While updating data onto DB. Request code ' . $res . '. Please, contact support@libchange.ru';
                    delete_user_cookie("LibChangeID");
                }
            }
        }
        else {
            header("Location: /recovery.php");
        }
    ?>
    
    <section class="top">
        <section class="main_text_box">
            <a href="index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
        <?php
            if(isset($_COOKIE["LibChangeCookie"]) and ($user_nickname != "NULL")) {
                echo    "
                        <section class='main_button_box'>
                            <a class='top_button' href='logout.php'>
                                <button>Logout</button>
                            </a>
                            <a href ='profile.php'>
                                <p class='logged'>" . $user_nickname . " </p>
                            </a>
                        </section> 
                
                ";
            }
            else {
                delete_user_cookie("LibChangeCookie");
                echo    "
                        <section class='main_button_box'>
                            <a class='top_button' href='log.php'>
                                <button>Login</button>
                            </a>
                            <a class='top_button' href='reg.php'>
                                <button>Registration</button>
                            </a>
                        </section>
                        ";
            }
        ?>
    </section>
    
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
                echo '<p class="' . $end_code['class'] . '">' . $end_code['message'] . '</p>';
            ?>
        </section>
    </section>
</body>            