<!DOCTYPE html>
<html>
<head>
    <title>Gallery Hub</title>
</head>
<body>
    <h1>Welcome to Gallery Hub</h1>

    <?php
    // Display account creation message if set
    if (isset($_GET['status']) && $_GET['status'] == 'created') {
        echo "<p>Account Created</p>";
    }
    ?>

    <button onclick="location.href='login.php'">Login</button>
    <button onclick="location.href='signup.php'">Sign Up</button>
    <button onclick="location.href='gallery.php'">Visit as Guest</button>
</body>
</html>
