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
        
        delete_user_cookie("LibChangeID");
        
        $password_min_len = 8;
        
        $user_nickname = "NULL";
        $user_id = -1;
        
        $end_code['class'] = 1;
        $end_code['message'] = '';
        
        $check_res = check_user_cookie();
        
        if($check_res == 1) {
            $user_id = get_id($_COOKIE["LibChangeCookie"]);
            $user_nickname = get_nickname($user_id);
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $end_code['class'] = "fatal_error";
            $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email_or_nickname = test_input($_POST["email_or_nickname"]);
            
            $res = select("id", "myusers", "nickname='" . $email_or_nickname . "' OR email ='" . $email_or_nickname . "'");
            
            if ($res != -1 and $res != 0) {
                setcookie("LibChangeID", $res['id'], time() + (3600), "/"); 
                header("Location: /qna.php");
            }
            else if ($res == 0) {
                $end_code['class'] = "ok_error";
                $end_code['message'] = 'We cant find your login :(';
            }
            else {
                $end_code['class'] = "fatal_error";
                $end_code['message'] = 'Error. While inserting data into DB. Request code ' . $res . '. Please, contact support@libchange.ru';
            }
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
            <h2 class="mini_heading">Recovery</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <div>
                        Your Email or Nickname:
                    </div>
                    <div>
                        <input type="text" name="email_or_nickname">
                    </div>
                </div>
                <br>
                <input type="submit" name="submit" value="Next">  
            </form>
            <?php
                echo '<p class="' . $end_code['class'] . '">' . $end_code['message'] . '</p>';
            ?>
        </section>
    </section>
</body>