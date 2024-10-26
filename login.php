<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials
    $sql = "SELECT * FROM user WHERE mail='$email' AND pass='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, set session variable
        $_SESSION['user_email'] = $email;

        // Update the curr column with the user's email (only the first entry)
        $updateSql = "UPDATE user SET curr='$email' WHERE id = (SELECT MIN(id) FROM user)";
        if ($conn->query($updateSql) === TRUE) {
            // Successful update, redirect to creator page
            header("Location: creator.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post">
        <label for="email">Email:</label>
        <input type="text" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
