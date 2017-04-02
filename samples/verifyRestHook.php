<?php

session_start();

require_once '../vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => '44dwuap5k2vbe7mqy7ex495g',
    'clientSecret' => 'p4jtNRuyE7',
    'redirectUri' => 'http://localhost/iphp/data/Order.php',
));

// In order to verify the endpoint, we need to return the X-Hook-Secret header.
// By default, the autoverify() function will set the proper header, but if you
// pass false as the first argument to autoverify(false) the function will simply
// return the header value for you to set as you please (handy if you are using
// a PHP class or framework that manages requests for you

$infusionsoft->resthooks()->autoverify();

return;