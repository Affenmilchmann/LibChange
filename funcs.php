<?php  
    include 'constants_and_errors.php';

    /*function bubble_sort($len) {
            $time = time();
            
            for ($i = 0; $i < $len; $i++) {
                $array[$i] = rand(10000, 99999);
            }
            
            
            for ($i = 0; $i < $len; $i++) {
                for ($j = 0; $j < $len - $i - 1; $j++) {
                    if ($array[$j] < $array[$j + 1]) {
                        $t = $array[$j];
                        $array[$j] = $array[$j + 1];
                        $array[$j + 1] = $t;
                    }
                }
            }
            
            echo "Array of " . $len . " elements sorted in " . (time() - $time) . " sec with simple Bubble sort";
    }*/
    
    /*function load_image($file, $id) {
        $target_dir = "images/user_avatars/";
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $target_file = $target_dir . $id . "." . $imageFileType;
        
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        
        // Check if file already exists
        if (file_exists($target_dir . $id . ".png" )) {
            unlink($target_dir . $id . ".png");
        }
        if (file_exists($target_dir . $id . ".jpg" )) {
            unlink($target_dir . $id . ".jpg");
        }
        if (file_exists($target_dir . $id . ".jpeg" )) {
            unlink($target_dir . $id . ".jpeg");
        }
        
        // Check file size
        if ($file["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG & PNG files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            
        // if everything is ok, try to upload file
        } 
        else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                echo "The file ". basename($file["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }*/
    function send_email($text, $email) {
        include "constants_and_errors.php";
        
        $message = "
        <html>
            <head>
                <title>LibChange</title>
            </head>
            <body> ".
                $text
            ."</body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers['Ver']  = 'MIME-Version: 1.0';
        $headers['Type'] = 'Content-type: text/html; charset=iso-8859-1';
        
        // Дополнительные заголовки
        $headers['From'] = "From: support@libchange.ru \n";
        
        //echo $message . "<br>" . implode("\r\n", $headers) . "<br>" . $email . "<br>";
        
        return mail($email, "LibChange support", $message, implode("\r\n", $headers));
    }
    
    function form_hat($is_logged, $user_nickname) {
        echo 
        '
        <section class="top">
            <section class="main_text_box">
                <a href="index.php">
                    <img src="images/Logo.png" alt="logo" width="200">
                </a>
            </section>
            ';
                if (!$is_logged)
                {
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
                else {
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
                

            echo '
        </section>
        ';
    }
    
    function form_error_section($fatal_error, $ok_error, $suc_message) {
        if ($fatal_error != "")
            ?> <p class="fatal_error"> <?php echo $fatal_error ?> </p> <?php
            
        if ($ok_error != "")
            ?> <p class="ok_error"> <?php echo $ok_error ?> </p> <?php
            
        if ($suc_message != "")
            ?> <p class="success"> <?php echo $suc_message ?> </p> <?php
    }
    
    function direct_to($where) {
        header("Location: /" . $where);
    }
    
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = str_replace(' ', '', $data);
        return $data;
    }
    
    function text_test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = str_replace(' ', '_', $data);
        return $data;
    }
    
    function is_requests_amount_ok($user_ip) {
        include 'constants_and_errors.php';
                
        $data = select("first_request, req_amount", "ip_requests", '`ip`="' . $user_ip . '"');
        
        if ($data == $EMPTY_ANSWER) {
            
            $res = insert("`ip_requests`(`ip`)", "('" . $user_ip . "')");
            
            if ($res == $DB_ERROR) return $DB_ERROR;
            
            return true;
        }
        else if ($data == $DB_ERROR) {
            return $DB_ERROR;
        }
        
        $res = update("ip_requests", "req_amount = req_amount + 1", '`ip`="' . $user_ip . '"');
        if ($res == $DB_ERROR) return $DB_ERROR;
        
        //first request time
        $date = $data['first_request'];
        //current time
        $curr = date_create(date('o-m-j H:i:s'));
        //formatting sql time
        $date = date_create($date);
        //calculating difference in seconds
        $diff = $curr->getTimestamp() - $date->getTimestamp();
        
        /*echo $MAX_REQUESTS_INTERVAL . "<br>";
        echo $diff . "<br>";*/
        
        //if time is expiered, than reset
        if ($diff > $MAX_REQUESTS_INTERVAL) {
            
            $res = delete("ip_requests", "`ip`='" . $user_ip . "'");
            
            if ($res == $DB_ERROR) return $DB_ERROR;
            
            return true;
        }
        else if ($data['req_amount'] < $MAX_REQUESTS_AMOUNT) {
            return true;
        }
        else return false;
    }
    
    function global_check($str, $allow_str) {
        $is_good = true;
        
        for ($i = 0; $i < strlen($str); $i++) {
            if (strpos($allow_str, $str[$i]) == false) {
                $is_good = false;
            }
        }
        
        return $is_good;
    }
    
    function check_password($password) {
        $allowed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_#*!";
        
        return global_check($password, $allowed);
    }
    
    function check_nickname($nickname) {
        $allowed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
        
        return global_check($nickname, $allowed);
    }
    
    function check_text($str) {
        $allowed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!,.+-=&_ ";
        
        return global_check($str, $allowed);
    }
    
    function getKey($n) { 
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 
      
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
      
        return $randomString;
    } 
    
    function set_log_cookie($user_id) {
        $key = getKey(20);
        setcookie("LibChangeCookie", $key, time() + (86400), "/");
        
        //delleting cookies from DB
        $res = delete("cookies", "user_id = '" . $user_id . "'");
                                    
        //creating cookie in DB
        insert("`cookies`(`user_id`, `cookie`, `ip`)", "(" . $user_id . ", '"  . $key . "', '" . $_SERVER['REMOTE_ADDR'] . "')");
    }
    
    //comparing ip and cookie existance
    function check_user_cookie() {
        include 'constants_and_errors.php';
        
        if (isset($_COOKIE["LibChangeCookie"])) {
            $res = select("ip", "cookies", "cookie='" . $_COOKIE["LibChangeCookie"] . "'");
            
            if ($res != $EMPTY_ANSWER and $res != $DB_ERROR) {
                if ($res['ip'] == $_SERVER['REMOTE_ADDR']) {
                    return $OK; //all ok
                }
                else return $IP_CONFLICT; //ip conflict
            }
            else if ($res == $EMPTY_ANSWER) {
                delete_user_cookie("LibChangeCookie");
                return $EMPTY_ANSWER; //cookie missing on server. cookie deleted from user side
            }
            else return $DB_ERROR; //sql error
        }
        else return $COOKIE_NOT_SET; //cookie is not set
    }
    
    function set_conn() {
        $login_file_name = "login.txt";
        $login_file = fopen($login_file_name, "r");
        $login_data = explode("\n", fread($login_file, filesize($login_file_name)));
        fclose($login_file);
    
        $servername = "localhost";
        $username = $login_data[0];
        $sql_password = $login_data[1];
        
        $conn = new mysqli($servername, $username, $sql_password);
        
        $sql = "USE lenatihonovavskr";
        
        if (!($result = mysqli_query($conn, $sql))) {
            echo "ERROR: " . mysqli_error($conn); 
            return false;
        }
        else {
            return $conn;
        }
    }
    
    function check_server_cookie($conn, $cookie_value) {
        if(isset($_COOKIE["LibChangeCookie"])) {
            return $_COOKIE["LibChangeCookie"] == $cookie_value;
        }
    }
    
    function delete_user_cookie($name) {
        setcookie($name, null, -1, '/');
    }
    
    // basic functions
    
    function insert($where, $what) {
        $conn = set_conn();
        
        $sql = "INSERT INTO " . $where . " 
                VALUES " . $what;
            
        //echo $sql . "<br>";
        
        $result = mysqli_query($conn, $sql);
        
        if ($result == false) {
            echo "ERROR: " . mysqli_error($conn) . "<br>";
        }
        
        if ($conn != false) {
            $conn->close();
        }
        
        return $result;
    }
    
    function delete($from, $where) {
        $conn = set_conn();
        
        $sql = "DELETE FROM " . $from .  " 
                WHERE " . $where;
        
        $result = mysqli_query($conn, $sql);
        
        if ($conn != false) {
            $conn->close();
        }
        
        return $result;
    }
    
    function update($table, $what, $where) {
        $conn = set_conn();
        
        $sql =  "
                    UPDATE " . $table . "
                    SET " . $what . "
                    WHERE " . $where . "
                ";
        
        $result = mysqli_query($conn, $sql);
        
        if ($conn != false) {
            $conn->close();
        }
        
        return $result;
    }
    
    function select($what, $from, $where) {
        include "constants_and_errors.php";
        
        $conn = set_conn();
        
        $sql = "SELECT " . $what . " 
                FROM " . $from . " 
                WHERE " . $where;
        
        //echo $sql . "<br>";
        
        if($result = mysqli_query($conn, $sql)) {
            if ($conn != false) {
                $conn->close();
            }
            
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_array($result);
                
                return $row;
            }
            else return $EMPTY_ANSWER;
        }
        else {
            echo "ERROR: " . mysqli_error($conn) . "<br>"; 
            if ($conn != false) {
                $conn->close();
            }
            return $DB_ERROR;
        }
    }
    
    //high functions
    function get_user_info($user_id) {
        $info = select("*", "myusers", "id=" . $user_id);
        
        return $info;
    }
    
    function get_id($cookie) {
        include "constants_and_errors.php";
        
        $res = select("user_id", "cookies", "cookie='" . $_COOKIE["LibChangeCookie"] . "'");
        
        if ($res == $EMPTY_ANSWER) {
            delete_user_cookie("LibChangeCookie");
        }
        
        return $res['user_id'];
    }
    
    function global_get($id, $what) {
        include "constants_and_errors.php";
        
        $res = select($what, "myusers", "id='" . $id ."'");
        
        if ($res == 0) {
            return $EMPTY_ANSWER;
        }
        else if ($res != -1) {
            return $res[$what];
        }
        else {
            echo "Error: Your profile data is missing. Please contact support@libchange.ru";
            return $DB_ERROR;
        }
    }
    
    function get_location($user_id) {
        $ans = global_get($user_id, "city");
        
        if ($ans == "") {
            return "Is not set.";
        }
        
        return global_get($user_id, "city");
    }
    
    function get_question($user_id) {
        return global_get($user_id, "question");
    }
    
    function get_answer_hash($user_id) {
        return global_get($user_id, "answer");
    }
    
    function get_password_hash($user_id) {
        return global_get($user_id, "password");
    }
    
    function get_fin_requests($user_id) {
        return global_get($user_id, "fin_requests");
    }
    
    function get_ruined_requests($user_id) {
        return global_get($user_id, "ruined_requests");
    }
    
    function get_nickname($user_id) {
        return global_get($user_id, "nickname");
    }
    
    function get_email($user_id) {
        return global_get($user_id, "email");
    }
    
    function get_code($user_id) {
        return global_get($user_id, "email_confirmed");
    }
?>