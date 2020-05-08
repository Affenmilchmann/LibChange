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
            
            $is_owner = false;
            
            $user_id = -1;
            $user_nickname = "NULL";
            
            $owner_nickname = "NULL";
            
            $user_email = "NULL";
            $user_location = "NULL";
            $user_helps = "NULL";
            $user_ruins = "NULL";

            //checking user cookie
            $check_res = check_user_cookie();
            
            //setting messages values
            $ok_error = "";
            $fatal_error = "";
            $suc_message = ""; 

            // $check_res codes are in /funcs.php
            if($check_res == $OK) {
                $user_id = get_id($_COOKIE["LibChangeCookie"]);
                
                $user_info = get_user_info($user_id);
                
                $owner_nickname = $user_info['nickname'];
                
                $user_nickname = $user_info['nickname'];
                $user_email = $user_info['email'];
                $user_location = $user_info['city'];
                $user_helps = $user_info['fin_requests'];
                $user_ruins = $user_info['ruined_requests'];
                $email_confirm = $user_info['email_confirmed'];
                
                $is_owner = true;
            }
            else if ($check_res == $IP_CONFLICT) {
                direct_to("ip_conflict.php");
            }
            else if ($check_res == $DB_ERROR) {
                $fatal_error = $cookie_select_error; 
            }
            
            if($_GET['nickname'] != "") {
                $temp_id = select("id", "myusers", "nickname='" . $_GET['nickname'] . "'");
                
                if ($temp_id == $DB_ERROR) {
                    $fatal_error = $select_error;
                }
                else if ($temp_id != $EMPTY_ANSWER) {
                    $temp_id = $temp_id['id'];
                    $user_info = get_user_info($temp_id);
                    
                    if ($user_info != $DB_ERROR) {
                    
                        if (strtolower($user_info['nickname']) != strtolower($user_nickname)) {
                            $is_owner = false;
                            $user_nickname = $user_info['nickname'];
                            $user_location = $user_info['city'];
                            $user_helps = $user_info['fin_requests'];
                            $user_ruins = $user_info['ruined_requests'];
                        }
                    }
                    else {
                        $fatal_error = $select_error;
                    }
                }
            }
            
            //making hat (aka 'shapka') html code
            form_hat($check_res == $OK, $owner_nickname);
        ?>
        
        <?php 
            if(isset($_COOKIE["LibChangeCookie"]) and $user_id != -1 or !$is_owner) {
                if ($user_nickname == "NULL" and $check_res != $IP_CONFLICT) {
                    direct_to("log.php");
                }
                
                if($_SERVER["REQUEST_METHOD"] == "POST") {
                    $new_location = test_input($_POST["new_location"]);
                    $upd_res = update("myusers", "city='" . $new_location . "'", "id='" . $user_id . "'");
                    
                    if ($upd_res == $DB_ERROR) {
                        $fatal_error = $update_error;
                    }
                    
                    header(htmlspecialchars("Location: " . $_SERVER["PHP_SELF"]));
                }
                
        ?>  
        <section class="main_profile">
            </div>
                <h2> <?php echo $user_nickname ?> </h2>
                
                <?php
                if ($email_confirm != -1 and $is_owner) {
                    ?>
                    
                    <h3 style="color:brown">Your email is not confirmed! You can not request!</h3>
                    <a href="confirm.php">
                        <button>Confirm</button>
                    </a>
                    
                    <?php
                }
                if ($is_owner) {
                    ?>
                    
                    <form method="post" action=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>"> 
                        <h4 class="mini_heading">Location:</h4>
                        <input type="text" name="new_location" value="<?php echo $user_location ?>"> 
                        <input type="submit" name="submit" value="Change location"> 
                    </form>
                    
                    <?php
                }
                else {
                    ?>
                    
                    <h4 class="mini_heading">Location:</h4>
                    <p> <?php echo $user_location ?> </p>
                    
                    <?php
                }
                
                ?>
                
                <h4 class="mini_heading">Rating*:</h4>
                <h3> <?php echo strval($user_helps - 2 * $user_ruins) ?> </h3>
                <p>Finished requests: <span class="green_span"> <?php echo $user_helps ?> </span></p>
                <p>Ruined requests: <span class="red_span"> <?php echo $user_ruins ?> </span></p>
                <p>* = (<span class="green_span">finished requests</span>) - 2 x (<span class="red_span">ruined requests</span>) </p>
                
                <?php
                if ($is_owner and $email_confirm == -1) {
                ?>
                
                    <h4 class="mini_heading">Password:</h4>
                    <a href="change_password.php">
                        <button>Change password</button>
                    </a>
                    
                <?php
                }
                
                ?> 
            </div>
                </section> 
            
            <?php
            }
            else {
                direct_to("log.php");
            }
            
            form_error_section($fatal_error, $ok_error, $suc_message);
            
        ?>
    </body>
</html>