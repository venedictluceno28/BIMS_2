<?php
// Define the loadEnv function to read from the .env file
if (!function_exists('loadEnv')) {
    function loadEnv($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('.env file not found');
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;  // Skip comments
            }
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Load the .env file (adjust the path if needed)
loadEnv(__DIR__ . '/.env');


// Connect to the database using the loaded environment variables
$db = mysqli_connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_DATABASE']
);

if (!$db) {
    die('Database connection error: ' . mysqli_connect_error());
}

