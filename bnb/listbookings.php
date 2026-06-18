<!DOCTYPE HTML>
<html>
<head>
  <title>Current Bookings</title>
</head>
<body>

<?php
include "config.php";
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate, customer.firstname, customer.lastname
FROM booking
INNER JOIN room ON booking.roomID = room.roomID
INNER JOIN customer ON booking.customerID = customer.customerID
ORDER BY booking.bookingID';

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Current bookings</h1>
<h2>
  <a href="make_booking.php">[Make a booking]</a>
  <a href="/bnb/">[Return to main page]</a>
</h2>

<table border="1">
  <thead>
    <tr>
      <th>Booking (room, dates)</th>
      <th>Customer</th>
      <th>Action</th>
    </tr>
  </thead>

<?php
if ($rowcount > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['bookingID'];
        echo '<tr>';
        echo '<td>' . $row['roomname'] . ', ' . $row['checkindate'] . ', ' . $row['checkoutdate'] . '</td>';
        echo '<td>' . $row['lastname'] . ', ' . $row['firstname'] . '</td>';
        echo '<td>';
        echo '<a href="viewbooking.php?id=' . $id . '">[view]</a>';
        echo '<a href="editbooking.php?id=' . $id . '">[edit]</a>';
        echo '<a href="editreview.php?id=' . $id . '">[manage reviews]</a>';
        echo '<a href="deletebooking.php?id=' . $id . '">[delete]</a>';
        echo '</td>';
        echo '</tr>' . PHP_EOL;
    }
} else {
    echo "<tr><td colspan='3'><h2>No bookings found!</h2></td></tr>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</table>

</body>
</html>
