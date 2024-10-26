<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

// Fetch the current user email from the 'curr' column (index 0)
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

        async function handleAnalyze() {
            const imageInput = document.getElementById('art');
            const analyzeButton = document.getElementById('analyze-button');
            const analysisResult = document.getElementById('analysis-result');

            const file = imageInput.files[0];
            if (!file) {
                alert('Please select an image first.');
                return;
            }

            analyzeButton.disabled = true;
            analysisResult.textContent = 'Analyzing...';

            try {
                // Convert image file to Base64
                const base64Image = await toBase64(file);

                // Send the Base64 encoded image to the Ollama LLaVA model
                const payload = {
                    model: "llava:latest",
                    prompt: "Generate a list of descriptive tags for the given image. Use a bag of words and ensure the tags are relevant and dicriptive, such that even a blind person can visualizw the image from the given each word must be separate by comma, don't use prepositions.",
                    images: [base64Image],
                    format: "json",
                    stream: false,
                };

                const analysisResponse = await fetch('http://127.0.0.1:11434/api/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                if (!analysisResponse.ok) {
                    throw new Error('Failed to analyze image');
                }

                const analysisData = await analysisResponse.json();
                analysisResult.textContent = `Response:\n${analysisData.response}`;
                
                // Automatically populate the genre field with the first few tags
                const tags = analysisData.response.split(',').slice(0, 3).join(', ');
                document.getElementById('art_genre').value = tags;
            } catch (err) {
                analysisResult.textContent = `Error: ${err.message}`;
            } finally {
                analyzeButton.disabled = false;
            }
        }

        function toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result.split(',')[1]);
                reader.onerror = error => reject(error);
            });
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
                <button type="button" id="analyze-button" class="btn btn-info mt-2" onclick="handleAnalyze()">Analyze Image</button>
                <div id="analysis-result" class="mt-2 p-2 border rounded"></div>
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
