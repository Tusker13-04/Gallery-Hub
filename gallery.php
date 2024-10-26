<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

// Fetch only users with uploaded art
$sql = "SELECT art, mail FROM user WHERE art IS NOT NULL AND art <> ''"; // Ensure art is not null or empty
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gallery</title>
    <style>
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        .gallery-item {
            text-align: center;
        }
        .gallery-item img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Welcome to the Gallery</h1>
    <div class="gallery">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="gallery-item">
                    <button style="background: none; border: none; padding: 0; cursor: pointer;">
                        <img src="<?php echo htmlspecialchars($row['art']); ?>" alt="Art" />
                    </button>
                    <p><?php echo htmlspecialchars($row['mail']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No creations found in the gallery.</p>
        <?php endif; ?>
    </div>
    
    <button onclick="location.href='index.php'">Back to Home</button>
</body>
</html>
