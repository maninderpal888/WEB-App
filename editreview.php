<!DOCTYPE HTML>
<html>
    <head>
    <title>Edit/add room review</title> 
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
//roomreview
        $roomreview = cleanInput($_POST['roomreview']);         
    
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE bookings SET roomreview=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $roomreview, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Room review updated.</h2>";     
    } else { 
      echo "<h2><font color='red'>$msg</font></h2>".PHP_EOL;
    }      
}
//locate the booking to edit by using the bookingID
//we also include the booking ID in our form for sending it back for saving the data
$query = 'SELECT * FROM bookings WHERE bookingid='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>
<h1>Edit/add room review</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
<h2>Review made by Test</h2>

<form method="POST" action="editreview.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">

  <p>
    <label for="roomreview">Room Review: </label>
    <textarea id="roomreview" name="roomreview" size="400" minlength="0" maxlength="400" rows="5" cols="50"> <?php echo $row['roomreview']; ?> </textarea>
  </p>  
   <input type="submit" name="submit" value="Update">
 </form>
<?php 
} else { 
  echo "<h2>booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>

<?php
    echo '</div></div>';
    // include "footer.php";
?>

</body>
</html>
  