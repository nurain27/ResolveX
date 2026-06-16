<?php
session_start();
include("db_connect.php");

$message = "";
$message_type = "";

// Pastikan user datang daripada forgot password
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}


if (isset($_POST['verify'])) {

    $email = $_SESSION['reset_email'];
    $code = mysqli_real_escape_string($conn, $_POST['code']);

    // Semak verification code
    $check = mysqli_query($conn,
        "SELECT * FROM password_resets 
        WHERE email='$email'
        AND verification_code='$code'"
    );


    if (mysqli_num_rows($check) > 0) {

        // Code betul, pergi reset password
        header("Location: reset_password.php");
        exit();

    } else {

        $message = "Invalid verification code!";
        $message_type = "error";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>
ResolveX - Verify Code
</title>


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
    justify-content:center;
    align-items:center;
}


.container{

    width:1400px;
    height:800px;
    background:white;
    border-radius:20px;
    overflow:hidden;
    display:flex;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}


/* LEFT */

.left{

    width:35%;
    background:linear-gradient(180deg,#003b8e,#001c5f);
    color:white;
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
    text-shadow:4px 4px 0 rgba(0,0,0,.2);
}


.logo p{

    letter-spacing:5px;
    font-size:20px;
}


/* RIGHT */


.right{

    width:65%;
    display:flex;
    justify-content:center;
    align-items:center;
}


.card{

    width:70%;
}


.icon{

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
    font-size:22px;
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
    text-align:center;
    border-radius:8px;
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


<!-- LEFT SIDE -->


<div class="left">


<div class="logo">


<img src="robot.jpeg">


<h1>
ResolveX
</h1>


<p>
COMPLAINT MANAGEMENT
</p>


</div>


</div>



<!-- RIGHT SIDE -->


<div class="right">


<div class="card">


<div class="icon">
🔐
</div>


<h2>
Verification Code
</h2>


<p class="subtitle">

Enter the 6-digit verification code<br>
sent to your email address.

</p>



<?php if($message!=""){ ?>


<div class="message <?php echo $message_type; ?>">

<?php echo $message; ?>

</div>


<?php } ?>



<form method="POST">


<label>
Verification Code
</label>


<input 
type="text"
name="code"
placeholder="Enter 6-digit code"
maxlength="6"
required>



<button name="verify">

Verify Code

</button>


</form>



<div class="bottom">


Didn't receive the code?

<a href="forgot_password.php">
Try again
</a>


</div>



</div>


</div>


</div>



</body>

</html>