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
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $old_password = test_input($_POST["old_password"]);
                $new_password = test_input($_POST["new_password"]);
                $repeat_new_password = test_input($_POST["repeat_new_password"]);
                
                // $old_hash codes are in /funcs.php
                $old_hash = get_password_hash($user_id);
                
                if (is_int($old_hash) and $old_hash == -1) {
                    $fatal_error = $select_error;
                }
                if (is_int($old_hash) and $old_hash == 0) {
                    $fatal_error = $user_existanse_error;
                }
                else if (!password_verify($old_password, $old_hash)) {
                    $ok_error = $wrong_password_error;
                }
                else if (strlen($new_password) < $password_min_len) {
                    $ok_error = $short_password_error;
                }
                else if ($new_password != $repeat_new_password) {
                    $ok_error = $password_dismatch_error;
                }
                else {
                    //if all is ok then changing the password
                    $res = update("myusers", "password='" . password_hash($new_password, PASSWORD_DEFAULT) . "'", "id='" . $user_id . "'");
                    if (!is_int($res)) {
                        $suc_message = $password_changed_message;
                    }
                    else {
                        $fatal_error = $update_error;
                    }
                }
            }
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $fatal_error = $cookie_select_error;
        }
        else {
            //sending user to the login page if he is not logged and there is no cookie error
            header("Location: /log.php");
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == 1, $user_nickname);
    ?>
    
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