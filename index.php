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
			$user_info;
			$kostil = false;
            
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
            
			
			if (!isset($_COOKIE["show_local_only"])) {
				setcookie("show_local_only", '0', time() + (31536000), "/"); 
			}
			
			if (isset($_GET["switch"]) and text_test_input($_GET["switch"]) != "") {
				if ($_COOKIE["show_local_only"] == '0')
					setcookie("show_local_only", '1', time() + (31536000), "/"); //31 536 000 - 1 year
				else
					setcookie("show_local_only", '0', time() + (31536000), "/"); 
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
				<?php 
				if ($check_res == $OK) {
				?>
				<a href="index.php?switch=1">
				<button>
					<?php 
					if ($_COOKIE["show_local_only"] == "0") 
						if (isset($_GET["switch"])) {
							echo "Show all requests";
							$kostil = false;
						}
						else {
							echo "Show local requests"; 
							$kostil = true;
						}
					else 
						if (isset($_GET["switch"])) {
							echo "Show local requests"; 
							$kostil = true;
						}
						else {
							echo "Show all requests";
							$kostil = false;
						}
					?>
				</button>
				</a>
				<?php
				}
				?>
                <table style="width:100%">
                    <tr>
                        <th>Location</th>
                        <th>Title</th>
                    </tr>
                    <?php
					$requests = select("city_id, country_id, title, id", "requests", "1", true);
					
					for ($i = 0; $i < count($requests); $i++) {
						if ($check_res != $OK or $kostil or ($requests[$i][0] == $user_info['city_id'] and $requests[$i][1] == $user_info['country_id'])) {
						?>
						<tr>
							<td><?php echo get_location($requests[$i][1], $requests[$i][0])?></td>
							<td><a href="request_page.php?id=<?php echo $requests[$i][3] ?>"><?php echo str_replace('_', ' ', $requests[$i][2], )?></a></td>
						</tr>
						<?php
						}
					}
					?>
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
