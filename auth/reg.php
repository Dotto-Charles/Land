<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home | Land Registration System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styleregister.css"> 
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
    }

    .hero {
      background: url('background.jpg') no-repeat center center/cover;
      height: 100vh;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: white;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.6);
      z-index: 1;
    }

    /* Navbar */
    .navbar {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      background-color: rgba(0,0,0,0.5);
    }

    .navbar .logo {
      display: flex;
      align-items: center;
    }

    .navbar .logo img {
      height: 40px;
      margin-right: 10px;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 20px;
    }

    .navbar ul li a {
      text-decoration: none;
      color: white;
      font-weight: 500;
      transition: color 0.3s;
    }

    .navbar ul li a:hover {
      color: #00b894;
    }

    .content {
      position: relative;
      z-index: 2;
      text-align: center;
      padding-top: 30px;
      animation: fadeIn 2s ease-in-out;
    }

    .content img.logo-main {
      height: 80px;
      margin-bottom: 20px;
    }

    h1 {
      font-size: 3rem;
      margin-bottom: 10px;
      font-weight: 600;
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      padding: 12px 25px;
      font-size: 1rem;
      border: 2px solid white;
      border-radius: 30px;
      background-color: transparent;
      color: white;
      text-decoration: none;
      position: relative;
      overflow: hidden;
      z-index: 1;
      transition: all 0.4s ease;
    }

    .btn::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 0;
      height: 100%;
      background-color: #00b894;
      z-index: -1;
      transition: 0.4s ease;
    }

    .btn:hover::before {
      width: 100%;
    }

    .btn:hover {
      border-color: #00b894;
      color: white;
    }

    footer {
      z-index: 2;
      position: relative;
      background-color: rgba(0, 0, 0, 0.7);
      text-align: center;
      padding: 15px 10px;
      font-size: 0.9rem;
      color: #ccc;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    @media (max-width: 768px) {
      h1 { font-size: 2rem; }
      p { font-size: 1rem; }
      .navbar {
        flex-direction: column;
        padding: 15px;
      }

      .navbar ul {
        flex-direction: column;
        margin-top: 10px;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

  <div class="hero">
    <div class="overlay"></div>

    <!-- Navbar -->
    <div class="navbar">
      <div class="logo">
        <img src="icons/images.jpeg" alt="Logo">
        <span style="color: white; font-size: 1.2rem; font-weight: bold;">LandSys</span>
      </div>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="auth/login.php">Login</a></li>
        <li><a href="auth/register.php">Register</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">Help</a></li>
        <li><a href="#">EN | SW</a></li>
      </ul>
    </div>

    <!-- Main Content -->
<div class="content">
      <h2>User Registration</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        
        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
                <label>Second Name:</label>
                <input type="text" name="second_name">
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="form-group">
                <label>Sex:</label>
                <select name="sex" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label>National ID:</label>
                <input type="text" name="national_id" required>
            </div>

            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="landowner">Landowner</option>
                    <option value="buyer">Buyer</option>
                    <option value="surveyor">Surveyor</option>
                    <option value="lawyer">Lawyer</option>
                    <option value="government_official">Government Official</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit">Register</button>
            <p>Do you have an accout?</p> <a href="login.php">Log in</a>
        </form>
    </div>
       


    <!-- Footer -->
    <footer>
      &copy; <?= date('Y') ?> Online Land Registration System. All rights reserved.
    </footer>

  </div>

</body>
</html>
