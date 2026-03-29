<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit; }


$u = new Usuaris();
$id_sesion = $_SESSION['usuario']['id_usuario'];
$notifs = $u->obtenerNotificacionesDinamicas($id_sesion);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - AuraPost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <style>
        body { color: #ffffff; }
        .notifications-container { max-width: 750px; margin: 0 auto; }


        /* TÍTULO LILA */
        .title-lila {
            font-size: 2.5rem;
            background: linear-gradient(to right, #a29bfe, #d6a2e8, #6c5ce7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 15px rgba(162, 155, 254, 0.4);
            font-weight: 700;
        }


        .notif-card {
            background: rgba(154, 95, 174, 0.33);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            margin-bottom: 12px;
        }


        .notif-user { font-weight: 700; color: #ffffff !important; text-decoration: none; }
        .notif-avatar { width: 55px; height: 55px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.2); }


        .notif-icon-badge {
            position: absolute; bottom: -2px; right: -2px; width: 22px; height: 22px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; border: 2px solid #1a1a2e;
        }


        .bg-like { background: #ff4757; }
        .bg-comment { background: #4834d4; }


        .post-preview {
            display: block; background: rgba(0, 0, 0, 0.2); padding: 10px; border-radius: 10px;
            margin-top: 8px; font-size: 0.9rem; color: rgba(255, 255, 255, 0.8);
            text-decoration: none; border-left: 3px solid rgba(255, 255, 255, 0.3);
        }


        .nav-icon.active-lila { color: #a29bfe !important; }
    </style>
</head>
<body>
<body>
  <!-- 🌊 Fondo animado premium -->


    <div class="bubble-container">
      <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
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
          <form action="buscar.php" method="GET" class="d-flex align-items-center">
            <i class="bi bi-search search-icon"></i>
            <input name="q" class="form-control search-bar" type="search" placeholder="Buscar usuarios..." required>
          </form>
      </div>
        <a href="inici.php" class="nav-icon " data-tooltip="Inicio"><i class="fa-solid fa-house"></i></a>
        <a href="notificaciones.php" class="nav-icon active" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
        <a href="discover.php" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
        <a href="daily.php" class="nav-icon" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
        <a href="profile.php" class="nav-icon" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
        <a href="configuracion.php" class="nav-icon" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
      </div>
    </div>
  </nav>


  <!-- MENÚ INFERIOR FIJO SOLO MÓVIL -->
  <div class="bottom-bar d-lg-none">
    <a href="inici.php" class="nav-icon active"><i class="bi bi-house-door-fill"></i></a>
    <a href="discover.php" class="nav-icon"><i class="fa-solid fa-water"></i></a>
    <a href="daily.php" class="nav-icon"><i class="fa-solid fa-feather"></i></a>
    <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
  </div>


<main class="container mt-5 pt-5">
    <div class="notifications-container mt-4">
        <h2 class="title-lila mb-4">Notificaciones</h2>


        <?php if (empty($notifs)): ?>
            <div class="glass-card p-5 text-center">
                <i class="bi bi-moon-stars fs-1 opacity-50"></i>
                <p class="mt-3">Aún no hay actividad.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifs as $n):
                $fotoOrig = !empty($n['foto_perfil']) ? "uploads/".$n['foto_perfil'] : "../src/logo.png";
                $esLike = ($n['tipo'] == 'like');
            ?>
                <div class="notif-card p-3">
                    <div class="d-flex align-items-start">
                        <div class="position-relative me-3">
                            <img src="<?= $fotoOrig ?>" class="notif-avatar">
                            <div class="notif-icon-badge <?= $esLike ? 'bg-like' : 'bg-comment' ?>">
                                <i class="fa-solid <?= $esLike ? 'fa-heart' : 'fa-comment' ?> text-white"></i>
                            </div>
                        </div>
                       
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div class="notif-text text-white">
                                    <a href="profile.php?alies=<?= urlencode($n['nombre_usuario']) ?>" class="notif-user">
                                        @<?= htmlspecialchars($n['nombre_usuario']) ?>
                                    </a>
                                    <span><?= $esLike ? 'reaccionó a tu Aura' : 'comentó tu publicación' ?></span>
                                </div>
                                <span class="small opacity-50"><?= date("H:i", strtotime($n['fecha'])) ?></span>
                            </div>


                            <a href="profile.php" class="post-preview">
                                <?= htmlspecialchars(mb_strimwidth($n['post_snippet'], 0, 85, "...")) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

