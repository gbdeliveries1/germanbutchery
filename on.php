<?php					
// Database connection file
session_start();

// Database configuration
$servername = "localhost";
$username = "u496441540_gbdeliveries";
$password = "!@Savion123@!"; // Add your password here if needed
$dbname = "u496441540_gbdeliveries";
date_default_timezone_set('Africa/Cairo');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include "deliver.php";

function simple_crypt( $string, $action = 'e' ) {
    // you may change these values to your own
    $secret_key = 'simple_secret_key';
    $secret_iv = 'simple_secret_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}
//$encrypted = simple_crypt( 'Hello World!', 'e' );
//$decrypted = simple_crypt( 'RTlOMytOZStXdjdHbDZtamNDWFpGdz09', 'd' );
function latest_version($file_name){
    echo $file_name."?".filemtime($_SERVER['DOCUMENT_ROOT'] .$file_name);
}
?><?php
// ... your existing database connection code ...

// Include currency functions
if (file_exists(__DIR__ . '/../includes/currency.php')) {
    include_once __DIR__ . '/../includes/currency.php';
}

// Get current currency globally
$currentCurrency = getCurrentCurrency($conn);
?>
