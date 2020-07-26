<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="images/favicon.png" type="image/png">
        <title>Send a respond</title>
    </head>
<body class="glob">
	<?php
		include 'funcs.php';
		include 'constants_and_errors.php';
		
		$user_id = -1;
		$user_nickname = "NULL";
		$email_confirmed = "99999";
		$is_responsed = false;
		
		$req_title = "";
		$reqestor_nickname = "";
		$responder_nickname = "";
		$req_location = "";
		
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
			
			//user can not answer without mail connected
			if ($email_confirmed != -1)
				$ok_error = $email_not_confirmed_error;
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
				if ($res['ans_id'] != -1) {
					$responder_nickname = select("nickname", "myusers", "id=" . $res['ans_id']);
					if ($responder_nickname != $DB_ERROR) 
						$responder_nickname = $responder_nickname['nickname'];
					else
						$fatal_error = $select_error;
				}
				$req_location = get_location($res['country_id'], $res['city_id']);
				
				$is_responsed = $res['response'];
			}
		}
		else {
			direct_to("index.php");
		}
		
		//uploading image
		if ($_SERVER["REQUEST_METHOD"] == "POST" and $email_confirmed == -1 and isset($_FILES['userfile']) and $is_responsed == "-1" and $check_res == $OK) {
			$extension = explode('.', $_FILES['userfile']['name'])[1];
			
			if ($extension != "jpeg" and $extension != "jpg" and $extension != "png" and $extension != "zip") {
				$ok_error = $wrong_extension;
			}
			else if ($_FILES['userfile']['size'] > 10485760) { //10MB
				$ok_error = $file_too_large;
			}
			else {
				$uploaddir = 'E:\\XAMPP\\htdocs\\responds\\';
				$uploadfile = $uploaddir . $_GET["id"] . '.' . $extension;

				if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
					$res = update("requests", "response = '" . $_GET["id"] . '.' . $extension . "', " . "ans_id = " . $user_id, "id = " . $_GET["id"]);
					if ($res == $DB_ERROR)
						$fatal_error = $update_error;
					else {
						$suc_message = $file_uploaded;
						$is_responsed = $_GET["id"] . '.' . $extension;
					}
					
				} else {
					$fatal_error = $file_load_error . $_FILES['userfile']['error'];
				}
			}
		}
	?>
	<section class="main">
	<p class="mini_heading">You are respondong to:</p>
	<p><?php echo $reqestor_nickname?></p>
	
	<p class="mini_heading">Material title:</p>
	<p><?php echo $req_title?></p>
				
	<p class="mini_heading">Location:</p>
	<p><?php echo $req_location?></p>
	
	
	
	<?php 
	if ($email_confirmed == -1 and $is_responsed == "-1") {
	?>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']?>" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		Your file: <input name="userfile" type="file" />
		<input type="submit" value="Upload" />
	</form>
	<br>
	<p>If you have single image, then upload it in '.jpg' or '.png'.</p>
	<p>If you have multiple images, then put them into '.zip' folder and upload it.</p>
	<p>Max size is 10MB.</p>
	<?php
	}
	
	if ($is_responsed != '-1') {
	?>
	<h3><?php echo $responder_nickname?> already responded!</h3>
	<a href="/responds/<?php echo $is_responsed ?>">
		<button>Get file</button>
	</a>
	<?php
	}
	
	if (!isset($_FILES['userfile']) and $_SERVER["REQUEST_METHOD"] == "POST")
		$fatal_error = "Looks like you have uploaded too big file. Or maybe our site had crashed :)";
	form_error_section($fatal_error, $ok_error, $suc_message);
	?>
	</section>
</body>