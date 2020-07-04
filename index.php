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
			$email_confirmed = "99999";
            
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
                
                $user_nickname = $user_info['nickname'];
				
				$email_confirmed = $user_info['email_confirmed'];
            }
            else if ($check_res == $IP_CONFLICT) {
                direct_to("ip_conflict.php");
            }
            else if ($check_res == $DB_ERROR) {
                $fatal_error = $cookie_select_error;    
            }
            
            
            //making hat (aka 'shapka') html code
            form_hat($check_res == $OK, $user_nickname);
        ?>
        
        <section>
            <div>
                <section class="left_menu">
                    <h3 class="mini_main_heading">Make a request.</h3>
                    <?php
                    if ($user_nickname == "NULL") {
						echo 'You need an account to request!'; 
                    }
                    else if ($email_confirmed != -1){
                       echo 'Your email is not confirmed!'; 
                    }
					else {
						echo '
                        
                        <a href="request_posting.php">
                            <button>Request!</button>
                        </a>
                        
                        ';
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
            form_error_section($fatal_error, $ok_error, $suc_message);   
        ?>
    </body>
</html>
