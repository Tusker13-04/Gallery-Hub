<?php
session_start();
include 'db.php';

$sql = "SELECT curr FROM user LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentUser = $row['curr'];
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['art']) && isset($_POST['art_desc']) && isset($_POST['art_genre'])) {
        $artFile = $_FILES['art'];
        $artName = $artFile['name'];
        $artDesc = $_POST['art_desc'];
        $artGenre = $_POST['art_genre'];

        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($artName);

        if (move_uploaded_file($artFile['tmp_name'], $targetFile)) {
            $sqlInsert = "UPDATE user SET art='$targetFile', art_desc='$artDesc', art_genre='$artGenre' WHERE mail='$currentUser'";
            if ($conn->query($sqlInsert) === TRUE) {
                $successMessage = "Your creation has been uploaded successfully!";
            } else {
                $errorMessage = "Error storing the file and details in the database: " . $conn->error;
            }
        } else {
            $errorMessage = "Sorry, there was an error uploading your file.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Creator Studio - Gallery Hub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        :root {
            --primary: #6d28d9;
            --primary-light: #7c3aed;
            --secondary: #db2777;
            --secondary-light: #ec4899;
            --background: #f5f3ff;
            --text: #1f2937;
            --text-light: #4b5563;
        }

        body {
            background-image: url('./download.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            line-height: 1.6;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            z-index: -1;
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .main-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 7rem 2rem 4rem;
        }

        .creator-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .creator-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }

        .creator-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .upload-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .upload-form:hover {
            transform: translateY(-5px);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 40, 217, 0.2);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--secondary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--text-light);
            color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: white;
            transform: translateY(-2px);
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .status-message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease-out;
        }

        .success-message {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #059669;
        }

        .error-message {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #ef4444;
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: block;
            padding: 1rem;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: #e5e7eb;
            border-color: var(--primary);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 6rem 1rem 2rem;
            }

            .creator-header h1 {
                font-size: 2rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                margin: 0.25rem 0;
            }
        }
        
        .analyze-button {
            display: block;
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--primary-light);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .analyze-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .analysis-result {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        
    </style>
</head>
<body>
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
                <div class="form-group">
                    <label for="art">Upload Your Creation</label>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-label">
                            <input type="file" name="art" id="art" style="display: none;" required>
                            <span>Click to choose a file or drag it here</span>
                        </label>
                    </div>
                    <button type="button" id="analyze-button" class="analyze-button">Analyze Image</button>
                    <div id="analysis-result" class="analysis-result"></div>
                </div>

                <div class="form-group">
                    <label for="art_desc">Description</label>
                    <textarea name="art_desc" id="art_desc" class="form-control" rows="4" placeholder="Tell us about your creation..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="art_genre">Genre</label>
                    <input type="text" name="art_genre" id="art_genre" class="form-control" placeholder="e.g., Abstract, Portrait, Landscape..." required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Upload Creation</button>
                    <button type="button" class="btn btn-secondary" onclick="location.href='gallery.php'">View Gallery</button>
                    <button type="button" class="btn btn-outline" onclick="location.href='index.php'">Back to Home</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            const artInput = document.getElementById("art");
            const descInput = document.getElementById("art_desc");
            const genreInput = document.getElementById("art_genre");

            if (!artInput.files[0] || !descInput.value.trim() || !genreInput.value.trim()) {
                alert("Please fill in all fields before submitting.");
                return false;
            }
            return true;
        }

        // Update file input label with filename
        document.getElementById('art').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Click to choose a file or drag it here';
            e.target.closest('.file-upload-wrapper').querySelector('span').textContent = fileName;
        });

        // Image analysis functionality
        const imageInput = document.getElementById('art');
        const analyzeButton = document.getElementById('analyze-button');
        const analysisResult = document.getElementById('analysis-result');
        //const genreInput = document.getElementById('art_genre');
        const descInput =document.getElementById('art_desc')

        analyzeButton.addEventListener('click', handleAnalyze);

        async function handleAnalyze() {
            const file = imageInput.files[0];
            if (!file) {
                alert('Please select an image first');
                return;
            }

            analyzeButton.disabled = true;
            analysisResult.textContent = 'Analyzing...';

            try {
                // Convert image file to Base64
                const base64Image = await toBase64(file);
                
                // Send the Base64 encoded image to the Ollama LLaVA model
                const payload = {
                    model: "x/llama3.2-vision:latest",
                    prompt: "Generate a list of descriptive tags for the given image. Use a bag of words and ensure the tags are relevant and dicriptive, such that even a blind person can visualize the image from the given. each word must be separate by comma, don't use prepositions.",
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
                console.log("Raw response:", analysisData);
                const analysisData = await analysisResponse.json();
                
                // Display the response in both the analysis result div and genre input
                //analysisResult.textContent = `Analysis Tags: ${analysisData.response}`;
                descInput.value = analysisData.response;
                //genreInput.value = analysisData.response;

            } catch (err) {
                analysisResult.textContent = `Error: ${err.message}`;
            } finally {
                analyzeButton.disabled = false;
            }
        }

        // Utility function to convert a file to Base64
        function toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result.split(',')[1]); // Get only the Base64 part
                reader.onerror = error => reject(error);
            });
        }
    </script>
</body>
</html>