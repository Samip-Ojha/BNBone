<!DOCTYPE HTML>
<html>
<head>
  <title>Booking Details View</title>
</head>
<body>

<?php
include "config.php";
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
    echo "<h2>Invalid booking ID</h2>";
    exit;
}

$query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate, booking.contactnumber, booking.extras, booking.roomreview
FROM booking
INNER JOIN room ON booking.roomID = room.roomID
WHERE booking.bookingID=' . $id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Booking Details View</h1>
<h2>
  <a href="listbookings.php">[Return to the booking listing]</a>
  <a href="/bnb/">[Return to the main page]</a>
</h2>

<?php
if ($rowcount > 0) {
    echo "<fieldset><legend>Booking detail #$id</legend><dl>";
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkin date:</dt><dd>" . $row['checkindate'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkout date:</dt><dd>" . $row['checkoutdate'] . "</dd>" . PHP_EOL;
    echo "<dt>Contact number:</dt><dd>" . $row['contactnumber'] . "</dd>" . PHP_EOL;
    echo "<dt>Extras:</dt><dd>" . $row['extras'] . "</dd>" . PHP_EOL;
    echo "<dt>Room review:</dt><dd>" . $row['roomreview'] . "</dd>" . PHP_EOL;
    echo '</dl></fieldset>' . PHP_EOL;
} else {
    echo "<h2>No booking found!</h2>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>
