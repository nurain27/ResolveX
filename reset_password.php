<?php
session_start();
include("db_connect.php");

$message = "";
$message_type = "";

// Pastikan user datang daripada verify code
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}


// Bila form submit
if (isset($_POST['reset'])) {

    $email = $_SESSION['reset_email'];

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    // Semak password sama atau tidak
    if ($password !== $confirm_password) {

        $message = "Password and Confirm Password do not match!";
        $message_type = "error";

    } 
    elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters!";
        $message_type = "error";

    } 
    else {

        // Encrypt password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        // Update password dalam table users
        $update = mysqli_query($conn,
            "UPDATE users 
             SET password='$hashedPassword'
             WHERE email='$email'"
        );


        if ($update) {

            // Padam verification code lama
            mysqli_query($conn,
                "DELETE FROM password_resets
                 WHERE email='$email'"
            );


            // Hapus session reset
            unset($_SESSION['reset_email']);


            echo "
            <script>
                alert('Password has been reset successfully!');
                window.location='index.php';
            </script>
            ";

            exit();


        } else {

            $message = "Failed to reset password!";
            $message_type = "error";

        }

    }
}

?>


<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>
ResolveX - Reset Password
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
    box-shadow:0 15px 40px rgba(0,0,0,.2);
}


/* LEFT PANEL */

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
    margin-bottom:45px;

}


label{

    font-size:22px;
    font-weight:600;
    display:block;
    margin-top:15px;

}


input{

    width:100%;
    height:65px;
    border:1px solid #ccc;
    border-radius:10px;
    padding:20px;
    font-size:20px;
    margin-top:10px;

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

    margin-top:35px;
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


<h1>
ResolveX
</h1>


<p>
COMPLAINT MANAGEMENT
</p>


</div>


</div>



<!-- RIGHT -->


<div class="right">


<div class="card">


<div class="icon">

🔑

</div>


<h2>
Reset Password

</h2>


<p class="subtitle">

Create a new secure password<br>
for your ResolveX account.

</p>



<?php if($message!=""){ ?>


<div class="message <?php echo $message_type; ?>">

<?php echo $message; ?>

</div>


<?php } ?>




<form method="POST">


<label>
New Password
</label>


<input 
type="password"
name="password"
placeholder="Enter new password"
required>



<label>
Confirm Password
</label>


<input 
type="password"
name="confirm_password"
placeholder="Confirm your password"
required>



<button name="reset">

Reset Password

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