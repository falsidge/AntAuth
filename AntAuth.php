<?php

class AntAuth
{
    // The URLs to the web authentication at login.uci.edu
    var $login_url    = 'https://login.uci.edu/ucinetid/webauth';
    var $logout_url   = 'https://login.uci.edu/ucinetid/webauth_logout';
    var $check_url    = 'http://login.uci.edu/ucinetid/webauth_check';
    var $this_url = ''; // README NOTE : URL SUPPLIED IN CONSTRUCTOR
    //TODO: AUTOMATICALLY GET THE URL

    // The cookie - the name of the cookie is 'ucinetid_auth'
    var $cookie;

    // The user's URL - indicates where to goes upon authentication
    var $url;

    // The user's remote address - matched against the auth_host
    var $remote_addr;

    // The various errors that might crop up are stored in this array
    var $errors = array();

    // These are the defined vars from login.uci.edu
    var $time_created = 0;
    var $ucinetid = '';
    var $age_in_seconds = 0;
    var $max_idle_time = 0;
    var $auth_fail = '';
    var $seconds_since_checked = 0;
    var $last_checked = 0;
    var $auth_host = '';

    // Constructor for the web authentication
    function AntAuth($this_url) {
        $this->this_url = $this_url;
        
        // TODO check the PHP version?

        // Next, we'll grab some key global variables
        $cookie_vars_array = $GLOBALS[_COOKIE];
        $get_vars_array = $GLOBALS[_GET];
        $server_vars_array = $GLOBALS[_SERVER];

        // Let's get the client's ip address
        $this->remote_addr = $server_vars_array[REMOTE_ADDR];

        // Time to construct the client's URL
        // Check the server port first
  

        // Modify the various login.uci.edu URLs with our return URL
        //print $this->login_url;
        $this->login_url .= '?return_url=' . urlencode($this->this_url);
        //print $this->login_url;
        $this->logout_url .= '?return_url=' . urlencode($this->this_url);

        // Let's add the cookie called 'ucinetid_auth'
        if ($cookie_vars_array[ucinetid_auth]) {
            $this->cookie = $cookie_vars_array[ucinetid_auth];
            $this->check_url .= '?ucinetid_auth=' . $this->cookie;
        }

        // Now, let's check authentication
        $this->checkAuth();

    } // end Constructor

    // Check the authentication based on cookie
    function checkAuth() {

        // First, we'll check that we even have a cookie
        if (empty($this->cookie) || $this->cookie == 'no_key') {
            return false;
        }

        // Check that we can connect to login.uci.edu
        if (!$auth_array = @file($this->check_url)) {
            $this->errors[2] = "Unable to connect to login.uci.edu";
            return false;
        }
        // Make sure we have an array, and build the auth values
        if (is_array($auth_array)) {
            foreach ($auth_array as $k => $v) {
  
                if (!empty($v)) {
                    
                    $v = trim($v);
                    $auth_values = explode("=", $v);
          
                    if (!empty($auth_values[0]) && !empty($auth_values[1])) 
                    {
                        $this->{$auth_values[0]} = $auth_values[1];
                    }
                }
            }
            // Check to ensure auth_host is verified
            if (isset($this->x_forwarded_for))
            {
                if ($this->auth_host != $this->x_forwarded_for) {
                    $this->errors[3] = "Warning, the auth host doesn't match.";
                    return false;
                }
            }
            else
            {
                if ($this->auth_host != $this->remote_addr) {
                    $this->errors[3] = "Warning, the auth host doesn't match.";
                    return false;
                }
            }
            return true;
        }
    } // end check_auth

    // Boolean, determines if someone's logged in
    function isLoggedIn() {
        if ($this->time_created) return true;
        else return false;
    }

    // The login function
    function login($token) {
        if ($token)
        {
            print Header('Location: '.$this->login_url.'?token='.$token);
        }
        else{
            print Header('Location: '.$this->login_url);

        }
        die();
    }

    // The logout function
    function logout() {
        print Header('Location: ' . $this->logout_url);
        die();
    }
}
?>