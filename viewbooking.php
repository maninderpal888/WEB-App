<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="converted_template/style/style.css">
    <title>View Booking</title>
</head>
<body>

<?php


// Load database configuration
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check if the connection was successful
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; // Stop processing if the connection failed
}

// Validate booking ID
$id = $_GET['id'] ?? null; // Get the booking ID from the URL
if (empty($id) || !is_numeric($id)) {
    echo "<h2>Invalid Booking ID</h2>";
    exit;
}

// Prepare a query to retrieve booking details
$query = 'SELECT b.*, r.roomname 
          FROM bookings b 
          JOIN room r ON b.roomid = r.roomid
          WHERE b.bookingid = ?';

// Prepare and execute the query
$stmt = mysqli_prepare($DBC, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if the booking was found
if ($row = mysqli_fetch_assoc($result)) {
    ?>
    <h1>Booking Details View</h1>
    <h2>
        <a href="listbookings.php">[Return to the Booking listing]</a>
        <a href="/bnb/">[Return to the main page]</a>
    </h2>
    <fieldset>
        <legend>Booking Detail #<?php echo $id; ?></legend>
        <dl>
            <dt>Room Name:</dt><dd><?php echo htmlspecialchars($row['roomname']); ?></dd>
            <dt>Check-in Date:</dt><dd><?php echo htmlspecialchars($row['checkindate']); ?></dd>
            <dt>Check-out Date:</dt><dd><?php echo htmlspecialchars($row['checkoutdate']); ?></dd>
            <dt>Contact Number:</dt><dd><?php echo htmlspecialchars($row['contactnumber']); ?></dd>
            <dt>Booking Extras:</dt><dd><?php echo htmlspecialchars($row['bookingextras']); ?></dd>
            <dt>Room Review:</dt><dd><?php echo htmlspecialchars($row['roomreview']); ?></dd>
        </dl>
    </fieldset>
    <?php
} else {
    echo "<h2>No Booking found with ID: $id</h2>";
}

// Free result and close the connection
mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>
