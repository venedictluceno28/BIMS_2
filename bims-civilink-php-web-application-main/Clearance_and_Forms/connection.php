<?php
// Get the current protocol (HTTP or HTTPS)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Get the current host (domain)
$host = $_SERVER['HTTP_HOST'];

// Get the base path of the current script
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Construct the relative path to db.php assuming it's in the root directory
$includePath = __DIR__ . '/../db.php'; // This uses the current directory

// Include the db.php file
include($includePath);
?>