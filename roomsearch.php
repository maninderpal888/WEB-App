<?php
include "config.php"; // Load database configuration

header('Content-Type: application/json; charset=utf-8');

// Create a new database connection
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Check if the required POST parameters are set
if (isset($_POST['fromDate']) && isset($_POST['toDate'])) {
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];

    // Prepare the SQL query
    $query = "
        SELECT * FROM room
        WHERE roomID NOT IN (
            SELECT roomID FROM bookings 
            WHERE checkindate <= ? AND checkoutdate >= ?
        )
    ";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $toDate, $fromDate); // Bind parameters
        $stmt->execute();
        $result = $stmt->get_result();

        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }

        echo json_encode($rooms);
    } else {
        echo json_encode(["error" => "Failed to prepare query."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Missing required parameters."]);
}

$conn->close();
