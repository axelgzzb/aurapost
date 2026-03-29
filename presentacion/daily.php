<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";

// Verificar que el usuario está logueado
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "Debes iniciar sesión para acceder";
    $_SESSION["volver_a"] = "daily.php";
    header("Location: error.php");
    exit;
}

$u = new Usuaris();
$usuarioActual = $_SESSION['usuario'];
$id_usuario = $usuarioActual['id_usuario'];

// Obtener datos del usuario
$datosUsuario = $u->obtenerDatosUsuario($id_usuario);
$foto = !empty($datosUsuario["foto_perfil"]) ? "uploads/" . $datosUsuario["foto_perfil"] : "../src/logo.png";

// Verificar si ya completó el reto hoy
$retoCompletadoHoy = $u->verificarRetoCompletadoHoy($id_usuario);

// Mensajes de éxito/error
$mensaje_exito = '';
$mensaje_error = '';

if (isset($_GET['success']) && $_GET['success'] === 'reto_completado') {
    $mensaje_exito = '¡Felicidades! Has completado el reto diario.';
}

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'vacio':
            $mensaje_error = 'El contenido no puede estar vacío';
            break;
        case 'ya_completado':
            $mensaje_error = 'Ya has completado el reto de hoy. ¡Vuelve mañana!';
            break;
        case 'db':
            $mensaje_error = 'Error al guardar el post. Intenta de nuevo.';
            break;
        default:
            $mensaje_error = 'Ha ocurrido un error. Intenta de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AuraPost - Reto Diario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <link href="../css/custom.css" rel="stylesheet">
  <link href="../css/mobile.css" rel="stylesheet">
  <style>
    .challenge-featured {
      border: 2px solid rgba(255, 193, 7, 0.3);
      background: linear-gradient(135deg, rgba(255, 217, 61, 0.1), rgba(255, 193, 7, 0.05));
    }
    
    .challenge-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #ffd93d, #ffc107);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
    }
    
    .challenge-instructions ul li {
      padding: 0.5rem 0;
    }
    
    .challenge-rewards {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 12px;
    }
    
    .reward-item {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .challenge-badge {
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, #ffd93d, #ffc107);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
      box-shadow: 0 10px 30px rgba(255, 193, 7, 0.3);
      animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .completed-badge {
      background: linear-gradient(135deg, #4ade80, #22c55e);
      padding: 1rem 2rem;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
      box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
    }
  </style>
</head>

<body>
  <!-- 🌊 Fondo animado -->
  <div class="background-scene">
    <div class="betta-fish betta1"></div>
    <div class="betta-fish betta2"></div>
    <div class="bubble-container">
      <div class="bubble"></div>
      <div class="bubble"></div>
      <div class="bubble"></div>
    </div>
    <div class="water-waves"></div>
  </div>

<!-- NAVBAR -->
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
      <a href="discover.html" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
      <a href="daily.php" class="nav-icon active" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
      <a href="profile.php" class="nav-icon" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
      <a href="configuracion.php" class="nav-icon" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
    </div>
  </div>
</nav>

  <!-- 🌞 CONTENIDO -->
  <div class="container mt-5 pt-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <?php if ($mensaje_exito): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <?php if ($mensaje_error): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <!-- Header del Reto -->
        <div class="text-center mb-5">
          <div class="challenge-badge mb-3">
            <i class="bi bi-sun-fill display-4 text-white"></i>
          </div>
          <h1 class="text-gradient fw-bold">Reto del Día</h1>
          <p class="lead text-muted">Completa el desafío diario</p>
        </div>

        <?php if ($retoCompletadoHoy): ?>
          <!-- Reto ya completado -->
          <div class="glass-card p-5 text-center mb-4">
            <div class="completed-badge mb-4">
              <i class="bi bi-check-circle-fill"></i>
              <span>¡Reto Completado!</span>
            </div>
            <h4 class="mb-3">¡Excelente trabajo!</h4>
            <p class="text-muted mb-4">Ya has completado el reto de hoy. Vuelve mañana para un nuevo desafío.</p>
            <div class="d-flex justify-content-center gap-3">
              <a href="inici.php" class="btn btn-primary-gradient">
                <i class="bi bi-house-fill me-2"></i>Ir al Inicio
              </a>
              <a href="profile.php" class="btn btn-outline-primary">
                <i class="bi bi-person-fill me-2"></i>Ver mi Perfil
              </a>
            </div>
          </div>
        <?php else: ?>
          <!-- Card del Reto Actual -->
          <div class="glass-card p-4 mb-4 challenge-featured">
            <div class="d-flex align-items-center mb-3">
              <div class="challenge-icon me-3">
                <i class="bi bi-stars text-white"></i>
              </div>
              <div>
                <span class="badge bg-warning text-dark mb-2">ACTIVO HOY</span>
                <h4 class="mb-1">🌞 Gratitud Matutina</h4>
              </div>
            </div>
            
            <p class="mb-3">Piensa en algo o alguien que te haya hecho sentir agradecido recientemente. Escribe un post corto que capture esa sensación.</p>
            
            <div class="challenge-instructions">
              <h6 class="text-primary">Tu misión:</h6>
              <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Sé honesto y auténtico</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Manténlo breve y específico</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Comparte desde el corazón</li>
              </ul>
            </div>

            <div class="challenge-rewards mt-3">
              
              <div class="reward-item">
                <i class="bi bi-lightning-charge-fill text-info me-2"></i>
                <span>+1 día de racha</span>
              </div>
              
            </div>
          </div>

          <!-- ✏️ Editor del Reto -->
          <div class="glass-card p-4 mb-5">
            <h5 class="mb-3">
              <i class="bi bi-pencil-fill text-primary me-2"></i>Tu publicación
            </h5>
            
            <form action="../negocio/completar_reto.php" method="POST">
              <div class="d-flex gap-3 align-items-start mb-3">
                <img src="<?php echo $foto; ?>" class="create-avatar" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 4px 12px rgba(123,78,255,0.2);">
                <div class="flex-grow-1">
                  <textarea name="contenido" class="form-control mb-3" rows="5" placeholder="Escribe aquí tu reflexión de gratitud..." required></textarea>
                  
                  <!-- Selector de emoción (oculto por defecto, se usa la emoción 1 - Feliz para el reto de gratitud) -->
                  <input type="hidden" name="id_emocion" value="1">
                  <input type="hidden" name="es_reto_diario" value="1">
                  
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="bi bi-info-circle me-1"></i>
                      Este post se publicará con la emoción "Feliz" 😊
                    </small>
                    <button type="submit" class="btn btn-primary-gradient">
                      <i class="bi bi-send-fill me-2"></i>Publicar Reto
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        <?php endif; ?>
        
      </div>
    </div>
  </div>

  <!-- MENÚ INFERIOR FIJO SOLO MÓVIL -->
  <div class="bottom-bar d-lg-none">
    <a href="inici.php" class="nav-icon"><i class="bi bi-house-door-fill"></i></a>
    <a href="discover.html" class="nav-icon"><i class="fa-solid fa-water"></i></a>
    <a href="daily.php" class="nav-icon active"><i class="fa-solid fa-feather"></i></a>
    <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>