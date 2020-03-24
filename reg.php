<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Registration</title>
</head>
<body class="glob">
    <?php
        include("funcs.php");
        
        $password_min_len = 8;
        $nickname_min_len = 4;

        $check_res = check_user_cookie();
            
        if($check_res == 1) {
            $end_code['class'] = "ok_error";
            $end_code['message'] = "You are already logged!"; 
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $end_code['class'] = "fatal_error";
            $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nickname = test_input($_POST["nickname"]);
            $email = test_input($_POST["email"]);
            $location = test_input($_POST["location"]);
            $password = test_input($_POST["password"]);
            $password_repeat = test_input($_POST["password_repeat"]);
            $question = text_test_input($_POST["question"]);
            $answer = text_test_input($_POST["answer"]);
            
            // checking existanse
            $nickname_res = select("nickname", "myusers", "LOWER(nickname) ='" . strtolower($nickname) . "'");
            $email_res = select("email", "myusers", "LOWER(email) ='" . strtolower($email) . "'");
            
            $end_code['class'] = 1;
            
            if($nickname_res != -1 and $email_res != -1) {
                if (strtolower($nickname_res['nickname']) == strtolower($nickname)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'This nickname already exists!';
                }
                else if (!check_nickname($nickname)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = "Only letters, digits and '_' allowed in nickname!";
                }
                else if (strlen($email) == 0) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'You must input your email!';
                }
                else if (strtolower($email_res['email']) == strtolower($email)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'This email already exists!';
                }
                else if (!filter_var($email, FILTER_VALIDATE_EMAIL) and strlen($email) != 0) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Email format is invalid.';
                }
                else if (strlen($nickname) < $nickname_min_len) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Your nickname must contain at least ' . $nickname_min_len . ' characters';
                }
                else if (!check_text($question) and (strlen($question) != 0)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Only latin letters, digits, space and "?!,.+-=&" allowed in question';
                }
                else if (strlen($answer) == 0 and (strlen($question) != 0)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'You must input answer!';
                }
                else if (!check_text($answer) and (strlen($question) != 0)) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Only latin letters, digits, space and "?!,.+-=&" allowed in answer';
                }
                else if (strlen($password) < $password_min_len) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Your password must contain at least ' . $password_min_len . ' characters and contain letters and digits only.';
                }
                else if ($password_repeat != $password) {
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = 'Passwords are different!';
                }
                else {
                    //generating confirm code
                    $code = rand(1000, 99999);
                    
                    //generating INSERT message
                    $where = "`myusers`(`nickname`, `password`, `email_confirmed`";
                    if (strlen($email) != 0) {
                        $where .= ", `email`";
                    }
                    if (strlen($location) != 0) {
                        $where .= ", `city`";
                    }
                    if (strlen($question) != 0) {
                        $where .= ", `question`, `answer`";
                    }
                    $where .= ")";
                    
                    $what = "('" . $nickname . "','" . password_hash($password, PASSWORD_DEFAULT) . "','" . $code . "'";
                    if (strlen($email) != 0) {
                        $what .= ", '" . strtolower($email) . "'";
                    }
                    if (strlen($location) != 0) {
                        $what .= ", '" . $location . "'";
                    }
                    if (strlen($question) != 0) {
                        $what .= ", '" . str_replace(" ", "_", $question) . "','" . password_hash(strtolower($answer), PASSWORD_DEFAULT) . "'";
                    }
                    $what .= ")";
                    
                    //sending INSERT message
                    $res = insert($where, $what);
                    if (!is_int($res))
                    {
                        delete_user_cookie("LibChangeCookie");
                        
                        //generating cookie key
                        $key = getKey(20);
                        //setting cookie on user side
                        setcookie("LibChangeCookie", $key, time() + (86400), "/"); 
                        
                        //getting new user id
                        $res = select("id", "myusers", "nickname ='" . $nickname . "'");
                        //and inserting new cookie
                        $res = insert("`cookies`(`user_id`, `cookie`)", "(" . $res['id'] . ", '" . $key . "')");
                        
                        //sending confirmation code to users email
                        $headers = "From: support@libchange.ru \n";
                        mail ($email, "LibChange email", "Your security code is: " . $code . ". \n \n You are not to reply", $headers);
                        header("Location: /confirm.php");
                    }
                    else {
                        $end_code['class'] = "fatal_error";
                        $end_code['message'] = 'Error. While inserting data into DB. Request code ' . $res . '. Please, contact support@libchange.ru';
                    }
                }
            }
            else {
                $end_code['class'] = "fatal_error";
                $end_code['message'] = 'Error. While inserting data into DB. Request code ' . $email_res . ' and ' . $nickname_res . '. Please, contact support@libchange.ru';
            }
        }
    ?>
    
    <section class="top">
        <section class="main_text_box">
            <a href="index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
    </section>
    
    <section class="main">
        <section class="main_child">
            <h2>Registration form</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <div>
                        Nickname*:
                    </div>
                    <div>
                        <input type="text" name="nickname" <?php echo "value='" . $nickname , "'"?>>
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        E-mail*:
                    </div>
                    <div>
                        <input type="text" name="email" <?php echo "value='" . $email . "'"?>>
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Location:
                    </div>
                    <div>
                        <input type="text" name="location" <?php echo "value='" . $location . "'"?>>
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Password*:
                    </div>
                    <div>
                        <input type="password" name="password">
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Repeat your password*:
                    </div>
                    <div>
                        <input type="password" name="password_repeat">
                    </div>
                </div>
                <br>
                <br>
                <div>
                    <div>
                        Security question**:
                    </div>
                    <div>
                        <input type="text" name="question" <?php echo "value='" . $question . "'"?>>
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Answer**:
                    </div>
                    <div>
                        <input type="text" name="answer" <?php echo "value='" . $answer . "'"?>>
                    </div>
                </div>
                <input type="submit" name="submit" value="Submit">  
                <?php
                    echo '<p class="' . $end_code['class'] . '">' . $end_code['message'] . '</p>';
                ?>
                <p>* Required fields</p>
                <p>** Not required, but if you forget your password, you will not be able to restore your account!</p>
            </form>
        </section>
    </section>
    
</body>