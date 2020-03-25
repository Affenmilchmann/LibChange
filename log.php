<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
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
        if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $fatal_error = $cookie_select_error; 
        }
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email_or_nickname = test_input($_POST["email_or_nickname"]);
            $password = test_input($_POST["password"]);
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == 1, $user_nickname);
    ?>
    
    <section class="main">
        <section>
            <h2>Login</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <div>
                        Nickname/E-mail:
                    </div>
                    <div>
                        <input type="text" name="email_or_nickname" <?php echo "value='" . $nickname , "'"?>>
                    </div>
                </div>
                <br>
                <div>
                    <div>
                        Password:
                    </div>
                    <div>
                        <input type="password" name="password">
                    </div>
                </div>
                <br>
                <input type="submit" name="submit" value="Login">  
                
                <?php
                if ($check_res == 1) {
                    $ok_error = $already_logged_error;
                }
                else {
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        //searching for matches in DB
                        $res = select("password, id", "myusers", "nickname='" . $email_or_nickname . "' OR email ='" . $email_or_nickname . "'");
                        
                        $curr_user_id = $res['id'];
                        
                        if($res != -1){
                            if($res != 0) {
                                if (password_verify($password, $res['password'])) //is  password correct
                                {
                                    //deletting cookie from user
                                    delete_user_cookie("LibChangeCookie");
                                    
                                    set_log_cookie($curr_user_id);
                                    
                                    //sending user to the main page
                                    header("Location: /index.php");
                                }
                                else {
                                    $ok_error = $wrong_password_error;
                                }
                            }
                            else {
                                $ok_error = $ok_user_existance_error;
                            }
                        }
                    }
                }
                ?>
                
            </form>
            <br>
            <a href="/recovery.php">
                <button>I don`t remember my password :(</button>
            </a>
            <?php
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