<?php
include "config.php";

$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$fromDate = $_GET['fromDate'];
$toDate   = $_GET['toDate'];

$query = "SELECT * FROM room
WHERE roomID NOT IN (
    SELECT roomID FROM booking
    WHERE checkindate >= ?
    AND checkoutdate <= ?
)";

$stmt = mysqli_prepare($DBC, $query);
mysqli_stmt_bind_param($stmt, 'ss', $fromDate, $toDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['roomID']   . "</td>";
        echo "<td>" . $row['roomname'] . "</td>";
        echo "<td>" . $row['roomtype'] . "</td>";
        echo "<td>" . $row['beds']     . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No rooms available for the selected dates.</td></tr>";
}

mysqli_stmt_close($stmt);
mysqli_close($DBC);
?>
