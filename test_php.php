<?php
    include "funcs.php";
    include 'constants_and_errors.php';
    /*$date = select("first_request", "ip_requests", '`ip`="37.190.63.10"')['first_request'];
    $curr = date_create(date('o-m-j H:i:s'));
    
    $date = date_create($date);
    
    echo date_format($date, 'o-m-j H:i:s') . "<br>";
    echo date_format($curr, 'o-m-j H:i:s') . "<br>";
    
    echo date_diff($date, $curr)->format('%y-%m-%d %H:%i:%s');*/
    
    echo is_requests_amount_ok("0.0.0.0");
    
    
?>