<?php
session_start();
require_once "Usuaris.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $password = $_POST["contrasena"];

    $u = new Usuaris();
    $usuario = $u->login($email, $password);

    if (!$usuario) {
        $_SESSION["error"] = "Credenciales incorrectas.";
        $_SESSION["volver_a"] = '../presentacion/index.html'; // página de login
        header("Location: ../presentacion/error.php");
        exit;
    }
        unset($usuario["contrasena"]);


    // LOGIN CORRECTO
    $_SESSION["usuario"] = $usuario;
    header("Location: ../presentacion/inici.php");
    exit;
}


?>