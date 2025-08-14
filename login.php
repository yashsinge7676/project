<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "website-01";

    $con = mysqli_connect($servername, $username, $password, $database);
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    session_start();
    $login_message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $user_password = $_POST['password'];

        $sql = "SELECT * FROM user WHERE name = ? AND password = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $name, $user_password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            // Store username in session and redirect to home.php
            $_SESSION['username'] = $name;
            header("Location: home.php");
            exit();
        } else {
            $login_message = "<p style='color:red;'>Invalid username or password.</p>";
        }
        mysqli_stmt_close($stmt);
    }
    ?>
    <?php if (!empty($login_message)) echo $login_message; ?>
    <form method="post" action="" class="login-form">
        <label for="name">Username:</label>
        <input type="text" id="name" name="name" class="login-input" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" class="login-input" required>
        <input type="submit" value="Login" class="login-btn">
    </form>
</body>
</html>