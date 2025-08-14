<?php
    $page_title = 'CLIENT MANAGEMENT';
    include ('includes/dashboard.html');
    
    require 'connection.php';


    $display_all = "SELECT * FROM client_details";

    $query = mysqli_query($connection, $display_all);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Client Management</title>

    <style>

       body {
            margin: 0 50px;
        }

        .main_title {
            font-size: 72px;
            margin-top: 120px; 
            padding: 0 90px; 
            text-align:center;
            color: #B4203A;

        }   

         table {
            border-collapse: collapse;
            width: 100%;
            border-radius: 10px; 
            overflow: hidden;            
            margin-top: 20px; 
            margin-bottom: 50px;
        }

        th, td {
            border: 1px solid #37E6E2;
            padding: 10px;
            text-align: center;

        }

        th {
            background-color: #3ABFD2;
            color: #FFFFFE;
        }

        td {
            background-color: #EA675D;
            color: #FFFFFE;
        }

        .actions{
            color: #FFFFFE;
            text-decoration: none;

        }


    </style>

</head>

<body>
    
        <h1 class="main_title">CLIENTS</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>CLIENT NAME</th>
                <th>SOCIALS</th>
                <th>CONTACT NO.</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($rows = mysqli_fetch_array($query)) {
                $clientId = $rows['id'];

                // Fetch socials associated with the client
                $socialQuery = "SELECT social FROM socials WHERE client_id = $clientId";
                $socialResult = mysqli_query($connection, $socialQuery);
                $socials = mysqli_fetch_all($socialResult, MYSQLI_ASSOC);

                // Fetch phone numbers associated with the client
                $phoneQuery = "SELECT phone_number FROM phone_numbers WHERE client_id = $clientId";
                $phoneResult = mysqli_query($connection, $phoneQuery);
                $phoneNumbers = mysqli_fetch_all($phoneResult, MYSQLI_ASSOC);
            ?>
                <tr>
                    <td><?php echo $rows['id']; ?></td>
                    <td><?php echo $rows['fullname']; ?></td>
                    <td><?php
                        foreach ($socials as $social) {
                            echo $social['social'] . '<br>';
                        }
                        ?></td>
                    <td><?php
                        foreach ($phoneNumbers as $phoneNumber) {
                            echo $phoneNumber['phone_number'] . '<br>';
                        }
                        ?></td>
                    <td>
                        <a href="delete-client-details.php?id=<?php echo $rows['id']; ?>" class="actions">Delete</a><br><br>
                        <a href="edit-client-details.php?id=<?php echo $rows['id']; ?>" class="actions">Edit</a><br><br>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>