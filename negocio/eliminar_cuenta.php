<?php
session_start();

require_once "Usuaris.php";

if (!isset($_SESSION["usuario"]["id_usuario"])) {
    header("Location: ../presentacion/index.html");
    exit;
}

$id_usuario = $_SESSION["usuario"]["id_usuario"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["confirmar"]) && $_POST["confirmar"] === "SI") {
        $u = new Usuaris();
        if ($u->deleteUser($id_usuario)) {
            session_unset();
            session_destroy();
            header("Location: ../presentacion/index.html");
            exit;
        } else {
            $_SESSION["error"] = "No se pudo eliminar tu cuenta.";
            header("Location: ../presentacion/error.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eliminar Cuenta</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f9f5ff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.card-delete {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 450px;
    text-align: center;
    position: relative;
}
.card-delete img.logo {
    width: 80px;
    margin-bottom: 1rem;
}
.card-delete h2 {
    color: #d31212;
    margin-bottom: 1rem;
}
.card-delete p {
    margin-bottom: 2rem;
    color: #555;
}
.btn-danger-custom {
    background: linear-gradient(90deg, #d31212, #ff4b4b);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: 0.3s;
}
.btn-danger-custom:hover {
    background: linear-gradient(90deg, #ff4b4b, #d31212);
}
.btn-cancel {
    display: inline-block;
    margin-bottom: 2rem;
    color: #6c5ce7;
    text-decoration: none;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border: 2px solid #6c5ce7;
    border-radius: 8px;
    transition: 0.3s;
}
.btn-cancel:hover {
    background: #6c5ce7;
    color: white;
}
</style>
</head>
<body>

<div class="card-delete">
    <img src="../src/scared-fish.png" alt="AuraPost" class="logo">
    <h2>¿Eliminar cuenta?</h2>
    <h2>⚠️</h2>
    <p>Esta acción es permanente y no se puede deshacer.</p>

    <!-- Botón Cancelar arriba -->
    <a href="../presentacion/configuracion.php" class="btn-cancel">Volver atrás</a>

    <!-- Botón Eliminar abajo -->
    <form action="" method="POST">
        <button type="submit" name="confirmar" value="SI" class="btn-danger-custom">Eliminar definitivamente</button>
    </form>
</div>

</body>
</html>
