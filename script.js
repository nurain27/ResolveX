function togglePassword() {

    let password = document.getElementById("password");

    if(password.type === "password"){
        password.type = "text";
    }
    else{
        password.type = "password";
    }
}

function login(){

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if(email === "" || password === ""){
        alert("Please fill in all fields!");
        return;
    }

    alert("Login Successful!");
}