<?php  
    function bubble_sort($len) {
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
    }
    
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
        return $data;
    }
    
    function check_nickname($nickname) {
        $allowed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
        $is_good = true;
        
        for ($i = 0; $i < strlen($nickname); $i++) {
            if (strpos($allowed, $nickname[$i]) == false) {
                $is_good = false;
            }
        }
        
        return $is_good;
    }
    
    function check_text($str) {
        $allowed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!,.+-=& ";
        $is_good = true;
        
        for ($i = 0; $i < strlen($str); $i++) {
            if (strpos($allowed, $str[$i]) == false) {
                $is_good = false;
            }
        }
        
        return $is_good;
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
        if (isset($_COOKIE["LibChangeCookie"])) {
            $res = select("ip", "cookies", "cookie='" . $_COOKIE["LibChangeCookie"] . "'");
            
            if (!is_int($res)) {
                if ($res['ip'] == $_SERVER['REMOTE_ADDR']) {
                    return 1; //all ok
                }
                else return 2; //ip conflict
            }
            else if ($res == 0) {
                delete_user_cookie("LibChangeCookie");
                return 0; //cookie missing on server. cookie deleted from user side
            }
            else return -1; //sql error
        }
        else return 3; //cookie is not set
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
            
        if ($DEBUG_MODE) echo $sql . "<br>";
        
        $result = mysqli_query($conn, $sql);
        
        if ($conn != false) {
            $conn->close();
        }
        
        return $result;
    }
    
    function delete($from, $where) {
        $conn = set_conn();
        
        $sql = "DELETE FROM " . $from .  " 
                WHERE " . $where;
                
        if ($DEBUG_MODE) { 
            echo $sql . "<br>";
        }
        
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
                
         
        if ($DEBUG_MODE) { 
            echo $sql . "<br>";
        }
        
        $result = mysqli_query($conn, $sql);
        
        if ($conn != false) {
            $conn->close();
        }
        
        return $result;
    }
    
    function select($what, $from, $where) {
        $conn = set_conn();
        
        $sql = "SELECT " . $what . " 
                FROM " . $from . " 
                WHERE " . $where;
        
        //echo $sql . "<br>";
        
        if ($DEBUG_MODE) { 
            echo $sql . "<br>";
        }
        
        if($result = mysqli_query($conn, $sql)){
            if ($conn != false) {
                $conn->close();
            }
            
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_array($result);
                
                return $row;
            }
            else return 0;
        }
        else {
            echo "ERROR: " . mysqli_error($conn) . "<br>"; 
            if ($conn != false) {
                $conn->close();
            }
            return -1;
        }
    }
    
    //high functions
    function get_id($cookie) {
        $res = select("user_id", "cookies", "cookie='" . $_COOKIE["LibChangeCookie"] . "'");
        
        if ($res != 0 and $res != -1) {
            return $res['user_id'];
        }
        else {
            delete_user_cookie("LibChangeCookie");
            return -1;
        }
    }
    
    function global_get($id, $what) {
        $res = select($what, "myusers", "id='" . $id ."'");
        
        if ($res == 0) {
            return 0;
        }
        else if ($res != -1) {
            return $res[$what];
        }
        else {
            echo "Error: Your profile data is missing. Please contact support@libchange.ru";
            return -1;
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