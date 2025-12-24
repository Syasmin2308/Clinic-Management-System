<?php
// MUST be first thing in file - no whitespace before!
session_start();

// Check if already logged in
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

// Handle login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
    
    // Get inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // 1. Find user in database
    $stmt = $conn->prepare("SELECT userID, password, fullName, role FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // 2. Verify user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 3. Verify password
        if (password_verify($password, $user['password'])) {
            
            // 4. Set session data
            $_SESSION['user'] = [
                'id' => $user['userID'],
                'name' => $user['fullName'],
                'role' => $user['role']
            ];
            
            // 5. Force clean redirect
            ob_end_clean(); // Clear any buffered output
            header("Location: home.php");
            exit();
        }
    }
    
    // If we get here, login failed
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<!-- Rest of your HTML form remains the same --><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UK Admin - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: rgba(212, 241, 228, 1);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-container {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 2rem;
      color: #2e7d6b;
    }
    
    .login-header i {
      font-size: 3rem;
      margin-bottom: 1rem;
    }
    
    .btn-login {
      background-color: #2e7d6b;
      border-color: #2e7d6b;
      width: 100%;
    }
    
    .btn-login:hover {
      background-color: #1c5b4a;
      border-color: #1c5b4a;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <i class="bi bi-hospital"></i>
      <h2>UK Admin Login</h2>
    </div>
    
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-login">Login</button>
    </form>
  </div>
</body>
</html>