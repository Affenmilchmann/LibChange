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
        
        $user_nickname = "NULL";
        $user_id = -1;
        
        $end_code['class'] = 1;
        $end_code['message'] = '';
        
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
            if (is_int($user_nickname)) {
                delete_user_cookie("LibChangeCookie");
            }
        }
        else if ($check_res == 2) {
            header("Location: /ip_conflict.php");
        }
        else if ($check_res == -1) {
            $fatal_error = $cookie_select_error; 
        }
        
        if ($check_res == 1) {
            $email_or_nickname = test_input($_POST["email_or_nickname"]);
            
            $res = select("id", "myusers", "nickname='" . $email_or_nickname . "' OR email ='" . $email_or_nickname . "'");
            
            if ($res != -1 and $res != 0) {
                setcookie("LibChangeID", $res['id'], time() + (3600), "/"); 
                header("Location: /qna.php");
            }
            else if ($res == 0) {
                $ok_error = $login_existance_error;
            }
            else {
                $fatal_error = $insert_error;
            }
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == 1, $user_nickname);
    ?>
    
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