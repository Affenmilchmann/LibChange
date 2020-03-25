<?php
    //----------global vars
    $support_email = "support@libchange.ru";
    $password_min_len = 8;
    $nickname_min_len = 6;
    
    $max_location_or_title_length = 100;
    $comments_max_length = 1000;


    //----------message texts
    //      FATAL ONES
    $user_existanse_error = "Whoops! Looks like your profile has been deleted. Or there is a DB error while checking your cookie file. Please contact " . $support_email;
    $select_error = "Error. While selecting from DB. Please contact " . $support_email;
    $insert_error = "Error. While inserting into DB. Please contact " . $support_email;
    $update_error = "Error. While updating DB. Please contact " . $support_email;
    $cookie_select_error = "Error. While selecting you cookie file from DB. Please contact " . $support_email;
    
    //      OK ERRORS
    $wrong_password_error = "Password is incorrect!";
    //email confirm
    $wrong_code_error = "Code is incorrect!";
    $ok_user_existance_error = "This user does not exists!";
    $already_logged_error = "You are already logged!"; 
    //account restoring
    $question_empty_error = 'You have no question!';
    $incorrect_answer_error = 'Answer is incorrect.';
    $login_existance_error = 'We cant find your login :(';
    //registration
    $nickname_already_exists_error = 'This nickname already exists!';
    $nickname_format_error = "Only letters, digits and '_' allowed in nickname!";
    $email_empty_error = 'You must input your email!';
    $email_already_exists_error = 'This email already exists!';
    $email_format_error = 'Email format is invalid.';
    $short_nickname_error = 'Your nickname must contain at least ' . $nickname_min_len . ' characters';
    $short_password_error = "Password must contain at least " . $password_min_len . " characters.";
    $text_format_error = 'Only latin letters, digits, space and "?!,.+-=&" are allowed';
    $empty_answer_error = 'You must input an answer!';
    $password_dismatch_error = "Passwords do not match!";
    //request making
    $location_empty_error = "Location field is required!"; 
    $title_empty_error = "Title field is required!"; 
    $location_or_title_lenght_error = "Location and title max lenght is " . $max_location_or_title_length . "! Your message lenght is ";
    $comment_lenght_error = "Comments max lenght is " . $comments_max_length . "! Your message lenght is ";

    
    //      SUCCESS ONES
    $password_changed_message = "Password had been changed";
    $email_confirmed_message = "Your email is comfirmed now!";
    $password_sent_message = 'Password sent to your mail.';
?>