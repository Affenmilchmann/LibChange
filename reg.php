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

        //checking user cookie
        $check_res = check_user_cookie();
        
        //setting messages values
        $ok_error = "";
        $fatal_error = "";
        $suc_message = ""; 

        // $check_res codes are in /funcs.php
        if($check_res == $OK) {
            $ok_error = $already_logged_error;
        }
        else if ($check_res == $IP_CONFLICT) {
            direct_to("ip_conflict.php");
        }
        else if ($check_res == $DB_ERROR) {
            $fatal_error = $cookie_select_error; 
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nickname = test_input($_POST["nickname"]);
            $email = test_input($_POST["email"]);
            $location = test_input($_POST["location"]);
            $password = test_input($_POST["password"]);
            $password_repeat = test_input($_POST["password_repeat"]);
    
            // checking existanse
            $nickname_res = select("nickname", "myusers", "LOWER(nickname) ='" . strtolower($nickname) . "'");
            $email_res = select("email", "myusers", "LOWER(email) ='" . strtolower($email) . "'");
            
            
            if($nickname_res != $DB_ERROR and $email_res != $DB_ERROR) {
                if (strlen($nickname) == 0) {
                    $ok_error = $nickname_empty_error;
                }
                else if (strtolower($nickname_res['nickname']) == strtolower($nickname)) {
                    $ok_error = $nickname_already_exists_error;
                }
                else if (!check_nickname($nickname)) {
                    $ok_error = $nickname_format_error;
                }
                else if (strlen($email) == 0) {
                    $ok_error = $email_empty_error;
                }
                else if (strtolower($email_res['email']) == strtolower($email)) {
                    $ok_error = $email_already_exists_error;
                }
                else if (!filter_var($email, FILTER_VALIDATE_EMAIL) and strlen($email) != 0) {
                    $ok_error = $email_format_error;
                }
                else if (strlen($nickname) < $nickname_min_len) {
                    $ok_error = $short_nickname_error;
                }
                else if (!check_password($password)) {
                    $ok_error = $passwod_format_error;
                }
                else if (strlen($password) < $password_min_len) {
                    $ok_error = $short_password_error;
                }
                else if ($password_repeat != $password) {
                    $ok_error = $password_dismatch_error;
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
                    $where .= ")";
                    
                    $what = "('" . $nickname . "','" . password_hash($password, PASSWORD_DEFAULT) . "','" . $code . "'";
                    if (strlen($email) != 0) {
                        $what .= ", '" . strtolower($email) . "'";
                    }
                    if (strlen($location) != 0) {
                        $what .= ", '" . $location . "'";
                    }
                    $what .= ")";
                    
                    //sending INSERT message
                    $res = insert($where, $what);
                    if ($res != $DB_ERROR)
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
                        direct_to("message_send.php");
                    }
                    else {
                        $fatal_error = $insert_error;
                    }
                }
            }
            else {
                $fatal_error = $select_error;
            }
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == $OK, $user_nickname);
    ?>
    
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
                <input type="submit" name="submit" value="Submit">  
                <?php
                    form_error_section($fatal_error, $ok_error, $suc_message);
                ?>
                <p>* Required fields</p>
            </form>
        </section>
    </section>
    
</body>