<!DOCTYPE HTML>
<html><head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="converted template/style/style.css">
    <title>Register new customer</title> </head>
<body>

<?php

include "header.php";
include "menu.php";
echo '<div id="site_content">';
include "sidebar.php";
echo '<div id="content">';
// Function to clean input but not validate type and content
function cleanInput($data) {  
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the form was submitted
if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Register')) {
    include "config.php"; // Load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; // Stop processing the page further
    }

    // Validate incoming data
    $error = 0; // Clear our error flag
    $msg = 'Error: ';

    // Firstname validation
    if (isset($_POST['firstname']) && !empty($_POST['firstname']) && is_string($_POST['firstname'])) {
        $fn = cleanInput($_POST['firstname']); 
        $firstname = (strlen($fn) > 50) ? substr($fn, 0, 50) : $fn; // Clip to 50 characters if too long      
    } else {
        $error++; // Bump the error flag
        $msg .= 'Invalid firstname '; // Append error message
        $firstname = '';  
    }

    // Lastname validation
    if (isset($_POST['lastname']) && !empty($_POST['lastname']) && is_string($_POST['lastname'])) {
        $ln = cleanInput($_POST['lastname']);
        $lastname = (strlen($ln) > 50) ? substr($ln, 0, 50) : $ln;
    } else {
        $error++;
        $msg .= 'Invalid lastname ';
        $lastname = '';
    }

    // Email validation
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = cleanInput($_POST['email']);
    } else {
        $error++;
        $msg .= 'Invalid email ';
        $email = '';
    }

    // Password validation
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = cleanInput($_POST['password']);
    } else {
        $error++;
        $msg .= 'Invalid password ';
        $password = '';
    }

    // Save the customer data if there are no validation errors
    if ($error == 0) {
        $query = "INSERT INTO customer (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($DBC, $query); // Prepare the query

        // Check if the statement was prepared successfully
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $firstname, $lastname, $email, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Customer saved</h2>";
        } else {
            echo "<h2>Error preparing the statement: " . mysqli_error($DBC) . "</h2>";
        }
    } else {
        echo "<h2>$msg</h2>";
    }

    mysqli_close($DBC); // Close the connection once done
}
?>

<h1>New Customer Registration</h1>
<h2><a href='listcustomers.php'>[Return to the Customer listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" action="registercustomer.php">
  <p>
    <label for="firstname">First Name: </label>
    <input type="text" id="firstname" name="firstname" minlength="5" maxlength="50" required> 
  </p> 
  <p>
    <label for="lastname">Last Name: </label>
    <input type="text" id="lastname" name="lastname" minlength="5" maxlength="50" required> 
  </p>  
  <p>  
    <label for="email">Email: </label>
    <input type="email" id="email" name="email" maxlength="100" size="50" required> 
   </p>
  <p>
    <label for="password">Password: </label>
    <input type="password" id="password" name="password" minlength="8" maxlength="32" required> 
  </p> 
  
   <input type="submit" name="submit" value="Register">
</form>
</body>
</html>
