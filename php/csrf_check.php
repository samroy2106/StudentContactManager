<?php

function check_csrf($post_token) {

    //For backward compatibility with the hash_equals function.
    //This function was released in PHP 5.6.0.
    //It allows us to perform a timing attack safe string comparison.
    if(!function_exists('hash_equals')) {
        function hash_equals($str1, $str2) {
            if(strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        }
    }

    //Make sure that the token POST variable exists.
    if(!isset($_POST['token'])){
        $csrf_status = false;
    } else {
        $csrf_status = true;
    }

    //It exists, so compare the token we received against the
    //token that we have stored as a session variable.
    if(hash_equals($post_token, $_SESSION['token']) === false){
        $csrf_status = false;
    } else {
        $csrf_status = true;
    }

    return $csrf_status;
}