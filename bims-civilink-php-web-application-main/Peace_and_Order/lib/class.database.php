<?php

// Define the global function loadEnv outside the class
function loadEnv($filePath)
{

    $absolutePath = realpath($filePath);

    if (!$absolutePath || !file_exists($absolutePath)) {
        throw new Exception('.env file not found');
    }

    // Load .env file content
    $lines = file($absolutePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;  // Skip comments
        }
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}


class database
{
    protected $host;
    protected $username;
    protected $password;
    protected $db;
    protected $port;
    protected $_mysqli;
    protected $_query;

    public function __construct($host = null, $username = null, $password = null, $database = null, $port = null)
    {
        // Load environment variables from .env file
        $isLocalhost = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1', '::1']);

        if ($isLocalhost) {
            loadEnv(__DIR__ . '../../../.env');  // Localhost environment file path
        } else {
            loadEnv(__DIR__ . '/../../.env');  // Production environment file path
        }
        // Use environment variables if not provided as parameters
        $this->host = $host ?? $_ENV['DB_HOST'];
        $this->username = $username ?? $_ENV['DB_USERNAME'];
        $this->password = $password ?? $_ENV['DB_PASSWORD'];
        $this->db = $database ?? $_ENV['DB_DATABASE'];
        $this->port = $port ?? $_ENV['DB_PORT'];

        // Establish the database connection
        $this->connection();
    }

    public function connection()
    {
        $this->_mysqli = new mysqli($this->host, $this->username, $this->password, $this->db, $this->port) or die('There was a problem connecting to the database');
        $this->_mysqli->set_charset('utf8');
    }

    public function real_escape($string)
    {
        return $this->_mysqli->real_escape_string(stripslashes(addslashes(str_replace("'", "", "$string"))));
    }

    public function select($query)
    {
        $result = $this->_mysqli->query($query);
        if ($result) {
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return false;
            }
        }
    }

    public function insert($query)
    {
        $this->_mysqli->query($query);
        return $this->_mysqli->insert_id;
    }

    public function update($query)
    {
        $this->_mysqli->query($query);
    }

    public function delete($query)
    {
        $this->_mysqli->query($query);
    }

    public function rawData($query)
    {
        $result = $this->_mysqli->query($query);
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return false;
            }
        }
    }
}

?>