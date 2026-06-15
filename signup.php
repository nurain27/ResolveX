<?php
include("db_connect.php");

$message = "";

if(isset($_POST['signup']))
{
    // Database connection check (good practice)
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = $_POST['password']; 

    // Encrypt password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check existing email
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0)
    {
        $message = "Email already exists!";
    }
    else
    {
        $sql = "INSERT INTO users(name,email,password) VALUES('$name','$email','$hashedPassword')";

        if(mysqli_query($conn,$sql))
        {
            $message = "Registration successful!";
        }
        else
        {
            $message = "Registration failed! " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResolveX - Sign Up</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            background:#005da8;
            min-height:100vh;
            padding:35px;
        }

        .container{
            max-width:1300px;
            margin:auto;
        }

        .header{
            margin-bottom:30px;
        }

        .logo-section{
            display:flex;
            align-items:center;
            gap:20px;
        }

        .logo-section img{
            width:65px;
        }

        .logo-section p{
            color:white;
            font-size:18px;
        }

        .signup-card{
            background:#f5f5f5;
            border-radius:10px;
            padding:40px;
            min-height:500px;
        }

        .signup-card h2{
            text-align:center;
            font-size:48px;
            color:#222;
        }

        .subtitle{
            text-align:center;
            color:#777;
            margin-bottom:20px;
        }

        .php-message {
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .msg-success { background: #d4edda; color: #155724; }
        .msg-error { background: #f8d7da; color: #721c24; }

        .form-row{
            display:flex;
            gap:60px;
            margin-bottom:30px;
        }

        .input-group{
            flex:1;
        }

        .input-group label{
            display:block;
            margin-bottom:10px;
            font-weight:500;
        }

        .input-group input{
            width:100%;
            height:55px;
            border:none;
            border-radius:8px;
            background:#e8e8e8;
            padding:15px;
            font-size:15px;
        }

        .password-box{
            position:relative;
        }

        .password-box span{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
        }

        small{
            color:#d9534f;
            display:block;
            margin-top:8px;
        }

        .signup-btn{
            width:100%;
            height:55px;
            margin-top:30px;
            border:none;
            border-radius:8px;
            background:#4169f5;
            color:white;
            font-size:28px;
            cursor:pointer;
        }

        .signup-btn:hover{
            background:#3155db;
        }

        .footer-buttons{
            margin-top:20px;
            display:flex;
            justify-content:flex-end;
            gap:15px;
        }

        .footer-buttons button{
            width:90px;
            height:40px;
            border-radius:20px;
            border:1px solid white;
            background:transparent;
            color:white;
            cursor:pointer;
        }

        .footer-buttons button:hover{
            background:white;
            color:#005da8;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div class="logo-section">
            <img src="robot.jpeg" alt="Logo" class="logo-img">
            <p>Track, Assign, Resolve — All in One System</p>
        </div>
    </div>

    <div class="signup-card">

        <h2>Sign Up</h2>
        <p class="subtitle">Enter your credentials to access the system</p>

        <?php if(!empty($message)): ?>
            <div class="php-message <?php echo (strpos($message, 'successful') !== false) ? 'msg-success' : 'msg-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return registerUser()">

            <div class="form-row">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email" 
                        placeholder="Enter your email" required>
                </div>

                <div class="input-group">
                    <label for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-box">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password" required>
                        <span onclick="togglePassword()">👁</span>
                    </div>
                    <small>* More than 8 characters</small>
                </div>
            </div>

            <button type="submit" name="signup" class="signup-btn">
                Sign up
            </button>

        </form>

    </div>

    <div class="footer-buttons">
        <button onclick="goHome()">Home</button>
        <button onclick="goBack()">Back</button>
    </div>

</div>

<script>
function togglePassword(){
    let password = document.getElementById("password");
    if(password.type === "password"){
        password.type = "text";
    }else{
        password.type = "password";
    }
}

function registerUser(){
    let email = document.getElementById("email").value.trim();
    let name = document.getElementById("name").value.trim();
    let password = document.getElementById("password").value;

    if(email === "" || name === "" || password === ""){
        alert("Please fill all fields.");
        return false; // Blocks form submission
    }

    if(password.length <= 8){
        alert("Password must be more than 8 characters.");
        return false; // Blocks form submission
    }

    // Returning true allows the browser to hand execution over to the backend PHP action
    return true; 
}

function goHome(){
    window.location.href = "index.html";
}

function goBack(){
    history.back();
}
</script>

</body>
</html>