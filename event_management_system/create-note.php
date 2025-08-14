<?php
$page_title = 'CLIENT COMMS';

require 'connection.php';
error_reporting(E_ERROR | E_PARSE);

$errors = array();

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Initialize $edit_mode before using it
$edit_mode = false;

// Check if edit parameters are provided
if (isset($_GET["edit_note"])) {
    $edit_id = $_GET["edit_note"];
    $edit_query = "SELECT * FROM client_comms WHERE id = $edit_id";
    $edit_result = $connection->query($edit_query);

    if ($edit_result->num_rows > 0) {
        $edit_row = $edit_result->fetch_assoc();
        $title = $edit_row['title'];
        $edit_mode = true;
    } else {
        echo "Invalid note ID for editing.";
        exit();
    }
} else {
    $title = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["noteTitle"];
    $type = $_POST["communicationType"];
    $content = $_POST["noteContent"];

    if ($edit_mode) {
        $edit_id = $_POST["edit_id"];
        $update_query = "UPDATE client_comms SET title = '$title', notes = '$content', communication_type = '$type' WHERE id = $edit_id";
        
        if ($connection->query($update_query) === TRUE) {
            echo "Note updated successfully!";
            header("Location: client-comms.php");
            exit();
        } else {
            echo "Error updating note: " . $connection->error;
        }
    } else {
        $insert_query = "INSERT INTO client_comms (title, notes, communication_type) VALUES ('$title', '$content', '$type')";
        
        if ($connection->query($insert_query) === TRUE) {
            echo "Note saved successfully!";
            header("Location: client-comms.php");
            exit();
        } else {
            echo "Error saving note: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($edit_mode) ? 'Edit Note' : 'Create Note'; ?></title>

    <style>
    body {
        font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        margin-bottom:50px;
        margin-left:50px;
        margin-right:50px;
    }
    .backbtn{
        width: 100px;
        height: 30px;
        font-size: 16px;
        color: #ffffff;
        background-color: #c87F1D;
        border: 2px solid #333333;
        border-radius: 7px;
        margin-bottom: 20px;
    }



    body.booth-selected {
        background: #F8C4D3;
    }

    body.audio-selected {
        background: #324045;
    }

    body.number-selected {
        background: #B4203A;
    }

    body.letter-selected {
        background: #EA675D;
    }

    body.allin-selected {
        background: #3ABFD2;
    }

    textarea{
        margin: 20px 0;
        border-radius: 7px;
    }

    input{
        width: 250px;
        height: 20px;
        border-radius: 10px;
        font-size: 18px;
        padding: 10px 10px;
        border: none;
        margin-right:10px;
        background: rgba(173, 216, 236, 0.9);
        color:#000000;
    }

    #communicationType{
        width: 250px;
        height: 40px;
        border-radius: 10px;
        font-size: 16px;
        padding: 10px 10px;
        border: none;
        margin:0 10px;
        background: rgba(173, 216, 236, 0.9);
    }

    .update_save_btn{
        width: 100px;
        height: 30px;
        font-size: 16px;
        color: #ffffff;
        background-color: #c87F1D;
        border: 2px solid #333333;
        border-radius: 7px;
        box-shadow: 5.5px 5.5px 5px #333333;
        margin-bottom: 20px;
        float:right;
    }

    </style>
</head>
<body class="<?php echo ($type == 'booth') ? 'booth-selected' : (($type == 'audio and lights') ? 'audio-selected' : ''); ?>">

<form method="post" action="" id="noteForm">
    <?php if ($edit_mode) : ?>
        <input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>">
    <?php endif; ?>
    <button type="button" class= "backbtn" onclick="history.back()">Back</button><br>
    <input type="text" name="noteTitle" placeholder="Title" value="<?php echo $title; ?>">
    <select name="communicationType" id="communicationType" onchange="changeBackgroundColor()">
        <option value="booth" <?php echo ($type == 'booth' || ($edit_mode && $edit_row['communication_type'] == 'booth')) ? 'selected' : ''; ?>>Booth</option>
        <option value="audio and lights" <?php echo ($type == 'audio and lights' || ($edit_mode && $edit_row['communication_type'] == 'audio and lights')) ? 'selected' : ''; ?>>Audio and Lights</option>
        <option value="both package number" <?php echo ($type == 'both package number' || ($edit_mode && $edit_row['communication_type'] == 'both package number')) ? 'selected' : ''; ?>>Both Package Number</option>
        <option value="both package letter" <?php echo ($type == 'both package letter' || ($edit_mode && $edit_row['communication_type'] == 'both package letter')) ? 'selected' : ''; ?>>Both Package Letter</option>
        <option value="all in" <?php echo ($type == 'all in' || ($edit_mode && $edit_row['communication_type'] == 'all in')) ? 'selected' : ''; ?>>All In</option>
    </select>
    <textarea name="noteContent" placeholder="Type your notes here..." style="width: 100%; height: 70vh;"><?php echo ($edit_mode) ? $edit_row['notes'] : ''; ?></textarea>
    <button type="submit" class="update_save_btn"><?php echo ($edit_mode) ? 'Update' : 'Save'; ?></button>
</form>

<script>
    function changeBackgroundColor() {
        var communicationTypeSelect = document.getElementById('communicationType');
        var body = document.body;

        body.classList.remove('booth-selected', 'audio-selected', 'number-selected', 'letter-selected', 'allin-selected');

        switch (communicationTypeSelect.value) {
            case 'booth':
                body.classList.add('booth-selected');
                break;
            case 'audio and lights':
                body.classList.add('audio-selected');
                break;
            case 'both package number':
                body.classList.add('number-selected');
                break;
            case 'both package letter':
                body.classList.add('letter-selected');
                break;
            case 'all in':
                body.classList.add('allin-selected');
                break;
            default:
                break;
        }
    }

    changeBackgroundColor();
</script>

</body>
</html>