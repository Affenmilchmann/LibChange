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
        else if ($check_res == 0 or $check_res == 3) {
            header("Location: /log.php");
        }
        
        if ($check_res == 1 and $_SERVER["REQUEST_METHOD"] == "POST") {
            $location = text_test_input($_POST["location"]);
            $title = text_test_input($_POST["title"]);
            $comments = text_test_input($_POST["comments"]);
            
            if (strlen($location) == 0) {
                $ok_error = $location_empty_error;
            }
            else if (strlen($title) == 0) {
                $ok_error = $title_empty_error;
            } 
            else if (strlen($location) > $max_location_or_title_length) {
                $ok_error = $location_or_title_lenght_error . strlen($location); 
            }
            else if (strlen($title) > $max_location_or_title_length) {
                $ok_error = $location_or_title_lenght_error . strlen($title); 
            }
            else if (strlen($comments) > 100) {
                $ok_error = $comment_lenght_error . strlen($comments); 
            }
            else if (!check_text($location) or !check_text($title) or !check_text($comments)) {
                $ok_error = $text_format_error;
            }
            else {
                $res = insert("`requests`(`location`, `title`, `comment`)", "('" . $location . "', '" . $title . "', '" . $comments . "')");
                if ($res == false) {
                    $fatal_error = $insert_error;
                }
            }
        }
        
        //making hat (aka 'shapka') html code
        form_hat($check_res == 1, $user_nickname);
    ?>
    
    <section class="main">
        <section>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p class="mini_heading"> Location*: </p>
                <input type="text" name="location">
                <p class="mini_heading"> Title*: </p>
                <input type="text" name="title">
                <p class="mini_heading"> Comments: (pages, chapters and ect.) </p>
                <textarea name="comments" rows=8 cols=40></textarea>
                <br>
                <input type="submit" name="submit" value="Submit">  
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