<?php
// Start the session to access the current user's data
session_start();
// Include the database connection file
include 'db.php';

// Query to retrieve the current user's ID
$sql = "SELECT curr FROM user LIMIT 1";
$result = $conn->query($sql);

// Check if the query returned any results
if ($result->num_rows > 0) {
    // Fetch the current user's ID
    $row = $result->fetch_assoc();
    $currentUser = $row['curr'];
} else {
    // Redirect to the index page if no user is found
    header("Location: index.php");
    exit();
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    if (isset($_FILES['art']) && isset($_POST['art_desc']) && isset($_POST['art_genre'])) {
        // Get the uploaded file and form data
        $artFile = $_FILES['art'];
        $artName = $artFile['name'];
        $artDesc = $_POST['art_desc'];
        $artGenre = $_POST['art_genre'];

        // Set the target directory for the uploaded file
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($artName);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($artFile['tmp_name'], $targetFile)) {
            // Update the user's data in the database
            $sqlInsert = "UPDATE user SET art='$targetFile', art_desc='$artDesc', art_genre='$artGenre' WHERE mail='$currentUser'";
            if ($conn->query($sqlInsert) === TRUE) {
                // Set a success message
                $successMessage = "Your creation has been uploaded successfully!";
            } else {
                // Set an error message if the database update fails
                $errorMessage = "Error storing the file and details in the database: " . $conn->error;
            }
        } else {
            // Set an error message if the file upload fails
            $errorMessage = "Sorry, there was an error uploading your file.";
        }
    } else {
        // Set an error message if not all required fields are set
        $errorMessage = "Please fill in all fields.";
    }
}
?>

<!-- HTML and CSS code for the page layout and design -->

<!DOCTYPE html>
<html>
<head>
    <title>Creator Studio - Gallery Hub</title>
    <style>
        /* CSS styles for the page layout and design */
    </style>
</head>
<body>
    <!-- HTML code for the page content -->

    <div class="overlay"></div>
    <nav class="nav">
        <div class="nav-container">
            <div class="logo">Gallery Hub</div>
        </div>
    </nav>

    <div class="main-container">
        <div class="creator-header">
            <h1>Creator Studio</h1>
            <p>Welcome back, <?php echo htmlspecialchars($currentUser); ?>! Share your creative vision with the world.</p>
        </div>

        <?php if (isset($successMessage)): ?>
            <div class="status-message success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="status-message error-message">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="upload-form">
            <form method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
                <!-- Form fields for uploading a file and entering a description and genre -->
            </form>
        </div>
    </div>

    <script>
        // JavaScript code for form validation and image analysis

        function validateForm() {
            // Check if all required fields are filled in
            const artInput = document.getElementById("art");
            const descInput = document.getElementById("art_desc");
            const genreInput = document.getElementById("art_genre");

            if (!artInput.files[0] || !descInput.value.trim() || !genreInput.value.trim()) {
                alert("Please fill in all fields before submitting.");
                return false;
            }
            return true;
        }

        
    </script>
</body>
</html>
