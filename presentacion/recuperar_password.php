<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";


$mensaje = "";
$tipo_mensaje = ""; // success, danger, warning


if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $email = trim($_POST["email"] ?? "");


   // 1. Validar formato
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $mensaje = "El formato del correo no es válido.";
       $tipo_mensaje = "danger";
   } else {
       // 2. Verificar existencia en BDD
       $u = new Usuaris();
       $usuario = $u->getUserByEmail($email);


       if ($usuario) {
           // 3. Generar token y guardar en SESSION
           $token = bin2hex(random_bytes(16));
           $_SESSION['reset_token'] = $token;
           $_SESSION['reset_email'] = $email;
           $_SESSION['reset_expires'] = time() + 1800; // 30 min


           // 4. Construir enlace absoluto (detecta tu dominio automáticamente)
           // Ajusta la ruta '/presentacion' si tu estructura es diferente
           $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
           $host = $_SERVER['HTTP_HOST'];
           // Detectamos la carpeta donde está este script para armar la ruta
           $path = dirname($_SERVER['PHP_SELF']);
          
           $resetLink = "$protocol://$host$path/reset_password.php?token=$token";


           // 5. Preparar correo
           $asunto = "Recupera tu contraseña - AuraPost";
           $cuerpo = '
           <!DOCTYPE html>
           <html lang="es">
           <body style="background:#f0f1f5;font-family:sans-serif;padding:20px;">
               <div style="background:#fff;padding:30px;border-radius:10px;text-align:center;max-width:500px;margin:0 auto;">
                 <h2 style="color:#8c52ff;">AuraPost</h2>
                 <p>Has solicitado restablecer tu contraseña.</p>
                 <a href="'.$resetLink.'" style="background:#8c52ff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:20px;display:inline-block;">Cambiar contraseña</a>
                 <p style="font-size:12px;color:#777;margin-top:20px;">Si no fuiste tú, ignora este mensaje.</p>
               </div>
           </body>
           </html>';


           $headers  = "From: AuraPost <no-reply@aurapost.com>\r\n";
           $headers .= "MIME-Version: 1.0\r\n";
           $headers .= "Content-Type: text/html; charset=UTF-8\r\n";


           // 6. Enviar
           if(@mail($email, $asunto, $cuerpo, $headers)) {
               $mensaje = "Hemos enviado un enlace a tu correo. De no encontrarlo, por favor, revisa tu bandeja de spam.";
               $tipo_mensaje = "success";
           } else {
               // FALLBACK PARA DESARROLLO (Si el servidor de correo falla)
               $mensaje = "Simulación (Mail falló): <a href='$resetLink'><b>CLIC AQUÍ PARA RESETEAR</b></a>";
               $tipo_mensaje = "warning";
           }


       } else {
           // Mensaje genérico por seguridad
           $mensaje = "Si el correo existe, recibirás instrucciones.";
           $tipo_mensaje = "success";
       }
   }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Recuperar - AuraPost</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="../css/index.css" rel="stylesheet">
</head>
<body>
 <div class="login-card">
   <div class="mb-4 text-center">
      <div class="logo-container"><img src="../src/logo.png" alt="Logo"></div>
      <h4 style="color:#8c52ff;">Recuperar acceso</h4>
   </div>


   <?php if($mensaje): ?>
       <div class="alert alert-<?= $tipo_mensaje ?>"><?= $mensaje ?></div>
   <?php endif; ?>


   <form action="recuperar_password.php" method="POST">
     <div class="mb-3">
       <input type="email" name="email" class="form-control" placeholder="Introduce tu correo" required>
     </div>
     <button type="submit" class="btn btn-aura w-100">Enviar enlace</button>
   </form>
  
   <div class="text-center mt-3">
       <a href="index.html" class="text-decoration-none text-secondary">Volver al login</a>
   </div>
 </div>
</body>
</html>