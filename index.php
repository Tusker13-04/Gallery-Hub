<!DOCTYPE html>
<html>
<head>
  <title>Gallery Hub</title>
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
      background-image: url('./download.jpg'); /* Replace with your image path */
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

    .logo:hover {
      transform: scale(1.05);
    }

    /* Buttons */
    .btn {
      padding: 2rem 4rem; /* Double the padding */
      font-size: 1.4em; /* Increase font size */
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
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

    .btn-outline {
      background: transparent;
      border: 2px solid var(--secondary);
      color: var(--secondary);
    }

    .btn-outline:hover {
      background: var(--secondary);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
    }

    /* Main Content */
    .main {
      max-width: 1200px;
      margin: 0 auto;
      padding: 7rem 2rem 4rem;
    }

    .hero {
      text-align: center;
      margin-bottom: 4rem;
    }

    .hero h1 {
      font-size: 3.5rem;
      margin-bottom: 1rem;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      line-height: 1.2;
    }

    .hero p {
      font-size: 1.25rem;
      color: var(--text-light);
      max-width: 600px;
      margin: 0 auto 2rem;
    }

    .cta-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-bottom: 3rem;
    }

    /* Status Messages */
    .status-message {
      padding: 1rem;
      border-radius: 0.5rem;
      margin: 1rem 0;
      animation: fadeIn 0.5s ease-out;
    }

    .status-success {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #059669;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-container {
        padding: 1rem;
      }

      .hero h1 {
        font-size: 2.5rem;
      }

      .cta-buttons {
        flex-direction: column;
        padding: 0 1rem;
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
    <section class="hero">
      <h1>Welcome to Gallery Hub</h1>
      <p>Discover and share amazing artwork from creators around the world</p>
      <div class="cta-buttons">
        <button class="btn btn-primary" onclick="location.href='login.php'">Login</button>
        <button class="btn btn-outline" onclick="location.href='signup.php'">Sign Up</button>
        <button class="btn btn-outline" onclick="location.href='gallery.php'">Visit as Guest</button>
      </div>
    </section>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'created'): ?>
    <div class="status-message status-success">
      Account Created Successfully
    </div>
    <?php endif; ?>
  </main>
</body>
</html>