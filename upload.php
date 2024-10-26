<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['user_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $art = $_POST['art'];
    $art_desc = $_POST['art_desc'];
    $art_genre = $_POST['art_genre'];

    // Prepare SQL to insert art details into the database
    $sql = "UPDATE user SET art='$art', art_desc='$art_desc', art_genre='$art_genre' WHERE mail='$email'";

    if ($conn->query($sql) === TRUE) {
        echo "Art uploaded successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Creation</title>
</head>
<body>
    <h1>Upload Your Creation</h1>
    <form method="post">
        <label for="art">Creation:</label><br>
        <input type="text" name="art" required><br><br>

        <label for="art_desc">Description:</label><br>
        <textarea name="art_desc" required></textarea><br><br>

        <label for="art_genre">Genre:</label><br>
        <input type="text" name="art_genre" required><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="location.href='creator.php'">Back to Creator Page</button>
</body>
</html>
