<?php
//require_once 'includes/application_top.php';
// Set the JSON header
header("Content-type: text/json");


// The x value is the current JavaScript time, which is the Unix time multiplied by 1000.
$x = time() * 1000;
// The y value is a sin number
$y = sin($x);

// Create a PHP array and echo it as JSON
$ret = array($x, $y);



echo json_encode($ret);

?>