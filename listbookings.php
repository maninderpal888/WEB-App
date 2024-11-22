<?php

include "checksession.php";
include "config.php"; // Load in any variables

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check if the connection was successful
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

// Prepare the query and send it to the server
$query = 'SELECT b.bookingID, r.roomname, b.checkindate, b.checkoutdate, c.firstname, c.lastname
          FROM bookings b
          JOIN room r ON b.roomID = r.roomID
          JOIN customer c ON b.customerID = c.customerID';
$result = mysqli_query($DBC, $query);

// Check if the query was successful
if (!$result) {
    die('Error executing query: ' . mysqli_error($DBC));
}

$rowcount = mysqli_num_rows($result); 
?>
<h1>Current bookings</h1>
<h2>
  <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) { ?>
      <a href='addbooking.php'>[Make a booking]</a>
  <?php } ?>
  <a href="/bnb/">[Return to main page]</a>
</h2>
<table border="1">
<thead><tr><th>Booking (room, dates)</th><th>Customer</th><th>Action</th></tr></thead>
<?php

// Make sure we have bookings
if ($rowcount > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['bookingID'];    
        echo '<tr><td>'.$row['roomname'].', '.$row['checkindate'].', '.$row['checkoutdate'].'</td><td>'.$row['firstname'].', '.$row['lastname'].'</td>';
        echo '<td><a href="viewbooking.php?id='.$id.'">[view]</a>';
        echo '<a href="editbooking.php?id='.$id.'">[edit]</a>';
        echo '<a href="editreview.php?id='.$id.'">[manage reviews]</a>';
        echo '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
        echo '</tr>'.PHP_EOL;
    }
} else {
    echo "<h2>No bookings found!</h2>"; // Suitable feedback
}

mysqli_free_result($result); // Free any memory used by the query
mysqli_close($DBC); // Close the connection once done
?>
</table>
