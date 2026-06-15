<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ResolveX Admin Login</title>
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
    display:flex;
    justify-content:center;
    align-items:center;
}

.container{
    width:1200px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.left-section{
    width:45%;
    color:white;
}

.logo{
    margin-bottom:20px;
}

.logo img{
    width:180px;
    height:auto;
    display:block;
    margin-bottom:20px;
}

.left-section h1{
    font-size:70px;
    color:#28ecff;
    text-shadow:0 0 10px rgba(0,255,255,.5);
}

.left-section h3{
    letter-spacing:4px;
    margin-bottom:20px;
}

.tagline{
    margin-bottom:25px;
}

.left-section ul{
    list-style:none;
}

.left-section ul li{
    margin-bottom:15px;
    font-size:28px;
}

.access-box{
    margin-top:30px;
    border:1px solid rgba(255,255,255,.15);
    padding:20px;
    border-radius:10px;
    width:80%;
    background:rgba(255,255,255,.05);
}

.access-box h4{
    margin-bottom:10px;
}

.right-section{
    width:45%;
}

.login-card{
    background:#f8f8f8;
    border-radius:10px;
    padding:40px;
}

.login-card h2{
    text-align:center;
    font-size:40px;
}

.login-card p{
    text-align:center;
    color:#777;
    margin-bottom:30px;
}

.login-card label{
    display:block;
    margin-top:15px;
    margin-bottom:8px;
}

.login-card input{
    width:100%;
    padding:15px;
    border:none;
    background:#ececec;
    border-radius:8px;
}

.password-wrapper{
    position:relative;
}

.password-wrapper span{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
}

.login-card a{
    display:block;
    margin-top:10px;
    font-size:13px;
    color:#666;
    text-decoration:none;
}

.login-card button{
    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:8px;
    background:#4264f5;
    color:white;
    font-size:20px;
    cursor:pointer;
}

.login-card button:hover{
    background:#2c4fe8;
}

.bottom-buttons{
    display:flex;
    justify-content:flex-end;
    gap:15px;
    margin-top:20px;
}

.bottom-buttons button{
    width:90px;
    height:40px;
    border-radius:20px;
    border:1px solid white;
    background:transparent;
    color:white;
    cursor:pointer;
}

.bottom-buttons button:hover{
    background:white;
    color:#005da8;
}
</style>
</head>
<body>

<div class="container">

    <!-- LEFT SIDE -->
    <div class="left-section">
      <div class="logo">
    <img src="robot.jpeg" alt="ResolveX Logo">

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

        <div class="access-box">
            <h4>Access Control</h4>
            <p>
                Secure login system with role-based access control.
            </p>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right-section">

        <div class="login-card">

            <h2>Sign In</h2>
            <p>Enter your credentials to access the system</p>

            <label>Admin ID</label>
            <input type="text"
                   id="adminid"
                   placeholder="Enter your ID">

            <label>Password</label>

            <div class="password-wrapper">

                <input type="password"
                       id="password"
                       placeholder="Enter your password">

                <span onclick="togglePassword()">👁</span>

            </div>

            <a href="#">Forgot your password?</a>

            <button onclick="loginAdmin()">
                Sign In
            </button>

        </div>

        <div class="bottom-buttons">

            <button onclick="goHome()">
                Home
            </button>

            <button onclick="goBack()">
                Back
            </button>

        </div>

    </div>

</div>
<style>
    function togglePassword(){

    let password =
    document.getElementById("password");

    if(password.type === "password"){
        password.type = "text";
    }
    else{
        password.type = "password";
    }

}

function loginAdmin(){

     let adminid = document.getElementById("adminid").value;
    let password = document.getElementById("password").value;

    if(adminid === "admin" && password === "admin123"){

        window.location.href = "dashboard.html";

    }else{

        alert("Invalid Admin ID or Password");

    }
}

function goHome(){

    window.location.href="index.php";

}

function goBack(){

    history.back();

}
</style>
</body>
</html>