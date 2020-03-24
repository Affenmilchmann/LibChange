<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="images/favicon.png" type="image/png">
        <title>LibChange</title>
    </head>
    <body class="glob">
        <?php
            //echo dirname(__FILE__); 
            include 'funcs.php';
            //bubble_sort(10000);
            
            $is_owner = false;
            
            $user_id = -1;
            $user_nickname = "NULL";
            
            $owner_nickname = "NULL";
            
            $user_email = "NULL";
            $user_location = "NULL";
            $user_helps = "NULL";
            $user_ruins = "NULL";

            $check_res = check_user_cookie();

            if($check_res == 1) {
                $user_id = get_id($_COOKIE["LibChangeCookie"]);
                
                $owner_nickname = get_nickname($user_id);
                
                $user_nickname = get_nickname($user_id);
                $user_email = get_email($user_id);
                $user_location = get_location($user_id);
                $user_helps = get_fin_requests($user_id);
                $user_ruins = get_ruined_requests($user_id);
                $email_confirm = get_code($user_id);
                
                $is_owner = true;
            }
            else if ($check_res == 2) {
                header("Location: /ip_conflict.php");
            }
            else if ($check_res == -1) {
                $end_code['class'] = "fatal_error";
                $end_code['message'] = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
            }
            
            if($_SERVER["REQUEST_METHOD"] == "GET") {
                $temp_id = select("id", "myusers", "nickname='" . $_GET['nickname'] . "'");

                if ($temp_id == -1) {
                    $end_code['class'] = "fatal_error";
                    $end_code['message'] = "DB Error. While selecting get request data. Please contact support@libchange.ru";
                }
                else if ($temp_id != 0) {
                    $temp_id = $temp_id['id'];
                    if (strtolower(get_nickname($temp_id)) != strtolower($user_nickname)) {
                        $is_owner = false;
                        $user_nickname = get_nickname($temp_id);
                        $user_location = get_location($temp_id);
                        $user_helps = get_fin_requests($temp_id);
                        $user_ruins = get_ruined_requests($temp_id);
                    }
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
                if ($owner_nickname == "NULL")
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
                                <a href ='profile.php' class='top_button' style='padding:0;'>
                                    <p class='logged'>" . $owner_nickname . " </p>
                                </a>
                            </section> 
                    
                    ";
                }
                
            ?>
        </section>
        
        
        <?php 
            if(isset($_COOKIE["LibChangeCookie"]) and $user_id != -1 or !$is_owner) {
                if ($user_nickname == "NULL") {
                    header("Location: /error/404.php");
                }
                
                if($_SERVER["REQUEST_METHOD"] == "POST") {
                    $new_location = test_input($_POST["new_location"]);
                    update("myusers", "city='" . $new_location . "'", "id='" . $user_id . "'");
                    header(htmlspecialchars("Location: " . $_SERVER["PHP_SELF"]));
                }
                
                echo'<section class="main_profile">' .
                        '</div>
                            <h2>' . $user_nickname . '</h2>';
                            if ($email_confirm != -1 and $is_owner) {
                                echo '
                                <h3 style="color:brown">Your email is not confirmed! You can not request!</h3>
                                <a href="confirm.php">
                                    <button>Confirm</button>
                                </a>
                                ';
                            }
                            if ($is_owner) {
                        echo    '
                                <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '"> 
                                    <h4 class="mini_heading">Location:</h4>
                                    <input type="text" name="new_location" value="' . $user_location . '"> 
                                    <input type="submit" name="submit" value="Change location"> 
                                </form>
                                ';
                            }
                            else {
                        echo    '
                                <h4 class="mini_heading">Location:</h4>
                                <p>'. $user_location .'</p>
                                ';
                            }
                            
                    echo    '
                            <h4 class="mini_heading">Rating*:</h4>
                            <h3>' . strval($user_helps - 2 * $user_ruins) . '</h3>
                            <p>Finished requests: <span class="green_span">' . $user_helps . '</span></p>
                            <p>Ruined requests: <span class="red_span">' . $user_ruins . '</span></p>
                            <p>* = (<span class="green_span">finished requests</span>) - 2 x (<span class="red_span">ruined requests</span>)</p>
                            ';
                            if ($is_owner) {
                        echo    '
                                <h4 class="mini_heading">Password:</h4>
                                <a href="change_password.php">
                                    <button>Change password</button>
                                </a>
                                ';
                            }
                            
                    echo    '</div>
                        </section> ';
            }
            else {
                header("Location: /log.php");
            }
        ?>
    </body>
</html>