<!DOCTYPE HTML>
<html>
<head>
  <title>Edit/Add Room Review</title>
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
    $room_review = cleanInput($_POST['room_review']);
    $id          = cleanInput($_POST['id']);

    $query = "UPDATE booking SET roomreview=? WHERE bookingID=?";
    $stmt = mysqli_prepare($DBC, $query);
    mysqli_stmt_bind_param($stmt, 'si', $room_review, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "<h2>Room review updated.</h2>";
}

$query = 'SELECT roomreview FROM booking WHERE bookingID=' . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Edit/add room review</h1>
<h2>
  <a href="listbookings.php">[Return to the booking listing]</a>
  <a href="/bnb/">[Return to the main page]</a>
</h2>

<h3>Review made by Test</h3>

<form method="POST" action="editreview.php">

  <input type="hidden" name="id" value="<?php echo $id; ?>">

  <?php
  if ($rowcount > 0) {
      $row = mysqli_fetch_assoc($result);
  ?>

  <p>
    <textarea id="existing_review" name="existing_review" rows="4" cols="50"><?php echo $row['roomreview']; ?></textarea>
  </p>

  <p>
    <label for="room_review">Room review: </label>
    <textarea id="room_review" name="room_review"
              minlength="1" maxlength="500" rows="4" cols="50"></textarea>
  </p>

  <?php
  } else {
      echo "<h2>No booking found!</h2>";
  }

  mysqli_free_result($result);
  mysqli_close($DBC);
  ?>

  <input type="submit" name="submit" value="Update">

</form>

</body>
</html>
