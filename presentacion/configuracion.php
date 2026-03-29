<?php
session_start();

require_once "../negocio/Usuaris.php";

// 1) Comprobar si el usuario está logueado
if (!isset($_SESSION["usuario"]["id_usuario"])) {
    header("Location: presentacion/index.html");
    exit;
}

$id = $_SESSION["usuario"]["id_usuario"];

// 2) Cargar datos del usuario desde BD
$u = new Usuaris();
$datos = $u->getUserById($id);

if (!$datos) {
    $_SESSION["error"] = "No se pudieron cargar tus datos.";
    header("Location: error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - AuraPost</title>

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

<!-- NAVBAR COMPLETA -->
<nav class="navbar navbar-expand-lg navbar-light aura-nav fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="inici.php">
            <div class="logo-container">
                <img src="../src/logo.png" alt="AuraPost" style="width: 90%">
            </div>
            <span class="brand-text">AuraPost</span>
        </a>

            <div class="d-flex align-items-center gap-3">
            <div class="search-container">
                <form action="buscar.php" method="GET" class="d-flex align-items-center">
                    <i class="bi bi-search search-icon"></i>
                    <input name="q" class="form-control search-bar" type="search" placeholder="Buscar usuarios..." required>
                </form>
            </div>

            <a href="inici.php" class="nav-icon" data-tooltip="Inicio"><i class="fa-solid fa-house"></i></a>
            <a href="notificaciones.php" class="nav-icon" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
            <a href="discover.php" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
            <a href="daily.php" class="nav-icon" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
            <a href="profile.php" class="nav-icon" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
            <a href="configuracion.php" class="nav-icon active" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
        </div>
    </div>
</nav>

<!-- MENÚ MÓVIL -->
<div class="bottom-bar d-lg-none">
    <a href="inici.php" class="nav-icon"><i class="fa-solid fa-house"></i></a>
    <a href="discover.php" class="nav-icon"><i class="fa-solid fa-water"></i></a>
    <a href="daily.php" class="nav-icon"><i class="fa-solid fa-feather"></i></a>
    <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
</div>

<!-- CONTENIDO -->
<main class="container main-container py-4">

    <div class="text-center mb-5">
        <h1 class="text-gradient" style="font-size: 2.5rem; font-weight: 700;">
            <i class="bi bi-gear-fill me-3"></i>Configuración
        </h1>
        <p class="text-muted">Personaliza tu experiencia en AuraPost</p>
    </div>

    <!-- ===== ACCESIBILIDAD ===== -->
  

    <!-- ===== PERFIL ===== -->
    <div class="settings-section">
        <h3 class="section-title"><i class="bi bi-person-fill me-2"></i>Perfil</h3>
        <p class="section-subtitle">
            Esta información se mostrará públicamente, así que ten cuidado con lo que compartes.
        </p>

        <div class="glass-card p-4">
            <div class="settings-row">
                <span class="label"><i class="bi bi-person-badge me-2"></i>Nombre completo</span>
                <span class="value"><?= htmlspecialchars($datos["nombre"] . " " . $datos["apellidos"]) ?></span>
                <a href="../negocio/actualizar_nombre.php" class="update-link">Actualizar</a>
            </div>

            <div class="settings-row">
                <span class="label"><i class="bi bi-envelope-fill me-2"></i>Correo electrónico</span>
                <span class="value"><?= htmlspecialchars($datos["email"]) ?></span>
                <a href="../negocio/actualizar_email.php" class="update-link">Actualizar</a>
            </div>

            <div class="settings-row">
                <span class="label"><i class="bi bi-at me-2"></i>Nombre de usuario</span>
                <span class="value">@<?= htmlspecialchars($datos["nombre_usuario"]) ?></span>
                <a href="../negocio/actualizar_usuario.php" class="update-link">Actualizar</a>
            </div>
        </div>
    </div>

    <!-- ===== IDIOMA Y FECHAS ===== -->
    
    
    <!-- ===== ZONA DE PELIGRO ===== -->
    <div class="settings-section">
        <h3 class="section-title text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Zona de peligro
        </h3>
        <p class="section-subtitle">
            Acciones irreversibles. Procede con precaución.
        </p>

        <div class="glass-card p-4 border-danger" style="border: 2px solid rgba(220,53,69,.3)!important;">
            <div class="settings-row">
                <div class="flex-grow-1">
                    <span class="label text-danger d-block mb-1"><i class="bi bi-trash-fill me-2"></i>Eliminar cuenta</span>
                    <span class="value d-block" style="font-size: 0.85rem;">Borra permanentemente tu cuenta y todos tus datos</span>
                </div>

                <a href="../negocio/eliminar_cuenta.php" class="btn btn-outline-danger btn-sm">
                    Eliminar
                </a>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>