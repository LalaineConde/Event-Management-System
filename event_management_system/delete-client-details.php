<?php
    require 'connection.php';
    error_reporting(E_ERROR | E_PARSE);
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);


  
    if (isset($_GET['id'])) {
      $id = $_GET['id'];
  
      // Delete from socials table
      $deleteSocialsQuery = "DELETE FROM socials WHERE client_id = $id";
      mysqli_query($connection, $deleteSocialsQuery);
  
      // Delete from phone_numbers table
      $deletePhoneNumbersQuery = "DELETE FROM phone_numbers WHERE client_id = $id";
      mysqli_query($connection, $deletePhoneNumbersQuery);
  
      // Delete from client_details table
      $deleteClientQuery = "DELETE FROM client_details WHERE id = $id";
      mysqli_query($connection, $deleteClientQuery);
  
      header('Location: client-management.php');
      exit();
  } else {
      echo "Invalid request.";
  }
  
  mysqli_close($connection);
  ?>