<?php
session_start();

require_once "Usuaris.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION["usuario"]["id_usuario"])) {
    header("Location: ../presentacion/index.html");
    exit;
}

$id = $_SESSION["usuario"]["id_usuario"];
$u = new Usuaris();
$datos = $u->getUserById($id);

if (!$datos) {
    $_SESSION["error"] = "No se pudieron cargar tus datos.";
    header("Location: ../presentacion/configuracion.php");
    exit;
}

// Procesar el formulario si se envió
$errores = [];
$exito = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $valor = trim($_POST["valor"] ?? "");
    
    $partes = explode(" ", $valor, 2);
    if (count($partes) < 2) {
        $errores[] = "Debes introducir nombre y apellidos.";
        $_SESSION["error"]= "Debes introducir nombre y apellidos.";
    } else {
        $nombre = trim($partes[0]);
        $apellidos = trim($partes[1]);
        
        if (empty($nombre) || empty($apellidos)) {
            $errores[] = "Nombre y apellidos no pueden estar vacíos.";                    
            $_SESSION["error"]= "Nombre y apellidos no pueden estar vacíos.";

        } else {
            $datosActualizar = [
                'nombre' => $nombre,
                'apellidos' => $apellidos
            ];
            
            if ($u->updateUser($id, $datosActualizar)) {
                $exito = true;
                $_SESSION["usuario"]["nombre"] = $nombre;
                $_SESSION["usuario"]["apellidos"] = $apellidos;
                $datos["nombre"] = $nombre;
                $datos["apellidos"] = $apellidos;
            } else {
                $errores[] = "Error al actualizar el nombre.";
                $_SESSION["error"]= "Error al actualizar el nombre.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Nombre - AuraPost</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Tus estilos -->
    <link href="../css/accessibility.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/mobile.css" rel="stylesheet">
</head>

<body>

<!-- 🌊 Fondo animado -->
<div class="background-scene">
    <div class="betta-fish betta1"></div>
    <div class="betta-fish betta2"></div>
    <div class="betta-fish betta3"></div>
    <div class="bubble-container">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>
    <div class="water-waves"></div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light aura-nav fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="../presentacion/inici.php">
            <div class="logo-container">
                <img src="../src/logo.png" alt="AuraPost" style="width: 90%">
            </div>
            <span class="brand-text">AuraPost</span>
        </a>

        <div class="d-flex align-items-center gap-3">
            <a href="../presentacion/configuracion.php" class="nav-icon" data-tooltip="Volver">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>
</nav>

<!-- CONTENIDO -->
<main class="container main-container py-4">

    <div class="text-center mb-5">
        <h1 class="text-gradient" style="font-size: 2.5rem; font-weight: 700;">
            <i class="bi bi-person-badge me-3"></i>Actualizar Nombre
        </h1>
        <p class="text-muted">Modifica tu nombre completo</p>
    </div>

    <!-- Mensajes de error/éxito -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                <?php foreach($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            ¡Nombre actualizado correctamente!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="settings-section">
        <div class="glass-card p-4">
            <form method="POST" action="">
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Nombre y apellidos</label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           name="valor" 
                           value="<?= htmlspecialchars($datos["nombre"] . " " . $datos["apellidos"]) ?>"
                           placeholder="Ej: Andrea Milé"
                           required>
                    <small class="text-muted">Introduce tu nombre y apellidos separados por espacio</small>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="../presentacion/configuracion.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>