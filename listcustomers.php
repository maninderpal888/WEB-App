<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Browse Customers with AJAX Autocomplete</title>
    <script>
        
        function searchResult(searchstr) {
            if (searchstr.length == 0) {
                return;
            }
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Parse the JSON response
                    var mbrs = JSON.parse(this.responseText);
                    var tbl = document.getElementById("tblcustomers");

                    // Clear previous rows
                    var rowCount = tbl.rows.length;
                    for (var i = 1; i < rowCount; i++) {
                        tbl.deleteRow(1);
                    }

                    // Populate the table with customer data
                    for (var i = 0; i < mbrs.length; i++) {
                        var mbrid = mbrs[i]['customerID'];
                        var fn = mbrs[i]['firstname'];
                        var ln = mbrs[i]['lastname'];

                        var urls = '<a href="viewcustomer.php?id=' + mbrid + '">[view]</a>';
                        urls += '<a href="editcustomer.php?id=' + mbrid + '">[edit]</a>';
                        urls += '<a href="deletecustomer.php?id=' + mbrid + '">[delete]</a>';

                        tr = tbl.insertRow(-1);
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = fn;
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = ln;
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = urls;
                    }
                }
            }
            xmlhttp.open("GET", "customersearch.php?sq=" + searchstr, true);
            xmlhttp.send();
        }
    </script>
</head>

<body>
    <?php
    // Start the session and include session checking
    session_start();
    include "checksession.php";

    // Check if the user is logged in
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1;
    ?>

    <h1>Customer List Search by Lastname</h1>
    <h2>
        <?php if ($isLoggedIn) { ?>
            <a href='registercustomer.php'>[Create new Customer]</a>
        <?php } ?>
        <a href="/bnb/">[Return to main page]</a>
    </h2>

    <form>
        <label for="lastname">Lastname: </label>
        <input id="lastname" type="text" size="30" onkeyup="searchResult(this.value)" 
               onclick="javascript: this.value = ''" 
               placeholder="Start typing a last name">
    </form>

    <table id="tblcustomers" border="1">
        <thead>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>

    <?php
    // Display user info if logged in
    if ($isLoggedIn) {
        $username = $_SESSION['username'];
        echo "<h6>Logged in as $username</h6>";
    ?>

        <form method="post">
            <input type="submit" name="logout" value="Logout">
        </form>

        <?php
        // Handle logout
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    } else {
        echo "<h6>Please log in to access customer registration and management.</h6>";
    }
    ?>
</body>

</html>
