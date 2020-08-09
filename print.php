<?php
    // Require this web authentication class file
    require_once 'AntAuth.php';
    // Create a new authentication object
    $auth_object = new AntAuth('https://students.ics.uci.edu/~ddluong1/print.php');

    // Both of these commands make it possible
    // to go to http://mypage.uci.edu/auth-test.php?login=1
    // or http://mypage.uci.edu/auth-test.php?logout=1
    // so people can login or logout.
    if ($_GET[login]) { $auth_object->login(NULL); }
    if ($_GET[logout]) { $auth_object->logout(); }

    // Next, we can check whether or not you're logged
    // in by checking the $auth->isLoggedIn()  method
    if ($auth_object->isLoggedIn()) {
        // do stuff, you can check the ucinetid of
        // the person by looking at $auth->ucinetid
        print "logged in </br>";
    }
    else {
        $auth_object->login(NULL); // if you want to redirect the user to log in
    }

    print "<pre>";
    print_r ($auth_object);
    print "</pre>";

?>