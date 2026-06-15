<?php
session_start(); // Starts a secure session to remember who logged in
include("db_connect.php");

$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Raw input needed to check against hashed passwords

    // Search for user with this email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Decrypt and compare the password
        if (password_verify($password, $user['password'])) {
            // Save user details to the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect securely to your dashboard/history page
            header("Location: complaint_history.php");
            exit();
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResolveX Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <style>
        /* Embedded styling for the error alert box */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container span {
            position: absolute;
            right: 15px;
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="left-panel">
        <img src="robot.jpeg" alt="Robot Logo" class="robot">

        <h1>ResolveX</h1>
        <h3>COMPLAINT MANAGEMENT</h3>

        <p class="tagline">
            Track, Assign, Resolve — All in One System
        </p>

        <ul>
            <li>Real-Time Complaint Tracking</li>
            <li>Smart Technician Assignment</li>
            <li>Automated Status Updates</li>
            <li>Maintenance Request Management</li>
        </ul>

        <div class="feature-box">
            <h4>Access Control</h4>
            <p>Secure login system with role-based access control.</p>
        </div>
    </div>

    <div class="right-panel">

        <div class="top-buttons">
            <button class="signup-btn" onclick="window.location.href='signup.php'">
                Sign Up
            </button>

            <button class="admin-btn" onclick="window.location.href='admin_login.html'">
                Admin
            </button>
        </div>

        <div class="login-box">
            <h2>Sign In</h2>
            <p>Enter your credentials to access the system</p>

            <?php if (!empty($message)): ?>
                <div class="error-message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span onclick="togglePassword()">👁</span>
                </div>

                <a href="#">Forgot your password?</a>

                <button type="submit" name="login" class="login-btn">
                    Sign In
                </button>

            </form>
        </div>

    </div>

</div>

<script>
// Inline backup toggle function just in case script.js doesn't load it
function togglePassword(){
    let passwordField = document.getElementById("password");
    if(passwordField.type === "password"){
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>

</body>
</html>