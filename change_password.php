<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Password change</title>
</head>
<body class="glob">
    
    <?php
        include 'funcs.php';
        
        $password_min_len = 8;
        
        $check_res = check_user_cookie();
        
        if($check_res == 1) {
            $user_id = get_id($_COOKIE["LibChangeCookie"]);
            $user_nickname = get_nickname($user_id);
            
            $end_code['class'] = 1;
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $old_password = test_input($_POST["old_password"]);
                $new_password = test_input($_POST["new_password"]);
                $repeat_new_password = test_input($_POST["repeat_new_password"]);
                
                $old_hash = get_password_hash($user_id);
                
                if (is_int($old_hash)) {
                    $end_code['message'] = "Error. While selecting password hash. DB returned " . $old_hash . ". Please contact support@libchange.ru";
                    $end_code['class'] = "fatal_error";
                }
                else if (!password_verify($old_password, $old_hash)) {
                    $end_code['message'] = "Current password is incorrect!";
                    $end_code['class'] = "ok_error";
                }
                else if (strlen($new_password) < $password_min_len) {
                    $end_code['message'] = "Password must contain at least " . $password_min_len . " characters.";
                    $end_code['class'] = "ok_error";
                }
                else if ($new_password != $repeat_new_password) {
                    $end_code['message'] = "New passwords do not match!";
                    $end_code['class'] = "ok_error";
                }
                else {
                    $res = update("myusers", "password='" . password_hash($new_password, PASSWORD_DEFAULT) . "'", "id='" . $user_id . "'");
                    if (!is_int($res)) {
                        $end_code['class'] = "success";
                        $end_code['message'] = "Password had been changed";
                    }
                    else {
                        $end_code['message'] = "Error. While updating DB. BD returned " . $res . ". Please contact support@libchange.ru";
                        $end_code['class'] = "fatal_error";
                    }
                }
            }
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $end_code['class'] = "fatal_error";
            $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
    ?>
    
    <section class="top">
        <section class="main_text_box">
            <a href="index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
        <?php
            if(isset($_COOKIE["LibChangeCookie"])) {
                if ($user_nickname == "NULL")
                {
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
                else {
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
            }
            else {
                header("Location: /log.php");
            }
            ?>
    </section>
    
    <section class="main">
        <section>
            <h2>Change password</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <div>
                        Current password:
                    </div>
                    <div>
                        <input type="password" name="old_password">
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        New password:
                    </div>
                    <div>
                        <input type="password" name="new_password">
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Repeat new password:
                    </div>
                    <div>
                        <input type="password" name="repeat_new_password">
                    </div>
                </div>
                <br>
                <input type="submit" name="submit" value="Change">  
                <?php
                    echo '<p class="' . $end_code['class'] . '">' . $end_code['message'] . '</p>';
                ?>
            </form>
            <br>
            <a href="/recovery.php">
                <button>I don`t remember my password :(</button>
            </a>
        </section>
    </section>
</body>