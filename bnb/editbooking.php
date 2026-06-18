<!DOCTYPE HTML>
<html>
<head>
  <title>Edit a Booking</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
  <script>
$(function() {
  var dateFormat = "yy-mm-dd";

  $("#checkin_date").datepicker({
    dateFormat: dateFormat,
  });

  $("#checkout_date").datepicker({
    dateFormat: dateFormat,
  });
});
  </script>
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

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
    $id            = cleanInput($_POST['bookingID']);
    $roomID        = cleanInput($_POST['roomID']);
    $checkin_date  = cleanInput($_POST['checkindate']);
    $checkout_date = cleanInput($_POST['checkoutdate']);
    $contact_number= cleanInput($_POST['contactnumber']);
    $extras= cleanInput($_POST['extras']);
    $room_review   = cleanInput($_POST['roomreview']);

    $query = "UPDATE booking SET roomID=?, checkindate=?, checkoutdate=?, contactnumber=?, extras=?, roomreview=? WHERE bookingID=?";
    $stmt = mysqli_prepare($DBC, $query);
    mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $extras, $roomreview, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<h2>Booking details updated.</h2>";
}

$query = 'SELECT booking.bookingID, booking.roomID, room.roomname, room.roomtype, room.beds, booking.checkindate, booking.checkoutdate, booking.contactnumber, booking.extras, booking.roomreview
FROM booking
INNER JOIN room ON booking.roomID = room.roomID
WHERE booking.bookingID=' . $id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

$queryRooms = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomname';
$resultRooms = mysqli_query($DBC, $queryRooms);
?>

<h1>Edit a booking</h1>
<h2>
  <a href="listbookings.php">[Return to the Bookings listing]</a>
  <a href="/bnb/">[Return to the main page]</a>
</h2>

<?php
if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);
?>

<h3>Booking made for Test</h3>

<form method="POST" action="editbooking.php">

  <input type="hidden" name="bookingID" value="<?php echo $id; ?>">

  <p>
    <label for="roomID">Room (name,type,beds): </label>
    <select id="roomID" name="roomID" required>
      <option value="">Select a room</option>
      <?php
      while ($roomRow = mysqli_fetch_assoc($resultRooms)) {
          $selected = ($roomRow['roomID'] == $row['roomID']) ? 'selected' : '';
          echo '<option value="' . $roomRow['roomID'] . '" ' . $selected . '>'
              . $roomRow['roomname'] . ', ' . $roomRow['roomtype'] . ', ' . $roomRow['beds']
              . '</option>';
      }
      ?>
    </select>
  </p>

  <p>
    <label for="checkin_date">Checkin date: </label>
    <input type="text" id="checkin_date" name="checkindate"
           placeholder="yyyy-mm-dd"
           value="<?php echo $row['checkindate']; ?>"
           required>
  </p>

  <p>
    <label for="checkout_date">Checkout date: </label>
    <input type="text" id="checkout_date" name="checkoutdate"
           placeholder="yyyy-mm-dd"
           value="<?php echo $row['checkoutdate']; ?>"
           required>
  </p>

  <p>
    <label for="contact_number">Contact number: </label>
    <input type="text" id="contact_number" name="contactnumber"
           placeholder="(###) ### ####"
           pattern="\(\d{3}\) \d{3} \d{4}"
           value="<?php echo $row['contactnumber']; ?>"
           required>
  </p>

  <p>
    <label for="extras" style="vertical-align: bottom;">Booking extras: </label>
    <textarea id="extras" name="extras" rows="4" cols="30"><?php echo $row['extras']; ?></textarea>
  </p>

  <p>
    <label for="roomreview" style="vertical-align: bottom;">Room review: </label>
    <textarea id="roomreview" name="roomreview" rows="4" cols="30"><?php echo $row['roomreview']; ?></textarea>
  </p>

  <input type="submit" name="submit" value="Update">
  <a href="listbookings.php">[Cancel]</a>

</form>

<?php
} else {
    echo "<h2>No booking found with that ID</h2>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>
