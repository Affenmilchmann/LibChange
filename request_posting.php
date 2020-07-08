<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Post a request</title>
</head>
<body class="glob">
    
    <?php
        include 'funcs.php';
        
        $password_min_len = 8;
        $user_nickname = "NULL";
        $user_id = -1;
        
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
			$user_mail_confirmed = $user_info['email_confirmed'];
            if (is_int($user_nickname)) {
                delete_user_cookie("LibChangeCookie");
            }
        }
        else if ($check_res == $IP_CONFLICT) {
            direct_to("ip_conflict.php");
        }
        else if ($check_res == $DB_ERROR) {
            $fatal_error = $cookie_select_error; 
        }
        else if ($check_res == $COOKIE_NOT_SET) {
            direct_to("log.php");
        }
        
        if ($check_res == $OK and $_SERVER["REQUEST_METHOD"] == "POST") {
            $title = text_test_input($_POST["title"]);
            $comments = text_test_input($_POST["comments"]);
			
			$city = test_input($_POST["city"]);
			$country = test_input($_POST["country"]);
            
			if ($city == "") {
				$ok_error = $city_is_not_selected;
			}
			else if ($country != null and $city != null and !check_location_input($country, $city)) {
				$fatal_error = $location_check_error;
			}
			else if ($user_mail_confirmed != -1) {
				$ok_error = $email_not_confirmed_error;
			}
            else if (strlen($title) == 0) {
                $ok_error = $title_empty_error;
            } 
            else if (strlen($title) > $max_location_or_title_length) {
                $ok_error = $location_or_title_lenght_error . strlen($title); 
            }
            else if (strlen($comments) > 1000) {
                $ok_error = $comment_lenght_error . strlen($comments); 
            }
            else if (!check_text($title) or !check_text($comments)) {
                $ok_error = $text_format_error;
            }
            else {
                $res = insert("`requests`(`country_id`, `city_id`, `title`, `comment`, `req_id`)", "('" . $country . "', '" . $city . "', '" . $title . "', '" . $comments . "', " . $user_id . ")");
                if ($res == $DB_ERROR) {
                    $fatal_error = $insert_error;
                }
                else {
                    $suc_message = $request_posted_message;
                }
            }
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == $OK, $user_nickname);
    ?>
    
    <section class="main">
        <section>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p class="mini_heading"> Location*: </p>

				<div>
					Country:
				</div>
				<div>
					<select name="country" id="CountrySelect" onchange="countryChanged()">

					</select>
				</div>
				<br>
				<div>
					City:
				</div>
				<div>
					<select name="city" id="CitySelect">

					</select>
				</div>

                <br>
                <p class="mini_heading"> Title*: </p>
                <input type="text" name="title">
				
                <p class="mini_heading"> Comments: (pages, chapters and ect.) </p>
                <textarea name="comments" rows=8 cols=40></textarea>
				
                <br>
                <input type="submit" name="submit" value="Submit">  
            </form>
            <?php
                form_error_section($fatal_error, $ok_error, $suc_message);
            ?>
        </section>
    </section>
</body>            

<script>	
	var cities = <?php echo json_encode(select("*", "location_cities", "1", true));?>;
	//filling country list
	var countries = <?php echo json_encode(sorted_select('location_countries', "name"));?>;

	function countryChanged() {
		refillCitySelect(document.getElementById("CountrySelect").value, "CitySelect", cities);
	}
	
	fillCoutrySelect("CountrySelect", countries);
	
	countryChanged();
</script>