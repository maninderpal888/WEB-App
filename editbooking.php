<!DOCTYPE HTML>
<html>
    <head>
    <title>Edit a booking</title> 
    <link
      rel="stylesheet"
      href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
    />
   </head>
 <body>

<?php


include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
  exit; //stop processing the page further
};

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the bookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>"; //simple error feedback
        exit;
    } 
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
  $error = 0; //clear our error flag
  $msg = 'Error: ';  
//validate incoming data - only the first field is done for you in this example - rest is up to you do
    
//bookingID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID '; //append error message
       $id = 0;  
    }   
//roomID
       $roomID = cleanInput($_POST['roomID']); 
//checkindate
       $checkindate = date_format(date_create($_POST['checkindate']),"Y-m-d");        
//checkoutdate
       $checkoutdate = date_format(date_create($_POST['checkoutdate']),"Y-m-d");         
//contactnumber
        $contactnumber = cleanInput($_POST['contactnumber']);         
//bookingextras
        $bookingextras = cleanInput($_POST['bookingextras']);         
//roomreview
        $roomreview = cleanInput($_POST['roomreview']);         
//check check in and check out date periord
        if ($checkindate >=$checkoutdate){
          $error++;
          $msg .="Check-out date cannot be earlier than or equal to check-in date";
      }

  
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE bookings SET roomID=?,checkindate=?,checkoutdate=?,contactnumber=?,bookingextras=?,roomreview=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'isssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $roomreview, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking details updated.</h2>";     
//        header('Location: http://localhost/bit608/listrooms.php', true, 303);      
    } else { 
      echo "<h2><font color='red'>$msg</font></h2>".PHP_EOL;
    }      
}
//locate the booking to edit by using the bookingID
//we also include the booking ID in our form for sending it back for saving the data
$query = 'SELECT b.*, r.roomname, r.roomtype, r.beds 
FROM bookings b, room r 
WHERE b.roomid = r.roomid AND bookingid='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);

  $queryRoom = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
  $resultRoom = mysqli_query($DBC, $queryRoom);
  $roomcount = mysqli_num_rows($resultRoom);


?>
<h1>Edit a booking</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" action="editbooking.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
   <p>
    <label for="roomID">Room (name,type,beds): </label>
    <select id="roomID" name="roomID" required> 
    <?php
      if ($roomcount > 0) {
          while ($rowR = mysqli_fetch_assoc($resultRoom)) {
              $id = $rowR['roomID']; ?>

              <option value="<?php echo $rowR['roomID']; ?>"
                      <?php echo $row['roomID']==$rowR['roomID']?'selected':''; ?> >
                  <?php echo $rowR['roomname'] . ', '
                      . $rowR['roomtype'] . ', '
                      . $rowR['beds'] ?>
              </option>
      <?php }
      } else echo "<option>No flights found</option>";
      mysqli_free_result($resultRoom);
    ?>
    </select>
  </p> 
  <p>
    <label for="checkindate">Checkin Date: </label>
    <input type="text" id="checkindate" name="checkindate" value="<?php echo date_format(date_create($row['checkindate']),"d-m-Y"); ?>" required/>
  </p>  
  <p>  
    <label for="checkoutdate">Checkout Date: </label>
    <input type="text" id="checkoutdate" name="checkoutdate" value="<?php echo date_format(date_create($row['checkoutdate']),"d-m-Y"); ?>" required/>
   </p>
   <p>
    <label for="contactnumber">Contact number: </label>
    <input type="tel" id="contactnumber" name="contactnumber" value="<?php echo $row['contactnumber']; ?>" required placeholder="(001) 123-1234" pattern="[\(]\d{3}[\)] \d{3}-\d{4}"> 
  </p>  
  <p>
    <label for="bookingextras">Booking Extras: </label>
    <textarea id="bookingextras" name="bookingextras" size="200" minlength="0" maxlength="200" rows="5" cols="50" > <?php echo $row['bookingextras']; ?></textarea>
  </p>  
  <p>
    <label for="roomreview">Room Review: </label>
    <textarea id="roomreview" name="roomreview" size="400" minlength="0" maxlength="400" rows="5" cols="50"> <?php echo $row['roomreview']; ?> </textarea>
  </p>  
   <input type="submit" name="submit" value="Update">
   <a href="listbookings.php">[Cancel]</a>
 </form>
<?php 
} else { 
  echo "<h2>booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
</body>

<?php
    echo '</div></div>';
    // include "footer.php";
?>


<script>
    $("#checkindate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
    $("#checkoutdate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
  </script>

</html>
  