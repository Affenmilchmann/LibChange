<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="images/favicon.png" type="image/png">
        <title>Request</title>
    </head>
<body class="glob">
        <?php
            include 'funcs.php';
			include 'constants_and_errors.php';
            //bubble_sort(10000);
            
            $user_id = -1;
            $user_nickname = "NULL";
			$email_confirmed = "99999";
			
			$req_title = "";
			$reqestor_nickname = "";
			$req_comment = "";
			$req_date = "";
			$req_location = "";
			$req_responded = "-1";
			
			$responder_id = -1;
			$responder_nickname = "Somebody";
			$responder_exists = false;
            
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

			//getting request info from reqest id
			if (is_numeric($_GET["id"]) and $_GET["id"] != "") {
				$res = select('*', "requests", "id = " . test_input($_GET["id"]));
				if ($res == $EMPTY_ANSWER) {
					direct_to("index.php");
					return;
				}
				if ($res == $DB_ERROR)
					$fatal_error = $select_error;
				else {
					$req_title = str_replace("_", " ", $res['title']);
					$reqestor_nickname = get_user_info($res['req_id'])['nickname'];
					$req_comment = str_replace("_", " ", $res['comment']);
					$req_date = $res['date'];
					$req_location = get_location($res['country_id'], $res['city_id']);
					$req_responded = $res['response'];

					$responder_id = $res['ans_id'];
					$responder_nickname = select("nickname", "myusers", "id=" . $responder_id);
					if ($responder_nickname != $DB_ERROR) {
						$responder_nickname = $responder_nickname['nickname'];
						$responder_exists = true;
					}
					else
						$fatal_error = $select_error;
				}
			}
			else {
				direct_to("index.php");
			}
        ?>
		<section class="main">
			<p class="mini_heading">Title</p>
			<p><?php echo $req_title?></p>
			
			<p class="mini_heading">Comment</p>
			<p><?php echo $req_comment?></p>
						
			<p class="mini_heading">Location</p>
			<p><?php echo $req_location?></p>
			
			<p class="mini_heading">Nickname</p>
			<p><?php echo $reqestor_nickname?></p>
			
			<p class="mini_heading">Date</p>
			<p><?php echo $req_date?></p>
			
			<?php
			if ($req_responded == "-1") {
			?>
				<a href="respond_request.php?id=<?php echo test_input($_GET["id"])?>">
					<button> Respond </button>
				</a>
			<?php 
			}
			else {
				if ($responder_exists) {
			?>
					<h3>
						<a href="profile.php?id=<?php echo $responder_id?>">
							<?php echo $responder_nickname?>
						</a>
							already responded!
					</h3>
			<?php
				}
				else {
			?>
					<h3>Somebody already responded!</h3>
			<?php
				}
			?>
			<a href="/responds/<?php echo $req_responded ?>">
				<button>Get file</button>
			</a>
			<?php
			}
			?>
			<?php form_error_section($fatal_error, $ok_error, $suc_message); ?>
		</section>
</body>