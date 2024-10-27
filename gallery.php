<?php
// Start the session to access the current user's data
session_start();
include 'db.php';

// Query to retrieve artwork from the database, ordered by rating in descending order
$sql = "SELECT art, mail, rating FROM user WHERE art IS NOT NULL AND art != '' ORDER BY rating DESC";
$result = $conn->query($sql);
?>

<!-- HTML and CSS code for the page layout and design -->

<!DOCTYPE html>
<html>
<head>
    <title>Gallery Hub - Explore</title>
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
        <div class="gallery-header">
            <h1>Explore the Gallery</h1>
            <p>Discover amazing artwork from talented creators around the world</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <!-- Display the artwork grid if there are results -->
            <div class="gallery-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <!-- Display each artwork card -->
                    <div class="art-card">
                        <img src="<?php echo htmlspecialchars($row['art']); ?>" class="art-image" alt="Artwork">
                        <div class="art-content">
                            <!-- Display the creator's email and rating information -->
                            <div class="creator-info">
                                <a href="image_info.php?mail=<?php echo urlencode($row['mail']); ?>" class="creator-email">
                                    <?php echo htmlspecialchars($row['mail']); ?>
                                </a>
                            </div>

                            <div class="rating-container">
                                <?php
                                // Display the rating information, including the number of stars and the rating value
                                $rating = $row['rating'];
                                if ($rating == 0.0): ?>
                                    <div class="not-rated">Not rated yet</div>
                                <?php else: ?>
                                    <div class="star-rating">
                                        <?php
                                        // Loop through the rating stars and display the filled or empty stars accordingly
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo "<span class='filled'>★</span>";
                                            } else {
                                                echo "<span class='empty'>★</span>";
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="rating-value"><?php echo number_format($rating, 1); ?> out of 5</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Display a message if there are no results -->
            <div class="no-images">
                <h2>No Artwork Yet</h2>
                <p>Be the first to share your creation!</p>
            </div>
        <?php endif; ?>

        <!-- Display a back button to return to the home page -->
        <div style="text-align: center;">
            <a href="index.php" class="btn btn-back">Back to Home</a>
        </div>
    </div>

    <script>
        // Add an event listener to the document to load the images and display them with a fade-in effect
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.art-image');
            images.forEach(img => {
                img.style.opacity = '0';
                img.onload = function() {
                    img.style.transition = 'opacity 0.5s ease-in';
                    img.style.opacity = '1';
                };
            });
        });
    </script>
</body>
</html>
