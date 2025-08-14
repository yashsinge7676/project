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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $user_password = $_POST['password'];
        $email = $_POST['email'];
        $epassword = $_POST['epassword'];
        $cellno = $_POST['cellno'];

        // You may want to add validation and password hashing here
        $sql = "INSERT INTO user (name, password, email, epassword, cellno) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $user_password, $email, $epassword, $cellno);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to login.php after successful sign in
            header("Location: login.php");
            exit();
        } else {
            echo "<p style='color:red;'>Error storing data: " . mysqli_error($con) . "</p>";
        }
        mysqli_stmt_close($stmt);
    }
    ?>

    <form method="post" action="" class="singin-form">
        <label for="name">Username:</label>
        <input type="text" id="name" name="name" class="singin-input" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" class="singin-input" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="singin-input" required>
        <label for="epassword">Confirm Password:</label>
        <input type="password" id="epassword" name="epassword" class="singin-input" required>
        <label for="cellno">Phone:</label>
        <input type="number" id="cellno" name="cellno" class="singin-input" required>
        <input type="submit" value="Sign In" class="singin-btn">
        <a href="home.php" class="singin-link">Go to Home</a>
    </form>
</body>
</html>