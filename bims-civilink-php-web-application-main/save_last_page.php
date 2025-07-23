<?php
session_start();

// Store the last visited page in the session
if (isset($_POST['last_page'])) {
    $_SESSION['last_page'] = $_POST['last_page'];
}
?>