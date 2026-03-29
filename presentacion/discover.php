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

// Obtenemos los primeros 12 posts para la carga inicial
$posts_discover = $u->tenerPostDiscover(12); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuraPost - Discover</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <style>
        body { margin: 0; overflow: hidden; height: 100vh; background-color: #001220; }
        
        .ocean-container {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: 5;
            pointer-events: none;
        }

        .floating-post {
            position: absolute;
            pointer-events: auto;
            cursor: pointer;
            padding: 1.2rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            min-width: 200px;
            max-width: 280px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            opacity: 0; /* Empiezan invisibles para el efecto fade-in */
            transition: opacity 1s ease, transform 0.5s ease;
        }

        .user-name {
            color: #000000 !important; /* Nombre en NEGRO */
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        /* Animación de flotación suave */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
    </style>
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
          <form action="buscar.php" method="GET" class="d-flex align-items-center">
            <i class="bi bi-search search-icon"></i>
            <input name="q" class="form-control search-bar" type="search" placeholder="Buscar usuarios..." required>
          </form>
      </div>
        <a href="inici.php" class="nav-icon active" data-tooltip="Inicio"><i class="fa-solid fa-house"></i></a>
        <a href="notificaciones.php" class="nav-icon" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
        <a href="discover.php" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
        <a href="daily.php" class="nav-icon" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
        <a href="profile.php" class="nav-icon" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
        <a href="configuracion.php" class="nav-icon" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
      </div>
    </div>
  </nav>

    <main class="ocean-container" id="ocean-container">
        </main>

  <div class="bottom-bar d-lg-none">
    <a href="inici.php" class="nav-icon active"><i class="bi bi-house-door-fill"></i></a>
    <a href="discover.php" class="nav-icon"><i class="fa-solid fa-water"></i></a>
    <a href="daily.php" class="nav-icon"><i class="fa-solid fa-feather"></i></a>
    <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
  </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pasamos los posts iniciales de PHP a JS
        const initialPosts = <?php echo json_encode($posts_discover); ?>;
        const container = document.getElementById('ocean-container');

        function createPostElement(postData) {
            const div = document.createElement('div');
            div.className = 'floating-post';
            
            // Posición aleatoria
            const top = Math.floor(Math.random() * 60) + 20;
            const left = Math.floor(Math.random() * 70) + 10;
            
            div.style.top = `${top}%`;
            div.style.left = `${left}%`;
            div.style.animation = `float ${3 + Math.random() * 3}s ease-in-out infinite`;
            
            div.innerHTML = `
                <p class="mb-2">"${postData.contenido}"</p>
                <span class="user-name">@${postData.nombre_usuario}</span>
            `;

            div.onclick = () => {
                window.location.href = `visor.php?alies=${encodeURIComponent(postData.nombre_usuario)}`;
            };

            return div;
        }

        function refreshPosts() {
            // 1. Desvanecer posts actuales
            const currentPosts = document.querySelectorAll('.floating-post');
            currentPosts.forEach(p => p.style.opacity = '0');

            // 2. Esperar al fade out para quitarlos y poner nuevos
            setTimeout(() => {
                container.innerHTML = '';
                
                // Mezclar y mostrar nuevos
                const shuffled = [...initialPosts].sort(() => 0.5 - Math.random());
                const selected = shuffled.slice(0, 6); // Mostramos 6 a la vez para que no sature

                selected.forEach(post => {
                    const el = createPostElement(post);
                    container.appendChild(el);
                    // Pequeño timeout para el fade-in
                    setTimeout(() => el.style.opacity = '1', 100);
                });
            }, 1000);
        }

        // Iniciar el ciclo
        refreshPosts();
        setInterval(refreshPosts, 15000); // Cada 15 segundos
    </script>
</body>
</html>