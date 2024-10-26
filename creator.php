<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

// Fetch the current user email from the curr column (index 0)
$sql = "SELECT curr FROM user LIMIT 1"; // Get the first entry
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Get the current user's email
    $row = $result->fetch_assoc();
    $currentUser = $row['curr'];
} else {
    // Redirect if no user is found (this should not happen if the login worked correctly)
    header("Location: index.php");
    exit();
}

// Handle file upload and data storage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['art']) && isset($_POST['art_desc']) && isset($_POST['art_genre'])) {
        $artFile = $_FILES['art'];
        $artName = $artFile['name'];
        $artDesc = $_POST['art_desc'];
        $artGenre = $_POST['art_genre'];

        // Move the uploaded file to a desired directory (e.g., 'uploads/')
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($artName);

        if (move_uploaded_file($artFile['tmp_name'], $targetFile)) {
            // Store the file path, description, and genre in the database for the current user
            $sqlInsert = "UPDATE user SET art='$targetFile', art_desc='$artDesc', art_genre='$artGenre' WHERE mail='$currentUser'";
            if ($conn->query($sqlInsert) === TRUE) {
                echo "The file has been uploaded successfully and details have been stored.";
            } else {
                echo "Error storing the file and details in the database: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Creator Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script>
        function validateForm() {
            const artInput = document.getElementById("art").value;
            const descInput = document.getElementById("art_desc").value;
            const genreInput = document.getElementById("art_genre").value;

            if (!artInput || !descInput || !genreInput) {
                alert("Please fill in all fields before submitting.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Hello Creator: <?php echo htmlspecialchars($currentUser); ?></h1>
        <h2>Upload Creation:</h2>
        
        <form method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
            <div class="form-group">
                <label for="art">Select creation to upload:</label>
                <input type="file" name="art" id="art" required>
            </div>
            <div class="form-group">
                <label for="art_desc">Description:</label>
                <textarea name="art_desc" id="art_desc" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="art_genre">Genre:</label>
                <input type="text" name="art_genre" id="art_genre" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        
        <h2>What would you like to do?</h2>
        <button class="btn btn-secondary" onclick="location.href='gallery.php'">View Gallery</button>
        <button class="btn btn-warning" onclick="location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>
