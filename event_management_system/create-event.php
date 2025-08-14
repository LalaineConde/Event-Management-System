<?php
$page_title = 'ADD EVENT';
include('includes/dashboard.html');
require 'connection.php';
error_reporting(E_ERROR | E_PARSE);

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitForm'])) {
    // Extract event data
    $event_name = mysqli_real_escape_string($connection, $_POST['eventName']);
    $event_service = mysqli_real_escape_string($connection, $_POST['eventService']);
    $event_datetime = mysqli_real_escape_string($connection, $_POST['eventDateTime']);
    $event_endtime = mysqli_real_escape_string($connection, $_POST['eventEndTime']);

    // Extract client data
    $client_fullname = mysqli_real_escape_string($connection, $_POST['clientName']);
    $socials = isset($_POST['socials']) ? $_POST['socials'] : array();
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : array();


    if (count($errors) == 0) {
         if ($event_service == 'Null' || empty($event_datetime) || empty($event_endtime)) {
            $insertEventQuery = "INSERT INTO events_details (event_name, event_service, event_datetime, status, event_endtime) VALUES (?, ?, ?, 'PENDING', ?)";
            header('Location: pending.php');
            
        } else {
            $insertEventQuery = "INSERT INTO events_details (event_name, event_service, event_datetime, status, event_endtime) VALUES (?, ?, ?, 'CONFIRMED', ?)";
            header('Location: confirmed.php');
            
        }
        
        $stmtEvent = $connection->prepare($insertEventQuery);
        $stmtEvent->bind_param('ssss', $event_name, $event_service, $event_datetime, $event_endtime);

        if ($stmtEvent->execute()) {
            $lastEventId = $stmtEvent->insert_id;

            if (!empty($client_fullname) || !empty($socials) || !empty($phone_number)) {
                // Insert data into client_details table
                $insertClientQuery = "INSERT INTO client_details (fullname) VALUES (?)";
                $stmtClient = $connection->prepare($insertClientQuery);
                $stmtClient->bind_param('s', $client_fullname);

                if ($stmtClient->execute()) {
                    $lastClientId = $stmtClient->insert_id;

                    // Insert data into socials table
                    foreach ($socials as $social) {
                        $insertSocialQuery = "INSERT INTO socials (client_id, social) VALUES (?, ?)";
                        $stmtSocial = $connection->prepare($insertSocialQuery);
                        $stmtSocial->bind_param('is', $lastClientId, $social);

                        if (!$stmtSocial->execute()) {
                            $errors['db_error_social'] = "Database error: failed to create social. " . $stmtSocial->error;
                        }
                    }

                    // Insert data into phone_numbers table
                    foreach ($phone_number as $phoneNumber) {
                        $insertPhoneNumberQuery = "INSERT INTO phone_numbers (client_id, phone_number) VALUES (?, ?)";
                        $stmtPhoneNumber = $connection->prepare($insertPhoneNumberQuery);
                        $stmtPhoneNumber->bind_param('is', $lastClientId, $phoneNumber);

                        if (!$stmtPhoneNumber->execute()) {
                            $errors['db_error_phone'] = "Database error: failed to create phone number. " . $stmtPhoneNumber->error;
                        }
                    }
                } else {
                    $errors['db_error_client'] = "Database error: failed to create client. " . $stmtClient->error;
                }
            }

           
        } else {
            $errors['db_error_event'] = "Database error: failed to create event. " . $stmtEvent->error;
        }
    }
}

$connection->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Schedule Event / Appointment</title>
<style>

        body {

            margin:70px 50px;

        }
        a{

        text-decoration: none;

        }
        .event_form_title {
            font-size: 40px;
            margin-top: 50px; 
            margin-bottom:50px;
            color: #B4203A;

        }

        .client_form_title {
            font-size: 40px;
            margin-top: 50px; 
            margin-bottom:50px;
            color: #B4203A;

        }

        .form_container {
            margin: 0 50px; 
            padding: 20px 20px; 
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
            color: #000000;
            font-size: 18px;
            margin: 20px 0;
        }

        #eventName {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
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
            color: #000000;
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
            color: #000000;
            font-size: 18px;
            margin: 20px 0;
        }

        #eventDateTime {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
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
            color: #000000;
            font-size: 18px;
            margin: 20px 0;
        }
   

        #eventEndTime {
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }



    ::-webkit-calendar-picker-indicator {
            border-radius: 5px;
            margin: 0 10px; 
    }

    .clientId_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label.clientId{
            color: #000000;
            font-size: 18px;
            margin: 20px 0;
        }

        #clientId{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }

        .clientName_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label.clientName{
            color: #000000;
            font-size: 18px;
            margin: 20px 0;
        }

        #clientName{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }



        .col{
            display: flex;
            flex-direction: column;
            flex-basis: 50%;
        }

        .col2{
            display: flex;
            flex-direction: column;
            flex-basis: 50%;

        }

        .socials_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #EA675D;
            padding:20px 20px;
            border-radius: 10px;


        }
        .socials_label_container{
            display: flex;
            align-items: center;
            width: 100%; 
            max-width: 500px; 
            margin-bottom: 10px; 
 
        }

        label.socials{
            color: #000000;
            font-size: 18px;
            margin: 20px 0;

        }

        #socials{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
            
        }


        .phoneNumber_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #EA675D;
            padding:20px 20px;
            border-radius: 10px;


        }
        .phoneNumber_label_container{
            display: flex;
            align-items: center;
            width: 100%; 
            max-width: 500px; 
            margin-bottom: 10px;
        }

        label.phoneNumber{
            color: #000000;
            font-size: 18px;
            margin: 20px 0;

        }

        #phoneNumber{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
        }



        .socials_input_container,
        .phoneNumber_input_container {
            display: flex;
            gap: 10px;          
            width: 100%; 
        }

        .deleteSocial,
        .deletePhoneNumber {
            cursor: pointer;
            color: red; 
            font-size: 50px;
            line-height: 50px; 
 
        }
        
        .addSocial,
        .addPhoneNumber {
            cursor: pointer;
            font-size: 20px;
            color: black; 
            margin-left: 5px;
        }

        .addSocial .plus,
        .addPhoneNumber .plus {
            margin: 0px 20px;
            color: green; 
            
        }

        .input-container {
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            border-radius: 10px;
            background-color: rgba(255, 142, 60, 0.7);
            margin-bottom: 20px; 
        }


        label {
            font-size: 18px;
            margin: 20px 0;
        }

        input[type="text"] {
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px);
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px;
            margin-bottom: 20px; 
            background: rgba(173, 216, 236, 0.9);
        }

        .deleteButton {
            cursor: pointer;
            color: #EE494C;
        }

        .addButton {
            cursor: pointer;
            font-size: 20px;
            color: black;
            margin-left: 5px;
        }

        .addButton .plus {
            margin: 0px 20px;
            color: green;
        }

        .plus {
            color: green;
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
  <h1 class="event_form_title">EVENT INFORMATION</h1>
    <form action="create-event.php" method="POST">

        <?php if (count($errors) > 0): ?>
        <div>
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>

<!--     <div class="eventid_container">
        <label for="event_id" class="event_id">Event ID</label>
        <input type="text" id="event_id" name="event_id" value="<?php echo $row['event_id']; ?>" readonly><br><br>
</div> -->

<div class="row">

<div class="eventName_container">
        <label for="eventName" class="eventName">EVENT NAME</label>
        <input type="text" id="eventName" name="eventName" required><br>
        <br>
    </div>


    <div class="eventService_container">
        <label for="eventService" class="eventService">SERVICE</label>
    <select id="eventService" name="eventService">
        <option value="Null">Choose Your Service Type / Package</option>
        <option value="Booth: Photostandee">Booth: Photostandee</option>
        <option value="Booth: 4r Photo Magnet">Booth: 4r Photo Magnet</option>
        <option value="Booth: Half Magnet & Half Bookmark Photostrips">Booth: Half Magnet & Half Bookmark Photostrips</option>
        <option value="Booth: Bookmark Photostrips">Booth: Bookmark Photostrips</option>
        <option value="Booth: Magnetic Photostrips">Booth: Magnetic Photostrips</option>
        <option value="Booth: Polaroid Magnet">Booth: Polaroid Magnet</option>
        <option value="Audio Sounds and Lights: Package 1">Audio Sounds and Lights: Package 1</option>
        <option value="Audio Sounds and Lights: Package 2">Audio Sounds and Lights: Package 2</option>
        <option value="Audio Sounds and Lights: Package 3">Audio Sounds and Lights: Package 3</option>
        <option value="Booth & Audio Sounds and Lights Package: Package 1">Booth & Audio Sounds and Lights Package: Package 1</option>
        <option value="Booth & Audio Sounds and Lights Package: Package 2">Booth & Audio Sounds and Lights Package: Package 2</option>
        <option value="Booth & Audio Sounds and Lights Package: Package A">Booth & Audio Sounds and Lights Package: Package A</option>
        <option value="Booth & Audio Sounds and Lights Package: Package B">Booth & Audio Sounds and Lights Package: Package B</option>
        <option value="Booth & Audio Sounds and Lights Package: Package C">Booth & Audio Sounds and Lights Package: Package C</option>
        <option value="All in">All in</option>
    </select><br>
    <br>
    </div>

</div>

<div class="row2">

    <div class="eventDateTime_container">
        <label for="eventDateTime" class="eventDateTime">EVENT DATE & TIME</label>
        <input type="datetime-local" id="eventDateTime" name="eventDateTime"><br>
    </div>

        <div class="eventEndTime_container">
            <label for="eventEndTime" class="eventDateTime">EVENT END TIME</label>
            <input type="time" id="eventEndTime" name="eventEndTime"><br>
        </div>    
</div>

<hr>

<h1 class="client_form_title">CLIENT INFORMATION</h1>


    <div class="clientName_container">
    <label for="clientName" class="clientName">CLIENT NAME</label>
    <input type="text" id="clientName" name="clientName"><br>
   
    </div>


<div class="row">
    
    <div class="col">

    <label for="socials" class="socials">SOCIALS</label>
    <div class="socials_container">

    
        <div class="socials_input_container">
            <input type="text" id="socials" name="socials[]">
            <span class="deleteSocial" onclick="deleteSocial(this)">-</span>
        </div>
   
    </div> 
    
        </div>

    <div class="col2">
    <label for="phoneNumber" class="phoneNumber">PHONE NO.</label> 

    <div class="phoneNumber_container">


    
      
        <div class="phoneNumber_input_container">
            <input type="text" id="phoneNumber" name="phone_number[]">
            <span class="deletePhoneNumber" onclick="deletePhoneNumber(this)">-</span>
        </div>


    </div>


    <div class="task_submit_container">
    <button type="submit" name="submitForm" class="submit">SAVE</button>
</div> 



    </form>

    </div>
    
    <script>
    function addSocial() {
        const socialsContainer = document.querySelector('.socials_container');
        const socialsInputContainer = document.createElement('div');
        socialsInputContainer.classList.add('socials_input_container');

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'socials[]';
        input.required = true;

        const deleteButton = document.createElement('span');
        deleteButton.classList.add('deleteSocial');
        deleteButton.textContent = '-';
        deleteButton.onclick = function () {
            socialsContainer.removeChild(socialsInputContainer);
            updateAddButton(socialsContainer, 'Add social', addSocial);
        };

        socialsInputContainer.appendChild(input);
        socialsInputContainer.appendChild(deleteButton);
        socialsContainer.appendChild(socialsInputContainer);

        updateAddButton(socialsContainer, 'Add social', addSocial);
    }

    function deleteSocial(deleteButton) {
        const socialInputContainer = deleteButton.parentNode;
        const socialContainer = socialInputContainer.parentNode;
        socialContainer.removeChild(socialInputContainer);
    }

    function addPhoneNumber() {
        const phoneNumberContainer = document.querySelector('.phoneNumber_container');
        const phoneNumberInputContainer = document.createElement('div');
        phoneNumberInputContainer.classList.add('phoneNumber_input_container');

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'phone_number[]';
        input.required = true;

        const deleteButton = document.createElement('span');
        deleteButton.classList.add('deletePhoneNumber');
        deleteButton.textContent = '-';
        deleteButton.onclick = function () {
            phoneNumberContainer.removeChild(phoneNumberInputContainer);
            updateAddButton(phoneNumberContainer, 'Add phone number', addPhoneNumber);
        };

        phoneNumberInputContainer.appendChild(input);
        phoneNumberInputContainer.appendChild(deleteButton);
        phoneNumberContainer.appendChild(phoneNumberInputContainer);

        updateAddButton(phoneNumberContainer, 'Add phone number', addPhoneNumber);
    }

    function deletePhoneNumber(deleteButton) {
        const phoneNumberInputContainer = deleteButton.parentNode;
        const phoneNumberContainer = phoneNumberInputContainer.parentNode;
        phoneNumberContainer.removeChild(phoneNumberInputContainer);
    }

    function updateAddButton(container, buttonText, clickHandler) {
        let addButton = container.querySelector('.addButton');
        if (addButton) {
            container.removeChild(addButton);
        }

        addButton = document.createElement('span');
        addButton.classList.add('addButton');
        addButton.innerHTML = `<span class="plus">+</span> ${buttonText}`;
        addButton.onclick = clickHandler;

        container.appendChild(addButton);
    }

    updateAddButton(document.querySelector('.socials_container'), 'Add social', addSocial);
    updateAddButton(document.querySelector('.phoneNumber_container'), 'Add phone number', addPhoneNumber);
</script>


</body>
</html>