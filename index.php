<?php
session_start(); // Start the session

// Include the database connection file
require_once 'db_connect.php'; // Adjust path if db_connect.php is in a different directory

// If the user is already logged in, redirect them
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: emailpos.php'); // Redirect to emailpos.html as that's your editor
    exit;
}

$error_message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_username = $_POST['username'] ?? '';
    $submitted_password = $_POST['password'] ?? '';

    // Use prepared statements to prevent SQL injection
    // IMPORTANT: Ensure your 'users' table has 'name' and 'user_level' columns
    $stmt = $mysqli->prepare("SELECT id, username, password_hash, name, user_level FROM users WHERE username = ?");
    $stmt->bind_param("s", $submitted_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_hashed_password = $user['password_hash'];

        // Verify the submitted password against the stored hash
        if (password_verify($submitted_password, $stored_hashed_password)) {
            // Password is correct! Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; // Store user ID
            $_SESSION['user_level'] = $user['user_level'] ?? 'user'; // Store user level, default to 'user' if not set
            $_SESSION['name'] = $user['name'] ?? $user['username']; // Store user's name, fallback to username

            header('Location: emailpos.php'); // Redirect to the main application page
            exit;
        } else {
            // Password incorrect
            $error_message = 'Invalid username or password.';
        }
    } else {
        // User not found
        $error_message = 'Invalid username or password.';
    }
    $stmt->close();
}

// Close the database connection
if (isset($mysqli) && $mysqli instanceof mysqli) {
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to EmailPOS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img alt="EmailPOS" src="assets/images/emailpos.svg" style="width:300px;height:auto;">
        <h2>Login to EmailPOS</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="index.php">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px;"><a href="api/forgot_password.php">Forgot Password?</a></p>
    </div>
</body>
</html>
