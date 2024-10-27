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
            --gold: #fbbf24;
            --gold-light: #fcd34d;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 7rem 2rem 4rem;
        }

        .gallery-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .gallery-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }

        .gallery-header p {
            color: var(--text-light);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .art-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            animation: fadeIn 0.5s ease-out;
        }

        .art-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .art-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .art-content {
            padding: 1.5rem;
        }

        .creator-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .creator-email {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .creator-email:hover {
            transform: scale(1.05);
            color: white;
        }

        .rating-container {
            margin-top: 1rem;
        }

        .star-rating {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .star-rating .filled {
            color: var(--gold);
            text-shadow: 0 0 5px rgba(251, 191, 36, 0.3);
        }

        .star-rating .empty {
            color: #ddd;
        }

        .rating-value {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .not-rated {
            color: var(--text-light);
            font-style: italic;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-back {
            background: var(--text-light);
            color: white;
            margin-top: 2rem;
        }

        .btn-back:hover {
            background: var(--text);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .no-images {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 2rem auto;
            max-width: 500px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
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

            .gallery-header h1 {
                font-size: 2rem;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }

            .art-card {
                margin-bottom: 1rem;
            }
        }
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
