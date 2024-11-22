<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Booking</title> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>

<?php
// Load database configuration
include "config.php"; 
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check connection
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; // Stop processing the page further
}

// Function to clean input
function cleanInput($data) {  
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize error message
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] == 'Add') {
    // Validate incoming data
    $roomID = $_POST['roomID'];        
    $customerID = 1; // Fixed customer ID for the example
    $checkindate = date_format(date_create($_POST['checkindate']), "Y-m-d");        
    $checkoutdate = date_format(date_create($_POST['checkoutdate']), "Y-m-d");         
    $contactnumber = cleanInput($_POST['contactnumber']);         
    $bookingextras = cleanInput($_POST['bookingextras']);         
    $roomreview = cleanInput($_POST['roomreview']); // New review input

    // Check check-in and check-out date period
    if ($checkindate >= $checkoutdate) {
        $error .= "Check-out date cannot be earlier than or equal to check-in date.<br>";
    }

    // Save the booking data if there are no errors
    if (empty($error)) {
        $query = "INSERT INTO bookings (roomid, customerid, checkindate, checkoutdate, contactnumber, bookingextras, roomreview) VALUES (?, ?, ?, ?, ?, ?, ?)"; // Updated query
        $stmt = mysqli_prepare($DBC, $query); // Prepare the query
        
        if (!$stmt) {
            echo "Error preparing statement: " . mysqli_error($DBC);
            exit; // Stop further processing
        }

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'iisssss', $roomID, $customerID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $roomreview); // Updated binding
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<h2>New booking added successfully!</h2>";        
        } else {
            echo "<h2><font color='red'>Error adding booking: " . mysqli_stmt_error($stmt) . "</font></h2>";
        }
        mysqli_stmt_close($stmt);    
    } else { 
        echo "<h2><font color='red'>$error</font></h2>";
    }      
}

// Fetch available rooms
$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

// Close the database connection
mysqli_close($DBC); 
?>

<h1>Add a New Booking</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST">
    <p>
        <label for="roomID">Room (name, type, beds): </label>
        <select id="roomID" name="roomID" required> 
            <?php
            if ($rowcount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <option value="<?php echo $row['roomID']; ?>">
                        <?php echo $row['roomname'] . ', ' . $row['roomtype'] . ', ' . $row['beds']; ?>
                    </option>
                    <?php
                }
            } else {
                echo "<option>No rooms found</option>";
            }
            mysqli_free_result($result);
            ?>
        </select>
    </p> 
    <p>

        <label for="checkindate">Check-in Date: </label>
        <input type="text" id="checkindate" name="checkindate" required placeholder="dd-mm-yyyy">
    </p>  
    <p>  
        <label for="checkoutdate">Check-out Date: </label>
        <input type="text" id="checkoutdate" name="checkoutdate" required placeholder="dd-mm-yyyy">
    </p>
    <p>
        <label for="contactnumber">Contact Number: </label>
        <input type="tel" id="contactnumber" name="contactnumber" required placeholder="(001) 123-1234" pattern="[\(]\d{3}[\)] \d{3}-\d{4}"> 
    </p>  
    <p>
        <label for="bookingextras">Booking Extras: </label>
        <textarea id="bookingextras" name="bookingextras" maxlength="200" rows="5" cols="50"></textarea>
    </p>
    <p>
        <label for="roomreview">Room Review: </label>
        <textarea id="roomreview" name="roomreview" maxlength="500" rows="5" cols="50"></textarea>
    </p> 
    <input type="submit" name="submit" value="Add">
    <a href="listbookings.php">[Cancel]</a>
</form>

<form id="searchForm" method="POST" action="roomsearch.php">
    <hr>
    <h2>Search for Room Availability</h2>
    <p>
        <input type="text" id="fromDate" name="sqa" required placeholder="From Date">
        <input type="text" id="toDate" name="sqb" required placeholder="To Date">
        <input type="submit" name="search" id="search" value="Search Availability">
    </p>
</form>

<br><br>

<div class="row">
    <table id="tblbookings" border="1">
        <thead>
            <tr>
              <th>Room #</th>
              <th>Room Name</th>
              <th>Room Type</th>
              <th>Beds</th>
            </tr>
        </thead>
        <tbody></tbody> <!-- Empty body for AJAX updates -->
    </table>
</div>
<script>
    $(function() {
        $("#checkindate, #checkoutdate, #fromDate, #toDate").datepicker({
            numberOfMonths: 1,
            changeYear: true,
            changeMonth: true,
            minDate: 0,
            dateFormat: 'yy-mm-dd',
        });

        $('#searchForm').submit(function(event) {
            event.preventDefault();
            const formData = {
                fromDate: $('#fromDate').val(),
                toDate: $('#toDate').val()
            };
            $.ajax({
                type: "POST",
                url: "roomsearch.php",
                data: formData,
                dataType: "json",
                success: function(data) {
                    const tbody = $("#tblbookings tbody");
                    tbody.empty();
                    if (data.length > 0) {
                        data.forEach(room => {
                            tbody.append(`<tr>
                                <td>${room.roomID}</td>
                                <td>${room.roomname}</td>
                                <td>${room.roomtype}</td>
                                <td>${room.beds}</td>
                            </tr>`);
                        });
                    } else {
                        tbody.append(`<tr><td colspan="4">No available rooms found.</td></tr>`);
                    }
                },
                error: function(err) {
                    console.error(err);
                    alert("Error retrieving data.");
                }
            });
        });
    });
</script>



</body>
</html>
