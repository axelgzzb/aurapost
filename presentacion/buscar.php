<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";


if (!isset($_SESSION["usuario"])) {
   header("Location: error.php");
   exit;
}


$u = new Usuaris();
$usuarioActual = $_SESSION['usuario'];
$id_usuario = $usuarioActual['id_usuario'];


$datosUsuario = $u->obtenerDatosUsuario($id_usuario);
$query = isset($_GET['q']) ? trim($_GET['q']) : "";
$resultados = [];


if (!empty($query)) {
   $resultados = $u->buscarUsuarios($query);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>Buscar: <?= htmlspecialchars($query) ?> - AuraPost</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
 <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <link href="../css/custom.css" rel="stylesheet">
 <link href="../css/mobile.css" rel="stylesheet">
</head>


<body>
 <div class="background-scene">
   <div class="betta-fish betta1"></div>
   <div class="betta-fish betta2"></div>
   <div class="betta-fish betta3"></div>
   <div class="bubble-container">
     <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
   </div>
   <div class="water-waves"></div>
 </div>


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
         <form action="buscar.php" method="GET" class="d-flex align-items-center m-0">
           <i class="bi bi-search search-icon"></i>
           <input name="q" class="form-control search-bar" type="search" placeholder="Buscar..." value="<?= htmlspecialchars($query) ?>">
         </form>
       </div>
       <a href="inici.php" class="nav-icon" data-tooltip="Inicio"><i class="fa-solid fa-house"></i></a>
       <a href="notificaciones.php" class="nav-icon" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
       <a href="discover.html" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
       <a href="daily.php" class="nav-icon" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
       <a href="profile.php" class="nav-icon" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
       <a href="configuracion.php" class="nav-icon" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
     </div>
   </div>
 </nav>


 <div class="bottom-bar d-lg-none">
   <a href="inici.php" class="nav-icon"><i class="bi bi-house-door-fill"></i></a>
   <a href="discover.html" class="nav-icon"><i class="fa-solid fa-water"></i></a>
   <a href="daily.php" class="nav-icon"><i class="fa-solid fa-feather"></i></a>
   <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
 </div>


 <main class="container main-container" style="padding-top: 100px;">
   <div class="row justify-content-center">
     <div class="col-xl-7 col-lg-9">
      
       <div class="glass-card p-4 mb-4">
         <h4 class="mb-0 text-start" style="color: #2d2d2d; font-weight: 600;">
           Resultados para:
           <span style="color: #8c52ff; font-weight: 700;">
               "<?= htmlspecialchars($query) ?>"
           </span>
         </h4>
       </div>


       <div class="search-results">
         <?php if (count($resultados) > 0): ?>
           <?php foreach ($resultados as $res):
             $fotoRes = !empty($res['foto_perfil']) ? "uploads/" . $res['foto_perfil'] : "../src/logo.png";
           ?>
             <div class="glass-card mb-3 p-3" style="border-radius: 20px; transition: 0.3s;">
               <div class="d-flex align-items-center justify-content-between">
                 <div class="d-flex align-items-center">
                   <img src="<?= $fotoRes ?>" class="post-avatar" alt="Avatar" style="width: 60px; height: 60px; border: 2px solid #8c52ff; object-fit: cover;">
                   <div class="ms-3">
                     <h6 class="mb-0 fw-bold" style="color: #2d2d2d;">
                       <?= htmlspecialchars($res['nombre'] . " " . $res['apellidos']) ?>
                     </h6>
                     <span class="badge mt-1" style="background: rgba(140, 82, 255, 0.1); color: #8c52ff; font-weight: 500; font-size: 0.85rem;">
                       @<?= htmlspecialchars($res['nombre_usuario']) ?>
                     </span>
                     <?php if($res['pronombres']): ?>
                       <p class="mb-0 text-muted small mt-1"><?= htmlspecialchars($res['pronombres']) ?></p>
                     <?php endif; ?>
                   </div>
                 </div>
                
                 <a href="visor.php?alies=<?= urlencode($res['nombre_usuario']) ?>" class="btn btn-primary-gradient rounded-pill px-4 shadow-sm">
                   Ver Perfil
                 </a>
               </div>
             </div>
           <?php endforeach; ?>
         <?php else: ?>
           <div class="glass-card p-5 text-center">
             <i class="bi bi-search" style="font-size: 4rem; color: #8c52ff; opacity: 0.6;"></i>
             <h5 class="mt-3 fw-bold" style="color: #2d2d2d;">No hay coincidencias</h5>
             <p class="text-muted">Intenta buscar por nombre, apellido o @usuario</p>
             <a href="inici.php" class="btn btn-outline-secondary btn-sm mt-3">Volver al inicio</a>
           </div>
         <?php endif; ?>
       </div>


     </div>
   </div>
 </main>


 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>