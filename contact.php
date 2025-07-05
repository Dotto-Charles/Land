<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Online Land Registration System</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f7f9;
    }

    .contact-container {
      max-width: 900px;
      margin: 50px auto;
      background: white;
      padding: 40px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      border-radius: 12px;
    }

    h2 {
      text-align: center;
      color: #1a73e8;
      margin-bottom: 30px;
    }

    .contact-details, .contact-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .contact-details div {
      font-size: 16px;
      line-height: 1.6;
    }

    .contact-form input,
    .contact-form textarea {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      width: 100%;
    }

    .contact-form button {
      background: #1a73e8;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .contact-form button:hover {
      background: #155ab6;
    }

    .row {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
    }

    .col {
      flex: 1;
      min-width: 300px;
    }

    .back-button {
      display: inline-block;
      background: #6c757d;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.3s ease;
      margin-bottom: 20px;
    }

    .back-button:hover {
      background: #5a6268;
    }
  </style>
</head>
<body>
  <div class="contact-container">
    <!-- Back Button -->
    <a href="javascript:history.back()" class="back-button">← Back</a>

    <h2>Contact Us</h2>
    <div class="row">
      <div class="col contact-details">
        <div><strong>Office Address:</strong><br>
          Ministry of Lands HQ,<br>
          Dodoma, Tanzania.
        </div>
        <div><strong>Phone:</strong><br> +255 621 015</div>
        <div><strong>Email:</strong><br> tittocharles@gmail.com</div>
        <div><strong>Working Hours:</strong><br> Monday - Friday, 8:00 AM to 4:00 PM</div>
      </div>
      <div class="col contact-form">
        <form action="send_message.php" method="POST">
          <input type="text" name="name" placeholder="Your Full Name" required>
          <input type="email" name="email" placeholder="Your Email Address" required>
          <input type="text" name="subject" placeholder="Subject" required>
          <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
          <button type="submit">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
