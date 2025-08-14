<?php
$page_title = 'CLIENT COMMS';
include ('includes/dashboard.html');
require 'connection.php';
error_reporting(E_ERROR | E_PARSE);

$errors = array();

if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_note"])) {
    $note_id = $_POST["delete_note"];

    
    $delete_query = "DELETE FROM client_comms WHERE id = $note_id";
    if ($connection->query($delete_query) === TRUE) {
       
    } else {
        echo "Error deleting note: " . $connection->error;
    }
}


$select_query = "SELECT * FROM client_comms";
$result = $connection->query($select_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Communications</title>
    <style>
        body {
            color: #ffffff;
            margin:70px 50px;
        }
        #note-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content:center;

            padding: 20px;
        }
        .note-box {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px 30px;
            width: 400px;
            background-color: #fff;
            margin-bottom: 20px;
            position: relative;
            cursor: pointer;
            white-space: pre-line;
            overflow: hidden;
            height: 279px; 
            text-overflow: ellipsis;
        }

        .note-box h3,
        .note-box p {
            margin-top:3px;
        }
        .comms-type{
            font-size:10px;
        }
        .delete-button {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: transparent;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        #add-note-button {
            color: #ffffff;
            background-color: #c87F1D;
            border: 2px solid #333333;
           font-weight:bold;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 50px; 
            cursor: pointer;
            width: 150px;
            height: 30px;
          
            border-radius: 7px;

        }
    </style>
</head>
<body>


<button id="add-note-button" onclick="addNote()">Add Note</button>


<div id="note-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="note-box" style="background-color: ' . getColorByType($row["communication_type"]) . '" onclick="editNote(' . $row["id"] . ')">';
            echo '<button class="delete-button" onclick="deleteNote(event, ' . $row["id"] . ')">Delete</button>';
            echo '<h3>' . $row["title"] . '</h3>';
            echo '<p class="comms-type">Service Type: ' . $row["communication_type"] . '</p>';
            echo '<p>' . $row["notes"] . '</p>';
            echo '</div>';
        }
    } else {
        echo "No notes available.";
    }
    


    function getColorByType($type) {
        
        switch ($type) {
            case "booth":
                return "#F8C4D3"; 
            case "audio and lights":
                return "#324045"; 
            case "both package number":
                return "#B4203A"; 
            case "both package letter":
                return "#EA675D"; 
            case "all in":
                return "#3ABFD2"; 
            default:
                return "#DAA06D"; 
        }
    }


    ?>
</div>

<script>
    function editNote(noteId) {      
        window.location.href = "create-note.php?edit_note=" + noteId;
    }

    function addNote() {
        window.location.href = "create-note.php";
    }

    function deleteNote(event, noteId) {
        event.stopPropagation(); 
       
            const form = document.createElement("form");
            form.method = "post";
            form.action = "client-comms.php"; 
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "delete_note";
            input.value = noteId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        
    }
</script>
</body>
</html>