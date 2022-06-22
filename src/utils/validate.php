<?php

function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


function validateUserName($username){
    $usernameError = "";
    if(strlen($username) == 0){
         $usernameError = "username can't be empty";
    }
    return $usernameError;
}

function validateEmail($email){
    $emailErr = "";
    if(strlen($emailErr) == 0){
        $emailErr = "username can't be empty";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }
    return $emailErr;
}

function validatePassword($password){
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    $passwordErr = "";
    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        $passwordErr =  'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
    }
    return $passwordErr;
}

function validateFile($file){
    $mime_type = mime_content_type($file);
    $allowed_file_type = 'application/pdf';
    $fileUploadError = "";
    if ($allowed_file_type != $mime_type) {
        $fileUploadError = "only pdf file is allowed";
    }
    return $fileUploadError;
}

?>