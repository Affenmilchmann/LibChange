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
        
        $fatal_error = '';
        $ok_error = '';
        
        $check_res = check_user_cookie();
            
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
            $fatal_error = "DB Error. While checking you cookie file. Please contact support@libchange.ru"; 
        }
        else if ($check_res == 0 or $check_res == 3) {
            header("Location: /log.php");
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $location = text_test_input($_POST["location"]);
            $title = text_test_input($_POST["title"]);
            $comments = text_test_input($_POST["comments"]);
            
            if (strlen($location) == 0) {
                $ok_error = "Location field is required!"; 
            }
            else if (strlen($title) == 0) {
                $ok_error = "Title field is required!"; 
            } 
            else if (strlen($location) > 100) {
                $ok_error = "Location max lenght is 100! Your message lenght is " . strlen($location); 
            }
            else if (strlen($title) > 100) {
                $ok_error = "Title max lenght is 100! Your message lenght is " . strlen($title); 
            }
            else if (!check_text($location) or !check_text($title) or !check_text($comments)) {
                $ok_error ='Only latin letters, digits, space and "?!,.+-=&" are allowed'; 
            }
            else {
                $res = insert("`requests`(`location`, `title`, `comment`)", "('" . $location . "', '" . $title . "', '" . $comments . "')");
                if ($res == false) {
                    $fatal_error = "Error. While inserting data into DB. Please contact support@libchange.ru";
                }
            }
        }
    ?>
    
    <section class="top">
        <section class="main_text_box">
            <a href="index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
        <?php
            if(isset($_COOKIE["LibChangeCookie"]) and ($user_nickname != "NULL")) {
                echo    "
                        <section class='main_button_box'>
                            <a class='top_button' href='logout.php'>
                                <button>Logout</button>
                            </a>
                            <a href ='profile.php'>
                                <p class='logged'>" . $user_nickname . " </p>
                            </a>
                        </section> 
                
                ";
            }
            else {
                delete_user_cookie("LibChangeCookie");
                echo    "
                        <section class='main_button_box'>
                            <a class='top_button' href='log.php'>
                                <button>Login</button>
                            </a>
                            <a class='top_button' href='reg.php'>
                                <button>Registration</button>
                            </a>
                        </section>
                        ";
            }
        ?>
    </section>
    
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
                if ($fatal_error != '') echo '<p class="fatal_error">' . $fatal_error . '</p>';
                if ($ok_error != '') echo '<p class="ok_error">' . $ok_error . '</p>';
            ?>
        </section>
    </section>
</body>            