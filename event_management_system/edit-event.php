<?php
    require 'connection.php';
    error_reporting(E_ERROR | E_PARSE);
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $event_id = $_GET['event_id'];
    $status = $_GET['status']; 
    
    $query = "SELECT * FROM events_details WHERE event_id='$event_id'";
    $result = mysqli_query($connection, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $current_event_name = $row['event_name'];
        $current_event_datetime = $row['event_datetime'];
        $current_event_service = $row['event_service'];
        $current_event_endtime = $row['event_endtime'];

    } else {
        // Handle the case where the event with the specified ID is not found
        die("Event not found");
    }

    if (isset($_POST['submitForm'])) {
        $event_id_post = $_POST['event_id'];
        $event_name = $_POST['eventName'];
        $event_datetime = $_POST['eventDateTime'];
        $event_service = $_POST['eventService'];
        $event_endtime = $_POST['eventEndTime'];

        // Separate date and time from event_datetime
        list($event_date, $event_time) = explode("T", $event_datetime);

        $updateQuery = "UPDATE events_details SET event_name = '$event_name', event_datetime = '$event_datetime', event_service = '$event_service', event_endtime = '$event_endtime' WHERE event_id = '$event_id_post'";
        
        if (mysqli_query($connection, $updateQuery)) {
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
            echo "Error updating record: " . mysqli_error($connection);
        }
    }
?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Event Details</title>
<style>

        body {
                font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            background: rgba(240, 180, 180, 0.519);
            margin:70px 50px;
        }

        a{
        color: #000000;
        text-decoration: none;

        }
        .event_update_title {
            font-size: 40px;
            margin:30px 0;
            margin-bottom:20px;
            color: #B4203A;
        }

        .form_container {
            margin: 0 50px; 
            border-radius: 38px;
        }
      
        .row{
            display: flex;
            gap: 20px; 
            margin:0;
            padding:0;
        }
        .eventName_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
       
        label.eventName {
            font-size: 18px;
            margin: 20px 0;
        }

        #eventName {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }

        .eventService_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
       
        label.eventService {
            font-size: 18px;
            margin: 20px 0;
        }

        #eventService {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }

        
        .row2{
            display: flex;
            gap: 20px; 
            margin:0;
            padding:0;
        }


        .eventDateTime_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
       
        label.eventDateTime {
            font-size: 18px;
            margin: 20px 0;
        }

        #eventDateTime {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }

        .eventEndTime_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        
        label.eventEndTime {
            font-size: 18px;
            margin: 20px 0;
        }
   

        #eventEndTime {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }


    ::-webkit-calendar-picker-indicator {
            border-radius: 5px;
            margin: 0 10px; 
    }

    
        .task_submit_container {
            text-align: right;
            margin: 50px 0; 
        }


        .submit {
            width: 200px;
            height: 50px;
            font-size: 22px;
            color: #ffffff;
            background-color: #c87F1D;
            border: 2px solid #333333;
            border-radius: 7px;
            box-shadow: 5.5px 5.5px 5px #333333;
        }


</style>
</head>

<body>

  <div class="form_container">
  <h1 class="event_update_title">Update Event Details</h1>
  <form action="edit-event.php?event_id=<?php echo $event_id; ?>&status=<?php echo $status; ?>" method="POST">

       
<div class="row">

<div class="eventName_container">
        <label for="eventName" class="eventName">EVENT NAME</label>
        <input type="text" id="eventName" name="eventName" value="<?php echo $current_event_name; ?>" required><br>
        <br>
    </div>


    <div class="eventService_container">
        <label for="eventService" class="eventService" >SERVICE</label>
        <select id="eventService" name="eventService">
        <?php
            $services = array(
                "Choose Your Service Type / Package",
                "Booth: Photostandee",
                "Booth: 4r Photo Magnet",
                "Booth: Half Magnet & Half Bookmark Photostrips",
                "Booth: Bookmark Photostrips",
                "Booth: Magnetic Photostrips",
                "Booth: Polaroid Magnet",
                "Audio Sounds and Lights: Package 1",
                "Audio Sounds and Lights: Package 2",
                "Audio Sounds and Lights: Package 3",
                "Booth & Audio Sounds and Lights Package: Package 1",
                "Booth & Audio Sounds and Lights Package: Package 2",
                "Booth & Audio Sounds and Lights Package: Package A",
                "Booth & Audio Sounds and Lights Package: Package B",
                "Booth & Audio Sounds and Lights Package: Package C",
                "All in"
            );
            foreach ($services as $service) {
                $selected = ($current_event_service === $service) ? 'selected' : '';
                echo "<option value=\"$service\" $selected>$service</option>";
            }

        ?>
    </select><br>
    <br>
    </div>

</div>
<div class="row2">
<div class="eventDateTime_container">
            <label for="eventDateTime" class="eventDateTime">EVENT DATE & TIME</label>
            <input type="datetime-local" id="eventDateTime" name="eventDateTime" value="<?php echo $current_event_datetime; ?>"><br>
        </div>
        <div class="eventEndTime_container">
            <label for="eventEndTime" class="eventDateTime">EVENT END TIME</label>
            <input type="time" id="eventEndTime" name="eventEndTime" value="<?php echo $current_event_endtime; ?>"><br>
        </div>  

</div>
<input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
        <input type="hidden" name="status" value="<?php echo $status; ?>">


    <div class="task_submit_container">
    <button type="submit" name="submitForm" class="submit">SAVE</button>
</div> 



    </form>

    </div>

</body>
</html>