<?php
session_start();
include("db_connect.php");

$message = "";
$message_type = "";

if (isset($_POST['next'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Semak sama ada email wujud dalam database users
    $check = mysqli_query($conn, 
        "SELECT * FROM users WHERE email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {

        // Generate 6 digit verification code
        $code = rand(100000, 999999);

        // Padam code lama jika ada
        mysqli_query($conn,
            "DELETE FROM password_resets 
             WHERE email='$email'"
        );

        // Simpan code baru
        mysqli_query($conn,
            "INSERT INTO password_resets
            (email, verification_code)
            VALUES('$email','$code')"
        );

        // Simpan email dalam session
        $_SESSION['reset_email'] = $email;

        /*
        UNTUK PROJECT FYP/ASSIGNMENT:
        Disebabkan kita belum setup email SMTP,
        kita paparkan code dalam alert dahulu.
        Nanti boleh tukar kepada email sebenar.
        */

        echo "<script>
        alert('Your verification code is: $code');
        window.location='verify_code.php';
        </script>";

        exit();

    } else {

        $message = "Email address not found!";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ResolveX - Forgot Password</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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
    align-items:center;
    justify-content:center;
}

/* Container */
.container{
    width:1400px;
    height:800px;
    background:white;
    border-radius:20px;
    overflow:hidden;
    display:flex;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

/* LEFT PANEL */
.left{
    width:35%;
    background:
    linear-gradient(
    180deg,
    #003b8e,
    #001c5f
    );

    color:white;
    padding:60px;
    position:relative;
}

.logo{
    margin-top:150px;
    text-align:center;
}

.logo img{
    width:230px;
}

.logo h1{
    font-size:65px;
    color:#26f0ff;
    text-shadow:
    4px 4px 0 rgba(0,0,0,0.2);
}

.logo p{
    letter-spacing:5px;
    font-size:20px;
}


/* RIGHT PANEL */

.right{
    width:65%;
    display:flex;
    justify-content:center;
    align-items:center;
}

.card{
    width:70%;
}


.lock{
    width:120px;
    height:120px;
    background:#eef4ff;
    border-radius:50%;
    margin:auto;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:55px;
}


.card h2{
    text-align:center;
    font-size:55px;
    color:#031d64;
    margin:25px 0;
}


.subtitle{
    text-align:center;
    color:#555;
    font-size:22px;
    margin-bottom:50px;
}


label{
    font-size:25px;
    font-weight:600;
}


input{
    width:100%;
    height:65px;
    margin-top:12px;
    border:1px solid #ccc;
    border-radius:10px;
    padding:20px;
    font-size:20px;
}


button{
    width:100%;
    height:65px;
    margin-top:35px;
    border:none;
    border-radius:10px;
    background:#0057ff;
    color:white;
    font-size:28px;
    font-weight:600;
    cursor:pointer;
}


button:hover{
    background:#003fd1;
}


.message{
    margin-bottom:20px;
    padding:15px;
    border-radius:8px;
    text-align:center;
}


.error{
    background:#ffdede;
    color:#b30000;
}


.bottom{
    margin-top:40px;
    text-align:center;
    font-size:20px;
}


.bottom a{
    color:#0057ff;
    text-decoration:none;
}

</style>

</head>

<body>


<div class="container">


<!-- LEFT -->
<div class="left">

    <div class="logo">

        <img src="robot.jpeg">

        <h1>ResolveX</h1>

        <p>
        COMPLAINT MANAGEMENT
        </p>

    </div>

</div>



<!-- RIGHT -->

<div class="right">


<div class="card">

<div class="lock">
🔒
</div>


<h2>
Forgot Password
</h2>


<p class="subtitle">

Enter your registered email address<br>
and we will send you a verification code.

</p>


<?php if($message != ""){ ?>

<div class="message <?php echo $message_type; ?>">

<?php echo $message; ?>

</div>

<?php } ?>


<form method="POST">


<label>
Email Address
</label>


<input 
type="email"
name="email"
placeholder="Enter your email address"
required>


<button name="next">

Next

</button>


</form>


<div class="bottom">

Remember your password?

<a href="index.php">
Login here
</a>


</div>


</div>


</div>


</div>


</body>

</html>