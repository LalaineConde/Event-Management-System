<?php
require 'connection.php';
error_reporting(E_ERROR | E_PARSE);
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve the client ID from the URL parameters
    $id = mysqli_real_escape_string($connection, $_GET['id']);

    $query = "SELECT * FROM client_details WHERE id='$id'";
    $sql = mysqli_query($connection, $query);

    if (!$sql) {
        die("Query failed: " . mysqli_error($connection));
    }

    $socialsQuery = "SELECT * FROM socials WHERE client_id='$id'";
    $phoneNumbersQuery = "SELECT * FROM phone_numbers WHERE client_id='$id'";

    $socialsResult = mysqli_query($connection, $socialsQuery);
    $phoneNumbersResult = mysqli_query($connection, $phoneNumbersQuery);

    $socials = [];
    $phoneNumbers = [];

    while ($socialRow = mysqli_fetch_assoc($socialsResult)) {
        $socials[] = $socialRow['social'];
    }

    while ($phoneNumberRow = mysqli_fetch_assoc($phoneNumbersResult)) {
        $phoneNumbers[] = $phoneNumberRow['phone_number'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitForm'])) {
    
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $client_fullname = mysqli_real_escape_string($connection, $_POST['clientName']);

    
    $updateQuery = "UPDATE client_details SET fullname = ? WHERE id = ?";
    
    $stmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'si', $client_fullname, $id);

    if (mysqli_stmt_execute($stmt)) {

        // First, delete existing socials and phone numbers
        $deleteSocialsQuery = "DELETE FROM socials WHERE client_id = ?";
        $deletePhoneNumbersQuery = "DELETE FROM phone_numbers WHERE client_id = ?";
        
        $stmtDeleteSocials = mysqli_prepare($connection, $deleteSocialsQuery);
        mysqli_stmt_bind_param($stmtDeleteSocials, 'i', $id);
        mysqli_stmt_execute($stmtDeleteSocials);

        $stmtDeletePhoneNumbers = mysqli_prepare($connection, $deletePhoneNumbersQuery);
        mysqli_stmt_bind_param($stmtDeletePhoneNumbers, 'i', $id);
        mysqli_stmt_execute($stmtDeletePhoneNumbers);

        // Insert updated socials
        if (isset($_POST['socials']) && is_array($_POST['socials'])) {
            foreach ($_POST['socials'] as $social) {
                $insertSocialQuery = "INSERT INTO socials (client_id, social) VALUES (?, ?)";
                $stmtInsertSocial = mysqli_prepare($connection, $insertSocialQuery);
                mysqli_stmt_bind_param($stmtInsertSocial, 'is', $id, $social);
                mysqli_stmt_execute($stmtInsertSocial);
            }
        }

        // Insert updated phone numbers
        if (isset($_POST['phone_number']) && is_array($_POST['phone_number'])) {
            foreach ($_POST['phone_number'] as $phoneNumber) {
                $insertPhoneNumberQuery = "INSERT INTO phone_numbers (client_id, phone_number) VALUES (?, ?)";
                $stmtInsertPhoneNumber = mysqli_prepare($connection, $insertPhoneNumberQuery);
                mysqli_stmt_bind_param($stmtInsertPhoneNumber, 'is', $id, $phoneNumber);
                mysqli_stmt_execute($stmtInsertPhoneNumber);
            }
        }
        header('location: client-management.php');
    } 
    else {
        echo "Error updating record: " . mysqli_error($connection);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Client Details</title>

    <style>

        body {
            font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            background: rgba(240, 180, 180, 0.519);
            margin:0 50px;
        }

        a{
        text-decoration: none;
        }

        .update_title {
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
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px 10px;
            margin: 0; 
        }

        .clientName_container{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label.clientName{
            font-size: 18px;
            margin: 20px 0;
        }

        #clientName{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
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
            /* gap: 10px; */
            align-items: center;
            width: 100%; 
            max-width: 500px;
            margin-bottom: 10px; 
        }

        label.socials{
            font-size: 18px;
            margin: 20px 0;
        }

        #socials{
            background: rgba(173, 216, 236, 0.9);
            color: #000000;
            border: 1.5px solid #000000; */
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
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
            font-size: 18px;
            margin: 20px 0;
        }

        #phoneNumber{
            background: rgba(173, 216, 236, 0.9);
            width: 50%;
            max-width: 500px;
            width: calc(100% - 20px); 
            height: 30px;
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
            height: 30px;
            border-radius: 10px;
            font-size: 18px;
            padding: 10px;
            margin-bottom: 20px;
            background: rgba(173, 216, 236, 0.9);
        }

        .deleteButton {
            cursor: pointer;
            color: red;
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
            border: 2px solid #000000;
            border-radius: 7px;
            box-shadow: 5.5px 5.5px 5px #333333;
        }

</style>

</head>
<body>

    
    <div class="form_container">
    <h1 class="update_title">Update Client Details</h1>
        <form action="edit-client-details.php" method="POST">
            <?php while ($row = mysqli_fetch_array($sql)) { ?>

                <div class="clientId_container">
    <label for="clientId" class="clientId">CLIENT ID</label>
    <input type="text" id="clientId" name="id" value="<?php echo $row['id']; ?>" readonly><br><br>
</div>

<div class="clientName_container">
    <label for="clientName" class="clientName">CLIENT NAME</label>
    <input type="text" id="clientName" name="clientName" value="<?php echo $row['fullname']; ?>"required><br>
</div>

<div class="row">
                    <div class="col">
                        <label for="socials" class="socials">SOCIAL TYPE</label>
                        <div class="socials_container">
                            <?php foreach ($socials as $social) { ?>
                                <div class="socials_input_container">
                                    <input type="text" id="socials" name="socials[]" value="<?php echo htmlspecialchars($social); ?>" required>
                                    <span class="deleteSocial" onclick="deleteSocial(this)">-</span>
                                
                                </div>
                            <?php } ?>
                            <span class="addButton" onclick="addSocial()"><span class="plus">+</span> Add social</span>
                        </div>
                    </div>

                    <div class="col2">
                        <label for="phoneNumber" class="phoneNumber">PHONE TYPE</label>
                        <div class="phoneNumber_container">
                            <?php foreach ($phoneNumbers as $phoneNumber) { ?>
                                <div class="phoneNumber_input_container">
                                    <input type="text" id="phoneNumber" name="phone_number[]" value="<?php echo htmlspecialchars($phoneNumber); ?>" required>
                                    <span class="deletePhoneNumber" onclick="deletePhoneNumber(this)">-</span>
                                    
                                </div>
                            <?php } ?>
                            <span class="addButton" onclick="addPhoneNumber()"><span class="plus">+</span> Add phone number</span>
                        </div>
                    </div>
                </div>

                <div class="task_submit_container">
                    <button type="submit" name="submitForm" class="submit">SAVE</button>
                </div>

            <?php } ?>


    

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