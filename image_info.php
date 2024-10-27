<?php
session_start();
include 'db.php';

if (isset($_GET['mail'])) {
    $creatorEmail = $_GET['mail'];
    $sql = "SELECT art, art_desc, art_genre, rating FROM user WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $creatorEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $artImage = htmlspecialchars($row['art']);
        $artDesc = htmlspecialchars($row['art_desc']);
        $artGenre = htmlspecialchars($row['art_genre']);
        $currentRating = $row['rating'] ?? 0;
    } else {
        echo "No details found for this creator.";
        exit();
    }
} else {
    echo "No creator email provided.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rating'])) {
    $newRating = $_POST['rating'];
    $newAverageRating = ($currentRating == 0) ? $newRating : ($currentRating + $newRating) / 2;
    $finalRating = round($newAverageRating);

    $updateSql = "UPDATE user SET rating=? WHERE mail=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("is", $finalRating, $creatorEmail);
   
    if ($updateStmt->execute()) {
        $thankYouMessage = "Thank you for your feedback!";
        $currentRating = $finalRating;
    } else {
        echo "Error updating the rating: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Artwork Details - Gallery Hub</title>
    <style>
        /* Base Styles */
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
            background-repeat: no-repeat;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Navigation */
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
            transition: transform 0.3s ease;
        }

        /* Main Content */
        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 7rem 2rem 4rem;
        }

        /* Art Card Styles */
        .art-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 2rem auto;
            max-width: 800px;
            animation: fadeIn 0.5s ease-out;
        }

        .art-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .art-image:hover {
            transform: scale(1.02);
        }

        .art-details {
            padding: 2rem;
        }

        .art-title {
            font-size: 2rem;
            color: var(--text);
            margin-bottom: 1rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .art-info {
            margin-bottom: 1.5rem;
            color: var(--text-light);
        }

        .art-genre {
            display: inline-block;
            background: var(--background);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Rating System */
        .rating-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .star-rating {
            direction: rtl;
            display: inline-flex;
            font-size: 2.5em;
            margin: 1rem 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0 0.1em;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffd700;
            transform: scale(1.1);
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
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

        .btn-back {
            background: var(--secondary);
            color: white;
            margin-right: 1rem;
        }

        .btn-back:hover {
            background: var(--secondary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
        }

        /* Success Message */
        .thank-you-message {
            background: #ecfdf5;
            color: #047857;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Current Rating Display */
        .current-rating {
            font-size: 1.2rem;
            color: var(--text);
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: rgba(109, 40, 217, 0.1);
            display: inline-block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main {
                padding: 6rem 1rem 2rem;
            }

            .art-container {
                margin: 1rem;
            }

            .art-image {
                height: 300px;
            }

            .art-details {
                padding: 1.5rem;
            }

            .art-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="nav-container">
            <div class="logo">Gallery Hub</div>
        </div>
    </nav>

    <main class="main">
        <div class="art-container">
            <img src="<?php echo $artImage; ?>" class="art-image" alt="Artwork">
            <div class="art-details">
                <h1 class="art-title">Artwork Details</h1>
                <span class="art-genre"><?php echo $artGenre; ?></span>
                <div class="art-info">
                    <p><strong>Creator:</strong> <?php echo htmlspecialchars($creatorEmail); ?></p>
                    <p><strong>Description:</strong> <?php echo $artDesc; ?></p>
                </div>

                <div class="rating-container">
                    <div class="current-rating">
                        Current Rating: <?php echo $currentRating != 0 ? number_format((float)$currentRating, 2, '.', '') : "No rating yet"; ?>
                    </div>

                    <form method="post">
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">★</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Rating</button>
                    </form>

                    <?php if (isset($thankYouMessage)): ?>
                        <div class="thank-you-message">
                            <?php echo $thankYouMessage; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="gallery.php" class="btn btn-back">Back to Gallery</a>
            </div>

            <div class="comment-section">
            <h2 style="text-align: center;">Write a comment</h2>

            <form method="post" action="" id="commentForm" style="text-align: center; margin-bottom: 20px;">
                <textarea name="new_comment" required placeholder="Tell us your thoughts!" style="width: 80%; height: 100px; border-radius: 5px; padding: 10px; font-size: 16px; border: 1px solid #ccc; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);"></textarea>
                <br>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px; padding: 10px 20px; font-size: 16px; border-radius: 5px;">Submit Comment</button>
            </form>

            <h2 style="text-align: center;">Comment Section</h2>
            
            <div class="existing-comments" id="commentsContainer" style="border: 1px solid #ccc; padding: 1rem; border-radius: 8px; margin-top: 1rem; background: #f9f9f9;">
                <?php
                // Fetch existing comments from the database
                $commentsSql = "SELECT comment FROM user WHERE mail = ?";
                $commentsStmt = $conn->prepare($commentsSql);
                $commentsStmt->bind_param("s", $creatorEmail);
                $commentsStmt->execute();
                $commentsResult = $commentsStmt->get_result();

                // Display existing comments
                if ($commentsResult->num_rows > 0) {
                    $commentsRow = $commentsResult->fetch_assoc();
                    $comments = $commentsRow['comment'];
                    $individualComments = explode("<?>", $comments);

                    $colors = ['#e0f7fa', '#ffebee', '#ffe0b2', '#e1bee7', '#c8e6c9']; // Array of colors for margins

                    foreach ($individualComments as $index => $individualComment) {
                        if (!empty(trim($individualComment))) {
                            $color = $colors[$index % count($colors)]; // Cycle through colors
                            echo "<div style='margin: 10px; padding: 10px; border-radius: 5px; background: $color;'>" . htmlspecialchars(trim($individualComment)) . "</div>";
                        }
                    }
                } else {
                    echo "<p>No comments yet.</p>";
                }
                ?>
            </div>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_comment'])) {
                $newComment = $_POST['new_comment'];

                // Append the new comment to the existing comments
                $updateCommentSql = "UPDATE user SET comment = CONCAT(IFNULL(comment, ''), ?, '<?>') WHERE mail=?";
                $updateCommentStmt = $conn->prepare($updateCommentSql);
                $updateCommentStmt->bind_param("ss", $newComment, $creatorEmail);

                if ($updateCommentStmt->execute()) {
                    echo "<script>
                        var commentsContainer = document.getElementById('commentsContainer');
                        commentsContainer.innerHTML += '<div style=\"margin: 10px; padding: 10px; border-radius: 5px; background: #e0f7fa;\">' + " . json_encode(trim($newComment)) . " + '</div>';
                        document.getElementById(\"commentForm\").reset();
                    </script>";
                } else {
                    echo "Error updating the comments: " . $conn->error;
                }
            }
            ?>
        </div>
    </main>
</body>
</html>
