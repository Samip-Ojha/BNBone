<!DOCTYPE HTML>
<html>
<head>
  <title>Make a Booking</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function() {
  var dateFormat = "yy-mm-dd";

  $("#checkindate").datepicker({
    dateFormat: dateFormat,
  });

  $("#checkoutdate").datepicker({
    dateFormat: dateFormat,
  });

  $("#search_start").datepicker({
    dateFormat: dateFormat,
  });

  $("#search_end").datepicker({
    dateFormat: dateFormat,
  });
});

function searchRooms() {
    var fromDate = $("#search_start").val();
    var toDate   = $("#search_end").val();

    if (fromDate === "" || toDate === "") {
        alert("Please select both a start date and an end date.");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            $("#result").html(this.responseText);
            $("#availability_table").show();
        }
    };
    xhttp.open("GET", "roomsearch.php?fromDate=" + fromDate + "&toDate=" + toDate, true);
    xhttp.send();
}
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

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
    $error = 0;
    $msg = 'Error: ';

    if (isset($_POST['roomID']) and !empty($_POST['roomID']) and is_numeric($_POST['roomID'])) {
        $roomID = cleanInput($_POST['roomID']);
    } else {
        $error++;
        $msg .= 'Invalid room ';
        $roomID = 0;
    }

    $customerID     = 1;
    $checkindate   = cleanInput($_POST['checkindate']);
    $checkoutdate  = cleanInput($_POST['checkoutdate']);
    $contactnumber = cleanInput($_POST['contactnumber']);
    $extras = cleanInput($_POST['extras']);
    $roomreview = cleanInput($_POST['roomreview']);

    if ($error == 0) {
$query = "INSERT INTO booking (roomID, customerID, checkindate, checkoutdate, contactnumber, extras, roomreview) VALUES (?,?,?,?,?,?,?)";        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'iisssss', $roomID, $customerID, $checkindate, $checkoutdate, $contactnumber, $extras, $roomreview);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking added successfully.</h2>";
    } else {
        echo "<h2>$msg</h2>" . PHP_EOL;
    }
}

$queryRooms = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomname';
$resultRooms = mysqli_query($DBC, $queryRooms);
$rowcount = mysqli_num_rows($resultRooms);
?>

<h1>Make a booking</h1>
<h2>
  <a href="listbookings.php">[Return to the Bookings listing]</a>
  <a href="/bnb/">[Return to the main page]</a>
</h2>

<h3>Booking for Test</h3>

<form method="POST" action="make_booking.php">

  <p>
    <label for="roomID">Room (name,type,beds): </label>
    <select id="roomID" name="roomID" required>
      <option value="">Select a room</option>
      <?php
      if ($rowcount > 0) {
          while ($roomRow = mysqli_fetch_assoc($resultRooms)) {
              echo '<option value="' . $roomRow['roomID'] . '">'
                  . $roomRow['roomname'] . ', ' . $roomRow['roomtype'] . ', ' . $roomRow['beds']
                  . '</option>';
          }
      } else {
          echo '<option>No rooms found</option>';
      }
      mysqli_free_result($resultRooms);
      ?>
    </select>
  </p>

  <p>
    <label for="checkindate">Checkin date: </label>
    <input type="text"
           id="checkindate"
           name="checkindate"
           placeholder="yyyy-mm-dd"
           required>
  </p>

  <p>
    <label for="checkoutdate">Checkout date: </label>
    <input type="text"
           id="checkoutdate"
           name="checkoutdate"
           placeholder="yyyy-mm-dd"
           required>
  </p>

  <p>
    <label for="contactnumber">Contact number: </label>
    <input type="text" id="contactnumber" name="contactnumber"
           placeholder="(###) ### ####"
           pattern="\(\d{3}\) \d{3} \d{4}"
           required>
  </p>

  <p>
    <label for="extras">Booking extras: </label>
    <textarea id="extras" name="extras" rows="4" cols="30"></textarea>
  </p>

  <p>
    <label for="roomreview" style="vertical-align: bottom;">Room review: </label>
    <textarea id="roomreview" name="roomreview" rows="4" cols="30"></textarea>
  </p>

  <input type="submit" name="submit" value="Add">
  <a href="listbookings.php">[Cancel]</a>

</form>

<hr>
<h3>Search for room availability</h3>

<p>
  <label for="search_start">Start date: </label>
  <input type="text" id="search_start" placeholder="yyyy-mm-dd" readonly>

  <label for="search_end">End date: </label>
  <input type="text" id="search_end" placeholder="yyyy-mm-dd" readonly>

  <button type="button" id="searchBtn" onclick="searchRooms()">Search availability</button>
</p>

<table id="availability_table" border="1" style="display:none;">
  <thead>
    <tr>
      <th>Room #</th>
      <th>Room Name</th>
      <th>Room Type</th>
      <th>Beds</th>
    </tr>
  </thead>
  <tbody id="result"></tbody>
</table>

<?php
mysqli_close($DBC);
?>

</body>
</html>
