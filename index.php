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
            
            //checking user cookie
            $check_res = check_user_cookie();
            
            //setting messages values
            $ok_error = "";
            $fatal_error = "";
            $suc_message = ""; 
            
            // $check_res codes are in /funcs.php
            if($check_res == 1) {
                $user_id = get_id($_COOKIE["LibChangeCookie"]);
                $user_nickname = get_nickname($user_id);
                if ($user_id == -1 or $user_nickname == -1) {
                    $fatal_error = $select_error;
                }
                else if (is_int($user_nickname)) {
                    delete_user_cookie("LibChangeCookie");
                }
            }
            else if ($check_res == 2) {
                header("Location: /ip_conflict.php");
            }
            else if ($check_res == -1) {
                $fatal_error = $cookie_select_error;    
            }
            
            
            //making hat (aka 'shapka') html code
            form_hat($check_res == 1, $user_nickname);
        ?>
        
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
        <?php
            if ($fatal_error != "")
                echo '<p class="fatal_error">' . $fatal_error . '</p>';
                
            if ($ok_error != "")
                echo '<p class="ok_error">' . $ok_error . '</p>';
                
            if ($suc_message != "")
                echo '<p class="success">' . $suc_message . '</p>';    
        ?>
    </body>
</html>