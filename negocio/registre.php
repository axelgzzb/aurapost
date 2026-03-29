<?php
session_start();

require_once __DIR__ . "/Usuaris.php";
require_once __DIR__ . "/../helpers/valida.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["error"] = "Petición no válida";
    $_SESSION["volver_a"] = '../presentacion/registre.html';
    header("Location: ../presentacion/error.php");
    exit;
}

$nombre          = sanea($_POST["nombre"] ?? "");
$apellidos       = sanea($_POST["apellidos"] ?? "");
$nombre_usuario  = sanea($_POST["nombre_usuario"] ?? "");
$email           = sanea($_POST["email"] ?? "");
$contrasena      = $_POST["contrasena"] ?? "";
$pronombres      = sanea($_POST["pronombres"] ?? "");
$ruta_foto       = $_FILES["foto_perfil"] ?? null;

/* --- VALIDACIONES --- */

if (empty($nombre) || empty($apellidos) || empty($nombre_usuario) || empty($email) || empty($contrasena)) {
    $_SESSION["error"] = "Faltan campos obligatorios.";
    header("Location: ../presentacion/error.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Email no válido.";
    header("Location: ../presentacion/error.php");
    $_SESSION["volver_a"] = '../presentacion/registre.html';

    exit;
}


if (!validaSoloLetras($nombre) || !validaSoloLetras($apellidos)) {
    $_SESSION["error"] = "El nombre y los apellidos solo pueden contener letras.";
    $_SESSION["volver_a"] = '../presentacion/registre.html';
    header("Location: ../presentacion/error.php");
    exit;
}

if (!campValid($nombre_usuario)) {
    $_SESSION["error"] = "El alias no es válido.";
    $_SESSION["volver_a"] = '../presentacion/registre.html';
    header("Location: ../presentacion/error.php");
    exit;
}

if (!validaEmail($email)) {
    $_SESSION["error"] = "El correo electrónico no es válido o el dominio no existe.";
    $_SESSION["volver_a"] = '../presentacion/registre.html';
    header("Location: ../presentacion/error.php");
    exit;
}

/* --- COMPROBAR SI YA EXISTE --- */
$u = new Usuaris();

if ($u->getUserByEmail($email)) {
    $_SESSION["error"] = "El correo electrónico ya está registrado.";
    header("Location: ../presentacion/error.php");
    exit;
}

if ($u->getUserByUsername($nombre_usuario)) {
    $_SESSION["error"] = "El nombre de usuario ya está en uso.";
    header("Location: ../presentacion/error.php");
    exit;
}



/* --- SUBIR FOTO SI EXISTE --- */
$foto = null;

if ($ruta_foto && $ruta_foto["error"] === UPLOAD_ERR_OK) {
    $nombreFoto = uniqid() . "_" . basename($ruta_foto["name"]);
    $destino = __DIR__ . "/../uploads/" . $nombreFoto;

    if (move_uploaded_file($ruta_foto["tmp_name"], $destino)) {
        $foto = $nombreFoto;
    }
}

/* --- INSERTAR USUARIO --- */

$id = $u->addUser([
    "nombre_usuario" => $nombre_usuario,
    "nombre"         => $nombre,
    "apellidos"      => $apellidos,
    "pronombres"     => $pronombres,
    "email"          => $email,
    "contrasena"     => $contrasena,
    "foto_perfil"    => $foto,
    "biografia"      => null
]);

if (!$id) {
    $_SESSION["error"] = "No se pudo completar el registro.";
    header("Location: ../presentacion/error.php");
    exit;
}

/* --- 6. ENVÍO DE CORREO DE BIENVENIDA --- */
$destinatario = $email;
$asunto = "¡Bienvenido a AuraPost, $nombre_usuario!";
$mensaje = '
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f0f1f5;font-family:Arial, sans-serif;">
  <table width="100%" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="500" style="background-color:#ffffff;border-radius:15px;padding:40px;text-align:center;">
          <tr>
            <td>
              <h1 style="color:#8c52ff;">¡Hola, '.$nombre_usuario.'!</h1>
              <p>Te damos la bienvenida a <strong>AuraPost</strong>.</p>
              <a href="https://and.alwaysdata.net/AuraPost/presentacion/index.html"
                 style="display:inline-block;background-color:#8c52ff;color:#ffffff;padding:12px 30px;border-radius:30px;text-decoration:none;">
                Entrar a mi cuenta
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';


$headers = "From: AuraPost <no-reply@and.alwaysdata.net>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
mail($destinatario, $asunto, $mensaje, $headers);


/* --- LOGIN AUTOMÁTICO --- */
$_SESSION["usuario"] = $u->getUserById($id);

header("Location: ../presentacion/inici.php");
exit;

?>