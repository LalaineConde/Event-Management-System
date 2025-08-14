<?php
    $page_title = 'OVERVIEW';
    include ('includes/dashboard.html');
    
    require 'connection.php';


    $display_all = "SELECT * FROM events_details";

    $query = mysqli_query($connection, $display_all);


   

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Events</title>

    <style>

       body {
    
            margin: 90px 0;

        }
        .subhead{
            width: 100%;
  
            padding: 10px 50px;
            background-color: #EFF0F3;
            border-bottom: 1px solid #2A2A2A;
            display: flex;
            justify-content: left;
   
        }

        a.list{
        position: relative;
   
        margin: 0 10px;
        color: #2A2A2A;
        /* font-family: sans-serif; */
        font-weight: bold;
        font-size: 14px;
        padding: 5px 0;
    }
    
    a.list:hover{
        color: #0D0D0D;
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
    
<div class = "subhead">

<a href="pending.php" class = "list">PENDING</a>
<a href="confirmed.php" class = "list">CONFIRMED</a>
<a href="completed.php" class = "list">COMPLETED</a>
</div>
    



  

</body>
</html>