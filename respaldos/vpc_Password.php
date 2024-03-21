<?php
if (empty($_SERVER['CONTENT_TYPE'])) {
    $_SERVER['CONTENT_TYPE'] = "application/x-www-form-urlencoded";
}
//$serverusername = $_POST['username'] ?? '';
//$passwordserver = $_POST['passwd'] ?? '';
//$domain = $_POST['domain'] ?? '';
//$user = $_POST['user'] ?? '';
//$pass = $_POST['pass'] ?? '';


//echo "Username:" . $_POST['username'] . PHP_EOL;
//echo "Password: $passwordserver\n";
//echo "Domain: $domain\n";
//echo "User: $user\n";
//echo "Password: $pass\n";
var_dump($_POST);
