<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Logout</title>
</head>
<body class="glob">
    <?php
        include 'funcs.php';
        
        if(isset($_COOKIE["LibChangeCookie"])) {
            
            //deletting it from DB
            if (!delete("cookies", "cookie='" . $_COOKIE["LibChangeCookie"] . "'")) {
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
            }
            
            //deletting cookie from browser
            delete_user_cookie("LibChangeCookie");
        }
        direct_to("index.php");

    ?>
    <section class="top">
        <section class="main_text_box">
            <a href="index.php">
                <img src="images/Logo.png" alt="logo" width="200">
            </a>
        </section>
    </section>
    <section class="main">
        
        <h3>You have logged out!</h3>
    </section>
</body>