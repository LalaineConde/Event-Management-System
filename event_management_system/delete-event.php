<?php
require 'connection.php';
error_reporting(E_ERROR | E_PARSE);
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$event_id = $_GET['event_id'];

// Retrieve the status before deleting the event
$query = "SELECT status FROM events_details WHERE event_id = '$event_id'";
$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $status = $row['status'];

    // Delete the event
    $deleteQuery = "DELETE FROM events_details WHERE event_id = '$event_id'";
    $deleteResult = mysqli_query($connection, $deleteQuery);

    if ($deleteResult) {
        // Redirect based on the status
        switch ($status) {
            case 'PENDING':
                header('location: pending.php');
                break;
            case 'CONFIRMED':
                header('location: confirmed.php');
                break;
            case 'COMPLETED':
                header('location: completed.php');
                break;
        }
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
} else {
    echo "Error retrieving status: " . mysqli_error($connection);
}

mysqli_close($connection);
?>