<?php

/**
 * Bootstrap script that runs before any tests
 */

// Promote warnings "oh you need to fix something, but I won't tell you what, :)" to exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


// Prepare an environment similar to what the app will run in
$_SERVER['HTTP_HOST'] = 'example.com';
