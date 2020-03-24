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
    
        $check_res = check_user_cookie();
        
        $end_code['class'] = 1;
        $end_code['message'] = '';
        
        if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $end_code['class'] = "fatal_error";
            $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email_or_nickname = test_input($_POST["email_or_nickname"]);
            $password = test_input($_POST["password"]);
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
                    $end_code['class'] = "ok_error";
                    $end_code['message'] = "You are already logged!"; 
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
                                    $end_code['class'] = "ok_error";
                                    $end_code['message'] = "Wrong password!"; 
                                }
                            }
                            else {
                                $end_code['class'] = "ok_error";
                                $end_code['message'] = "This user does not exists!"; 
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
                echo '<p class="' . $end_code['class'] . '">' . $end_code['message'] . '</p>';
            ?>
        </section>
    </section>
</body>