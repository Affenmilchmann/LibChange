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
            include 'funcs.php';
            //bubble_sort(10000);
            
            $user_id = -1;
            $user_nickname = "NULL";
            
            $end_code['class'] = 1;
            $end_code['message'] = "";
            
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
        ?>
        <section class="top">
            <section class="main_text_box">
                <a href="index.php">
                    <img src="images/Logo.png" alt="logo" width="200">
                </a>
            </section>
            <?php
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
                
            ?>
        </section>
        
        <section>
            <div>
                <section class="left_menu">
                    <h3 class="mini_main_heading">Make a request.</h3>
                    <?php
                    if ($user_nickname != "NULL") {
                        echo '
                        
                        <a href="request.php">
                            <button>Request!</button>
                        </a>
                        
                        ';
                    }
                    else {
                       echo 'You need an account to request!'; 
                    }
                    ?>
                </section>
                
                <section class="middle_menu">
                <table style="width:100%">
                    <tr>
                        <th>Location</th>
                        <th>Title</th>
                    </tr>
                    <tr>
                        <td>Germany, Köln</td>
                        <td>Der Kunst ist gut, Hans Stern, 2034</td>
                    </tr>
                    <tr>
                        <td>Russia, Voronezh</td>
                        <td>Исскуство это хорошо, Иван Спичкин, 2034</td>
                    </tr>
                    <tr>
                        <td>USA, Washingtin</td>
                        <td>The art is good, John Gates, 2034</td>
                    </tr>
                </table>
                </section>
                <section class="right_menu">Right!</section>
            </div>
        </section> 
        <footer>
            <?php
                /*if(isset($_COOKIE["LibChangeCookie"])) {
                    echo "Cookie is set! " . $_COOKIE["LibChangeCookie"] .  "<br>";   
                }
                else {
                    echo "Cookie is not set!<br>"; 
                }*/
            ?>
        </footer>
    </body>
</html>