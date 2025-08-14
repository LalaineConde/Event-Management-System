<?php
    $page_title = 'COMPLETED';
    include ('includes/dashboard.html');
    
    require 'connection.php';


    $display_all = "SELECT * FROM events_details";

    $query = mysqli_query($connection, $display_all);

    $display_completed = "SELECT * FROM events_details WHERE status = 'COMPLETED'";
    $query = mysqli_query($connection, $display_completed);
    
    if (isset($_GET['event_id'])) {
        $completed_event_id = $_GET['event_id'];

        $update_status_query = "UPDATE events_details SET status = 'COMPLETED' WHERE event_id = ?";

        $stmt = mysqli_prepare($connection, $update_status_query);
        mysqli_stmt_bind_param($stmt, 'i', $completed_event_id);
        $result = mysqli_stmt_execute($stmt);


       
            header('Location: completed.php');
            exit();
        }
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Events</title>

    <style>
        body {
            margin: 90px 0;
        }

        .pending_container {
            width: 600px;
            height: 180px;
            padding: 30px;
            margin: 20px;
            border: 10px solid;
            box-sizing: border-box;
        }

        .container1 {
            display: flex;
            justify-content: center;
            color:#ffffff;
        }

        .left-content {
            float: left;
        }

        .right-content {
            float: right;
        }


        .subhead {
            width: 100%;
            padding: 10px 50px;
            background-color: #F8C4D3;
            border-bottom: 1px solid #2A2A2A;
            display: flex;
            justify-content: left;
        }

        a.list {
            position: relative;
            margin: 0 10px;
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            padding: 5px 0;
        }

        a.list:hover {
            color: #ffffff;
        }

        a.list::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            border-radius: 1px;
            background-color: #4059A0;
            transition: width 0.3s ease;
        }

        a.list:hover::before {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="subhead">
    <a href="pending.php" class="list">PENDING</a>
    <a href="confirmed.php" class="list">CONFIRMED</a>
    <a href="completed.php" class="list">COMPLETED</a>
</div>

<?php
    // Loop through each row in the result set
    while ($row = mysqli_fetch_assoc($query)) {
        $event_id = $row['event_id'];
        $eventName = $row['event_name'];
        $eventService = $row['event_service'];
        $eventDatetime = $row['event_datetime'];
        $eventEndTime = $row['event_endtime'];

        $backgroundColor = (
            $eventService == 'Booth: Photostandee' || 
            $eventService == 'Booth: 4r Photo Magnet' || 
            $eventService == 'Booth: Half Magnet & Half Bookmark Photostrips' || 
            $eventService == 'Booth: Bookmark Photostrips' || 
            $eventService == 'Booth: Magnetic Photostrips' || 
            $eventService == 'Booth: Polaroid Magnet'
        ) ? '#F8C4D3' : (
            $eventService == 'Audio Sounds and Lights: Package 1' ||
            $eventService == 'Audio Sounds and Lights: Package 2' ||
            $eventService == 'Audio Sounds and Lights: Package 3' 
            ? '#324045' : (
                $eventService == 'Booth & Audio Sounds and Lights Package: Package 1' ||
                $eventService == 'Booth & Audio Sounds and Lights Package: Package 2'
                ? '#B4203A' : (
                    $eventService == 'Booth & Audio Sounds and Lights Package: Package A' ||
                    $eventService == 'Booth & Audio Sounds and Lights Package: Package B' ||
                    $eventService == 'Booth & Audio Sounds and Lights Package: Package C'
                    ? '#EA675D' : (
                        $eventService == 'All in'
                        ? '#3ABFD2' : (
                            $eventService == 'Null'
                            ? '' : '' 
                        )
                    )
                )
            )
        );
        
        // Separate date and time
        list($eventDate, $eventTime) = explode(' ', $eventDatetime);
?>
    <section class="container1">
        <div class="pending_container" style="border: 3px solid gray; background: <?php echo $backgroundColor; ?>">
            <div class="left-content">
                <h1><?php echo $eventName; ?></h1>
                <br>
                <p><?php echo $eventService; ?></p>
           
                <br>
                <br>
                <a href="delete-event.php?event_id=<?php echo $event_id; ?>&status=COMPLETED">DELETE</a>
            </div>

            <div class="right-content">
                    <h1><?php echo $eventDate; ?></h1>
                    <br>
                    <p><?php echo $eventTime; ?> - <?php echo $eventEndTime; ?></p>
           
                <br>
                <br>
                <p>COMPLETED</p>
              
                
                </div>
            </div>
        </div>
    </section>
<?php
    }
?>

</body>
</html>