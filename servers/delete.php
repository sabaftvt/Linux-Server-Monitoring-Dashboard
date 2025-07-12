<?php
include 'config.php';

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    $sql = "DELETE FROM servers WHERE ip='$ip'";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>