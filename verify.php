<?php
    // Require this web authentication class file
    require_once 'AntAuth.php';
    // Create a new authentication object
    $auth_object = new AntAuth('https://students.ics.uci.edu/~ddluong1/verify.php');

    // Both of these commands make it possible
    // to go to http://mypage.uci.edu/auth-test.php?login=1
    // or http://mypage.uci.edu/auth-test.php?logout=1
    // so people can login or logout.
    if ($_GET[login]) {         
        if ($_GET[token])
        {
            $auth_object->login($_GET[token]); // if you want to redirect the user to log in with token
        }
        else
        {
            $auth_object->login(NULL); // if you want to redirect the user to log in
        }
    }
    if ($_GET[logout]) { $auth_object->logout(); }

    // Next, we can check whether or not you're logged
    // in by checking the $auth->isLoggedIn()  method
    if ($auth_object->isLoggedIn()) {
        // do stuff, you can check the ucinetid of
        // the person by looking at $auth->ucinetid
        print "logged in </br>";
    }
    else {
        // you're not logged in, sorry...
        if ($_GET[token])
        {
            $auth_object->login($_GET[token]); // if you want to redirect the user to log in with token
        }
        else
        {
            $auth_object->login(NULL); // if you want to redirect the user to log in
        }
    }


    // Also, you can look at all the values within
    // the auth object by using the code:
  
    if ($_GET[token]) {  
        $url = 'http://confessions-raw.herokuapp.com/verify/?id='.$auth_object->campus_id.'&token='.$_GET[token].'&hmac='.hash_hmac("sha256", $auth_object->campus_id, "insert secret here");
    
        
        $contents = file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true]]));
        
        $status_line = $http_response_header[0];

        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
    
        $status = $match[1];
    
        if ($status == "200") {
            print "VERIFICATION SUCCESS!";
        }
        else if ($status == "500")
        {
            print($contents);
        }
        else
        {
            print "Backend down";
        }

    }
    else
    {
        print "<pre>";
        print_r ($auth_object);
        print "</pre>";
    }
?>