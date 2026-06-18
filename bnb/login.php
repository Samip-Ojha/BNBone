<!DOCTYPE HTML>
<html>
<head>
  <title>Login</title>
</head>
<body>

<?php
session_start();

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['logout'])) {
    $_SESSION['loggedin'] = 0;
    $_SESSION['userid']   = -1;
    $_SESSION['username'] = '';
    header('Location: login.php', true, 303);
    exit();
}

if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
    include "config.php";
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit;
    }

    $error = 0;
    $msg = 'Error: ';

    if (isset($_POST['username']) and !empty($_POST['username']) and is_string($_POST['username'])) {
        $un = cleanInput($_POST['username']);
        $username = (strlen($un) > 100) ? substr($un, 0, 100) : $un;
    } else {
        $error++;
        $msg .= 'Invalid username ';
        $username = '';
    }

    $password = trim($_POST['password']);

    if ($error == 0) {
        $query = "SELECT customerID, email, password FROM customer WHERE email=?";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if ($password === $row['password']) {
                $_SESSION['loggedin'] = 1;
                $_SESSION['userid']   = $row['customerID'];
                $_SESSION['username'] = $username;
                header('Location: listbookings.php', true, 303);
                exit();
            } else {
                echo "<h2>Login failed. Incorrect password.</h2>" . PHP_EOL;
            }
        } else {
            echo "<h2>Login failed. User not found.</h2>" . PHP_EOL;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($DBC);
    } else {
        echo "<h2>$msg</h2>" . PHP_EOL;
    }
}
?>

<h1>Customer Login</h1>
<h2>
  <a href="/bnb/">[Return to the main page]</a>
</h2>

<form method="POST" action="login.php">
  <p>
    <label for="username">Username: </label>
    <input type="text" id="username" name="username" maxlength="100" required>
  </p>
  <p>
    <label for="password">Password: </label>
    <input type="password" id="password" name="password" maxlength="32" required>
  </p>
  <input type="submit" name="login" value="Login">
  <input type="submit" name="logout" value="Logout">
</form>

</body>
</html>
