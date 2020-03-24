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
        
        $code_input = $user_nickname = $email = $code = "";
        $id = -1;
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $code_input = test_input($_POST["code"]);
        }
        
        $check_res = check_user_cookie();
        
        if($check_res == 1) {
            $id = get_id($_COOKIE["LibChangeCookie"]);
            $email = get_email($id);
            $user_nickname = get_nickname($id);
            $code = get_code($id);
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
            <a href="../index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
            <?php
                if ($user_nickname != 0)
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
                
            ?>
    </section>
    <section class="main">
        <?php
            if($code == "-1") {
                echo '
                    <h3>Your email is already comfirmed!</h3>
                ';
            }
            else if ($id == -1) {
                echo '
                    <h3>Please log in!</h3>
                ';
            }
            else if ($code_input == $code){
                update("myusers", "email_confirmed=-1", "id=" . $id);
                
                $sql =  "
                    UPDATE `myusers`
                    SET `email_confirmed`=-1
                    WHERE `id` = " . $id . "
                ";
                
                echo '
                    <h3>Your email is comfirmed now!</h3>
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
                        <input type="submit" name="submit" value="Submit"> 
                    </form>
                ';
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    echo '
                        <h3>Code is incorrect!</h3>
                    ';
                }
            }
        ?>
    </section>
</body>