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
        
        $code_input = $user_nickname = $email = $code = 0;
        $id = 0;
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $code_input = test_input($_POST["code"]);
        }
        
        //checking user cookie
        $check_res = check_user_cookie();
        
        //setting messages values
        $ok_error = "";
        $fatal_error = "";
        $suc_message = "";   
        
        // $check_res codes are in /funcs.php
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
        <?php
            if ($code_input == $code){
                $res = update("myusers", "email_confirmed=-1", "id=" . $id);
                
                if ($res != false) {
                    $suc_message = $email_confirmed_message;
                }
                else {
                    $fatal_error = $update_error;
                }
            }
            else if($code == "-1") {
                echo '
                    <h3>Your email is already comfirmed!</h3>
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
                    $ok_error = $wrong_code_error;
                }
            }

            if ($fatal_error != "")
                echo '<p class="fatal_error">' . $fatal_error . '</p>';
                
            if ($ok_error != "")
                echo '<p class="ok_error">' . $ok_error . '</p>';
                
            if ($suc_message != "")
                echo '<p class="success">' . $suc_message . '</p>';
        ?>
    </section>
</body>