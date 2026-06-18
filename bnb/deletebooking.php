<!DOCTYPE HTML>
<html>
<head>
  <title>Booking Preview Before Deletion</title>
</head>
<body>

<?php
include "config.php";
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>";
        exit;
    }
}

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
    $error = 0;
    $msg = 'Error: ';

    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = cleanInput($_POST['id']);
    } else {
        $error++;
        $msg .= 'Invalid booking ID ';
        $id = 0;
    }

    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking deleted.</h2>";
    } else {
        echo "<h2>$msg</h2>" . PHP_EOL;
    }
}

$query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate
FROM booking
INNER JOIN room ON booking.roomID = room.roomID
WHERE booking.bookingID=' . $id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Booking preview before deletion</h1>
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
    echo '</dl></fieldset>' . PHP_EOL;
?>

  <h2>Are you sure you want to delete this Booking?</h2>

  <form method="POST" action="deletebooking.php">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" name="submit" value="Delete">
    <a href="listbookings.php">[Cancel]</a>
  </form>

<?php
} else {
    echo "<h2>No booking found, possibly deleted!</h2>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>
