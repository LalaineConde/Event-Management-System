<?php
    $page_title = 'CALENDAR';
    require 'connection.php';
    include ('includes/dashboard.html');

    error_reporting(E_ERROR | E_PARSE);
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    
    $eventQuery = mysqli_query($connection, "SELECT * FROM events_details WHERE status IN ('CONFIRMED', 'COMPLETED')");

    $events = array();

    while ($row = mysqli_fetch_assoc($eventQuery)) {

        if ($row['status'] == 'CONFIRMED' || $row['status'] == 'COMPLETED') {
            $backgroundColor = getBackgroundColor($row['event_service']);
            $events[] = array(
                'title' => $row['event_name'],
                'service' => $row['event_service'],
                'start' => date('Y-m-d\TH:i:s', strtotime($row['event_datetime'])),
                'end' => date('Y-m-d\TH:i:s', strtotime($row['event_endtime'])),
                'backgroundColor' => $backgroundColor,
                'className' => ($row['status'] == 'COMPLETED') ? 'completed-event' : '',
            );
        }
    }
    
    // Function to determine background color based on $eventService
    function getBackgroundColor($event_service)
    {
        switch ($event_service) {
            case 'Booth: Photostandee':
            case 'Booth: 4r Photo Magnet':
            case 'Booth: Half Magnet & Half Bookmark Photostrips':
            case 'Booth: Bookmark Photostrips':
            case 'Booth: Magnetic Photostrips':
            case 'Booth: Polaroid Magnet':
                return '#F8C4D3';
            case 'Audio Sounds and Lights: Package 1':
            case 'Audio Sounds and Lights: Package 2':
            case 'Audio Sounds and Lights: Package 3':
                return '#324045';
            case 'Booth & Audio Sounds and Lights Package: Package 1':
            case 'Booth & Audio Sounds and Lights Package: Package 2':
                return '#B4203A';
            case 'Booth & Audio Sounds and Lights Package: Package A':
            case 'Booth & Audio Sounds and Lights Package: Package B': 
            case 'Booth & Audio Sounds and Lights Package: Package C':
                return '#EA675D';
            case 'All in':
                return '#3ABFD2';
            case 'Null':
                return '#DAA06D';
            default:
                return '#DAA06D';
        }
    }
    
    $connection->close();
?>

<!DOCTYPE html>
<html>
<head></head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar View</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

  <style>

        .main_title {
            font-size: 72px;
            margin-top: 120px;
            margin-bottom: 30px;
            text-align: center;
            color: #B4203A;
    }

        .fc-day-content {
            position: relative;

    }

        .grouped {
            margin-top: 150px;
    }

        .calendar {
            box-shadow: 10px 10px 4px #324045;
            border: 8px solid #EE494C ;
            background-color: #EA675D;
            color: #ffffff;
            padding: 30px 50px;
            margin: 50px 100px;
            border-radius: 10px;
    }

        .calendar h2 {

            font-size: 32px;
            color: #ffffff; 
    }

        #calendar {
            color:#FABC2B;
    }


        .calendar button {
            background-color: #FABC2B;
            color: #333333;
            text-shadow: none;
            margin: 0 10px;
            border: none;
            padding: 0 20px;
            cursor: pointer;
            border-radius: 5px;
            opacity: 100%;

    }

        .calendar table {
            width: 100%;
            border-collapse: collapse;
    }

        .calendar th {
            background-color: #3ABFD2;
            border: 2px solid #333333;
            color: #ffffff;
            padding: 10px;
    }

        .calendar td {
            border: 2px solid #333333;
            background-color: #FDEAF1; 
            color: #000000;
    }
    .completed-event {
            text-decoration: line-through;
            color: #000000; 
        }


</style>

</style>
</head>
<body>

<div class="grouped">
    <h1 class="main_title">CALENDAR</h1>
    <div class="calendar">
        <h2>Calendar</h2>
        <div id="calendar"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },

            displayEventTime: true, 
            allDaySlot: false,
            events: <?php echo json_encode($events); ?>,

        

        eventClick: function (event) {
                var details = 'Event Name: ' + event.title + '\n';
                details += 'Event Service: ' + event.service + '\n';

                details += 'Event Date and Time: ' + moment(event.start).format('YYYY-MM-DD HH:mm') + '\n';
                details += 'Event End Time: ' + moment(event.end).format('HH:mm');
                
                
                alert(details);
            }
        });

        function getEventService(eventName) {
        var events = <?php echo json_encode($events); ?>;
        var event = events.find(function (e) {
            return e.title === eventName;
        });


    }
});
</script>
    
</body>
</html>