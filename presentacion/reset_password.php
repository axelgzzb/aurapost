<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";


$error = "";
$success = false;
$mostrarForm = false;


// 1. VALIDAR TOKEN DE URL VS SESIÓN
$tokenUrl = $_GET['token'] ?? '';
$sessionToken = $_SESSION['reset_token'] ?? null;
$sessionEmail = $_SESSION['reset_email'] ?? null;
$sessionExpires = $_SESSION['reset_expires'] ?? 0;


// Verificamos si hay token y si coinciden
if ($tokenUrl && $sessionToken && $tokenUrl === $sessionToken) {
   // Verificamos si no ha caducado
   if (time() < $sessionExpires) {
       $mostrarForm = true;
   } else {
       $error = "El enlace ha caducado. Por favor, solicita uno nuevo.";
   }
} else {
   $error = "Enlace inválido. Asegúrate de copiar todo el link o solicita uno nuevo.";
}


// 2. PROCESAR CAMBIO DE PASSWORD
if ($_SERVER["REQUEST_METHOD"] === "POST" && $mostrarForm) {
   $pass1 = $_POST["pass1"] ?? "";
   $pass2 = $_POST["pass2"] ?? "";


   if ($pass1 === $pass2) {
       if (strlen($pass1) >= 4) {
           $u = new Usuaris();
          
           // Actualizamos la contraseña usando el email guardado en sesión
           if ($u->updatePasswordByEmail($sessionEmail, $pass1)) {
               $success = true;
               $mostrarForm = false;
              
               // Limpiamos la sesión de recuperación
               unset($_SESSION['reset_token']);
               unset($_SESSION['reset_email']);
               unset($_SESSION['reset_expires']);
           } else {
               $error = "Hubo un error al actualizar la base de datos.";
           }
       } else {
           $error = "La contraseña debe tener al menos 4 caracteres.";
       }
   } else {
       $error = "Las contraseñas no coinciden.";
   }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva Contraseña - AuraPost</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/index.css" rel="stylesheet">
</head>
<body>


<div class="login-card">
 <h4 class="text-center mb-4" style="color:#8c52ff;">Nueva contraseña</h4>


 <?php if ($error): ?>
     <div class="alert alert-danger text-center"><?= $error ?></div>
     <div class="text-center">
         <a href="recuperar_password.php" class="btn btn-sm btn-outline-secondary">Intentar de nuevo</a>
     </div>
 <?php endif; ?>


 <?php if ($success): ?>
     <div class="alert alert-success text-center">
         <i class="fa-solid fa-check-circle fa-2x mb-2"></i><br>
         ¡Contraseña actualizada!
     </div>
     <p class="text-center text-muted">Redirigiendo...</p>
     <meta http-equiv="refresh" content="3;url=index.html">
     <div class="text-center">
       <a href="index.html" class="btn btn-aura w-100">Ir al Login ahora</a>
     </div>
 <?php endif; ?>


 <?php if ($mostrarForm): ?>
     <form action="" method="POST">
       <div class="mb-3">
           <input type="password" name="pass1" class="form-control" placeholder="Escribe nueva contraseña" required>
       </div>
       <div class="mb-3">
           <input type="password" name="pass2" class="form-control" placeholder="Repite la contraseña" required>
       </div>
       <button type="submit" class="btn btn-aura w-100">Guardar cambios</button>
     </form>
 <?php endif; ?>


</div>


</body>
</html>