
<?php
session_start();
$email = trim($_POST["username"] ?? "");
$password = trim($_POST["password"] ?? "");


if ($email == "" || $password == "") {
    echo "<script>
        alert('Email dan password harus diisi!');
        window.location.href = '../pages1/login.html';
    </script>";
    exit;
}

$_SESSION["login"] = true;
$_SESSION["email"] = $email;
header("Location: ../pages1/menu.html");
exit;
?>