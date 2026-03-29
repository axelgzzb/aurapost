<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";


// 1. Lógica de control de sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}


$u = new Usuaris();
$usuarioActual = $_SESSION['usuario'];
$id_usuario_sesion = $usuarioActual['id_usuario'];


// 2. Determinar qué perfil cargar (ESTO DEBE IR ANTES DE CARGAR DATOS)
$alias_perfil = isset($_GET['alies']) ? $_GET['alies'] : $usuarioActual['nombre_usuario'];
$datosUsuario = $u->getUserByUsername($alias_perfil);


if (!$datosUsuario) {
    die("Usuario no encontrado.");
}


// 3. AQUÍ se define la variable que te estaba dando error
$id_usuario_perfil = $datosUsuario["id_usuario"];


// Ahora sí podemos usar $id_usuario_perfil
$esMiPerfil = ($id_usuario_perfil == $id_usuario_sesion);
$yaLoSigo = $u->esSeguidor($id_usuario_sesion, $id_usuario_perfil);


// 4. Cargar datos dinámicos (AHORA SÍ FUNCIONARÁ)
$seguidores = $u->contarSeguidores($id_usuario_perfil);
$seguidos = $u->contarSeguidos($id_usuario_perfil);
$totalPostsCount = $u->contarPostsUsuario($id_usuario_perfil);
$postsReales = $u->getPostsUsuario($id_usuario_perfil);


// ESTA es la línea nueva colocada en el sitio correcto:
$listaAmigos = $u->getSeguidos($id_usuario_perfil);


$postsLikes = $u->obtenerLikes($id_usuario_perfil);
$postsGuardados = $u->obtenerGuardados($id_usuario_perfil);
$espectroEmocional = $u->calcularEspectroEmocional($id_usuario_perfil);


// Foto de perfil
$foto = !empty($datosUsuario["foto_perfil"]) ? "uploads/" . $datosUsuario["foto_perfil"] : "../src/lila.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AuraPost - Perfil de <?php echo htmlspecialchars($datosUsuario['nombre_usuario']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <link href="../css/custom.css" rel="stylesheet">
  <link href="../css/mobile.css" rel="stylesheet">
  <style>
    .comments-section {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      display: none;
    }
   
    .comments-section.active {
      display: block;
    }
   
    .comment-item {
      display: flex;
      gap: 0.75rem;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
    }
   
    .comment-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
    }
   
    .comment-content {
      flex: 1;
    }
   
    .comment-author {
      font-weight: 600;
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }
   
    .comment-text {
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }
   
    .comment-time {
      font-size: 0.75rem;
      color: #999;
    }
   
    .comment-input-container {
      display: flex;
      gap: 0.75rem;
      margin-top: 1rem;
      align-items: center;
    }
   
    .comment-input {
      flex: 1;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 0.5rem 1rem;
      color: #fff;
      font-size: 0.875rem;
    }
   
    .comment-input:focus {
      outline: none;
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(108, 92, 231, 0.5);
    }
   
    .comment-input::placeholder {
      color: #999;
    }
   
    .comment-submit {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      cursor: pointer;
      transition: transform 0.2s;
    }
   
    .comment-submit:hover {
      transform: scale(1.1);
    }
  </style>
</head>


<body>
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
      <a href="notifications.php" class="nav-icon" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
      <a href="discover.php" class="nav-icon" data-tooltip="Descubrir"><i class="fa-solid fa-water"></i></a>
      <a href="daily.php" class="nav-icon" data-tooltip="Reto Diario"><i class="fa-solid fa-feather"></i></a>
      <a href="profile.php" class="nav-icon active" data-tooltip="Perfil"><i class="fa-solid fa-user"></i></a>
      <a href="configuracion.php" class="nav-icon" data-tooltip="Configuración"><i class="fa-solid fa-gear"></i></a>
    </div>
  </div>
</nav>
  <div class="bottom-bar d-lg-none">
    <a href="inici.php" class="nav-icon"><i class="bi bi-house-door-fill"></i></a>
    <a href="discover.php" class="nav-icon"><i class="fa-solid fa-water"></i></a>
    <a href="daily.php" class="nav-icon"><i class="fa-solid fa-feather"></i></a>
    <a href="profile.php" class="nav-icon active"><i class="fa-solid fa-user"></i></a>
  </div>
  <main class="container-fluid mt-5 pt-5 px-lg-5">
    <div class="row g-4">
     
      <div class="col-lg-4">
        <div class="profile-main-card glass-card text-center p-4 mb-4">
          <div class="profile-header mb-4">
            <div class="position-relative d-inline-block">
              <img src="<?php echo $foto; ?>" class="profile-avatar" alt="<?php echo htmlspecialchars($datosUsuario['nombre']); ?>">
              <div class="aura-ring"></div>
              <div class="online-status"></div>
            </div>
          </div>
         
          <h4 class="profile-name mb-1" style="color: #000000; font-weight: bold; font-size: 1.8rem;">
            <?php echo htmlspecialchars($datosUsuario['nombre']); ?>
          </h4>


<p class="profile-handle mb-3" style="color: #6c5ce7; font-weight: 500;">
    @<?php echo htmlspecialchars($datosUsuario['nombre_usuario']); ?>
</p>
         
          <div class="profile-stats row g-3 mb-4">
            <div class="col-4">
              <div class="stat-number"><?php echo $seguidores; ?></div>
              <div class="stat-label">Seguidores</div>
            </div>
            <div class="col-4">
              <div class="stat-number"><?php echo $seguidos; ?></div>
              <div class="stat-label">Siguiendo</div>
            </div>
            <div class="col-4">
              <div class="stat-number"><?php echo $totalPostsCount; ?></div>
              <div class="stat-label">Posts</div>
            </div>
          </div>
         
          <div class="profile-actions">
            <?php if ($esMiPerfil): ?>
              <a href="configuracion.php" class="btn btn-primary-gradient w-100 mb-2">
                <i class="bi bi-pencil me-2"></i>Editar Perfil
              </a>
            <?php else: ?>
              <form action="../negocio/gestionar_seguimiento.php" method="POST">
                <input type="hidden" name="id_seguido" value="<?php echo $id_usuario_perfil; ?>">
                <input type="hidden" name="alias" value="<?php echo htmlspecialchars($alias_perfil); ?>">
                <button type="submit" name="accion" value="<?php echo $yaLoSigo ? 'unfollow' : 'seguir'; ?>" class="btn <?php echo $yaLoSigo ? 'btn-outline-danger' : 'btn-primary-gradient'; ?> w-100 mb-2">
                  <i class="bi <?php echo $yaLoSigo ? 'bi-person-x' : 'bi-person-plus'; ?> me-2"></i>
                  <?php echo $yaLoSigo ? 'Dejar de seguir' : 'Seguir'; ?>
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
        <!-- Estadísticas de Emociones -->
        <div class="emotion-stats glass-card p-4 mb-4">
          <h6 class="mb-3">📊 Mi Espectro Emocional</h6>
         
          <!-- Felicidad -->
          <div class="emotion-bar mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>😊 Felicidad</span>
              <span class="text-primary"><?php echo $espectroEmocional[1]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-warning" style="width: <?php echo $espectroEmocional[1]; ?>%"></div>
            </div>
          </div>
         
          <!-- Tristeza -->
          <div class="emotion-bar mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>😢 Tristeza</span>
              <span class="text-primary"><?php echo $espectroEmocional[2]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-info" style="width: <?php echo $espectroEmocional[2]; ?>%"></div>
            </div>
          </div>
         
          <!-- Enojo -->
          <div class="emotion-bar mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>😠 Enfado</span>
              <span class="text-primary"><?php echo $espectroEmocional[3]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-danger" style="width: <?php echo $espectroEmocional[3]; ?>%"></div>
            </div>
          </div>
         
          <!-- Vergüenza -->
          <div class="emotion-bar mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>😳 Vergüenza</span>
              <span class="text-primary"><?php echo $espectroEmocional[4]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar" style="background-color: #dc2eff; width: <?php echo $espectroEmocional[4]; ?>%"></div>
            </div>
          </div>
         
          <!-- Miedo -->
          <div class="emotion-bar mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>😨 Miedo</span>
              <span class="text-primary"><?php echo $espectroEmocional[5]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar" style="background-color: #4000ff; width: <?php echo $espectroEmocional[5]; ?>%"></div>
            </div>
          </div>
         
          <!-- Asco -->
          <div class="emotion-bar">
            <div class="d-flex justify-content-between mb-1">
              <span>🤢 Asco</span>
              <span class="text-primary"><?php echo $espectroEmocional[6]; ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar" style="background-color: #13622b; width: <?php echo $espectroEmocional[6]; ?>%"></div>
            </div>
          </div>
        </div>
        <div class="achievements-card glass-card p-4">
          <h6 class="mb-3">🏆 Mis Logros</h6>
          <div class="text-center">
            <div class="achievement-item mb-3">
              <div class="achievement-icon mb-3">
                <i class="bi bi-fire text-warning" style="font-size: 4rem;"></i>
              </div>
              <div class="achievement-info">
                <h5 class="mb-2">Racha Actual</h5>
                <p class="text-muted mb-2"><?php echo $datosUsuario['racha'] ?? 0; ?> días consecutivos activo</p>
                <div class="d-flex justify-content-center align-items-center gap-2">
                  <div class="progress" style="height: 10px; width: 150px;">
                    <div class="progress-bar bg-warning" style="width: <?php echo min(($datosUsuario['racha'] ?? 0) * 10, 100); ?>%"></div>
                  </div>
                  <span class="text-warning fw-bold"><?php echo $datosUsuario['racha'] ?? 0; ?>/10</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-lg-8">
        <div class="profile-tabs glass-card p-3 mb-4">
          <ul class="nav nav-pills nav-fill" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">
                <i class="bi bi-chat-square-text me-2"></i>Mis Posts
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="likes-tab" data-bs-toggle="tab" data-bs-target="#likes" type="button" role="tab">
                <i class="bi bi-heart me-2"></i>Me Gusta
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab">
                <i class="bi bi-bookmark me-2"></i>Guardados
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends" type="button" role="tab">
                <i class="bi bi-people me-2"></i>Amigos
              </button>
            </li>
          </ul>
        </div>


    <div class="tab-content" id="profileTabContent">
    <div class="tab-pane fade show active" id="posts" role="tabpanel">
        <div class="posts-container">
            <?php if (!empty($postsReales)): ?>
                <?php foreach ($postsReales as $post):
                    $pid = $post['id_post'];  
                    $comentarios = $u->obtenerComentariosPost($pid);
                
                    // Determinar la clase de emoción
                   
                    switch($post['id_emocion']) {
                        case 1:
                            $emocionClase = 'emotion-happy-post';
                            $emocionNombre = 'Feliz';
                            break;
                        case 2:
                            $emocionClase = 'emotion-sad-post';
                            $emocionNombre = 'Triste';
                            break;
                        case 3:
                            $emocionClase = 'emotion-angry-post';
                            $emocionNombre = 'Enfadado';
                            break;
                        case 4:
                            $emocionClase = 'emotion-shame-post';
                            $emocionNombre = 'Vergüenza';
                            break;
                        case 5:
                            $emocionClase = 'emotion-fear-post';
                            $emocionNombre = 'Miedo';
                            break;
                        case 6:
                            $emocionClase = 'emotion-disgust-post';
                            $emocionNombre = 'Asco';
                            break;
                    }
                    // Si es un post de reto, cambiar la clase y el nombre
                    if (isset($post['tipo_post']) && $post['tipo_post'] === 'reto') {
                      $emocionClase = 'challenge';
                      $emocionNombre = '🏆 Reto del Día';
                    }
                ?>
                    <article class="post-card <?php echo $emocionClase; ?> mb-4">
                        <div class="post-header">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $foto; ?>" class="post-avatar" alt="Avatar">
                                <div class="post-info ms-3">
                                    <h6 class="post-author mb-1"><?php echo htmlspecialchars($datosUsuario['nombre']); ?></h6>
                                    <span class="post-time"><?php echo date("d/m/Y H:i", strtotime($post['fecha'])); ?></span>
                                </div>
                            </div>
                            <div class="emotion-indicator">
                                <span><?php echo htmlspecialchars($emocionNombre); ?></span>
                            </div>
                        </div>
                       
                        <div class="post-content mt-3">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                        </div>


                        <div class="post-footer mt-3">
                            <button class="post-action like" data-post-id="<?= $post['id_post'] ?>">
                                <i class="far fa-heart"></i>
                                <span class="count"><?= $u->contarLikes($post['id_post']) ?></span>
                            </button>
                            <button class="post-action comment-toggle">
                                <i class="bi bi-chat"></i>
                                <span class="comment-count"><?= count($comentarios) ?></span>
                            </button>
                            <button class="post-action bookmark" data-post-id="<?= $post['id_post'] ?>">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                       
                        <!-- Sección de comentarios -->
                                   <div class="comments-section">
                        <div class="existing-comments">
                            <?php foreach ($comentarios as $com): 
                                $fotoCom = !empty($com['foto_perfil']) ? "uploads/" . $com['foto_perfil'] : "../src/logo.png";
                            ?>
                                <div class="comment-item">
                                    <img src="<?= $fotoCom ?>" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-author">@<?= htmlspecialchars($com['nombre_usuario']) ?></div>
                                        <div class="comment-text"><?= htmlspecialchars($com['contenido']) ?></div>
                                        <div class="comment-time"><?= date("H:i", strtotime($com['fecha'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="comment-input-container">
                            <img src="<?php echo $foto; ?>" class="comment-avatar">
                            <input type="text" class="comment-input" placeholder="Escribe un comentario...">
                            <button class="comment-submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="glass-card p-5 text-center">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: var(--primary-color); opacity: 0.5;"></i>
                    <h5 class="mt-3">No hay posts para mostrar</h5>
                    <p class="text-muted">¡Aún no has publicado nada!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
   
    <!-- Pestaña: Me Gusta -->
    <div class="tab-pane fade" id="likes" role="tabpanel">
        <div class="posts-container">
            <?php if (!empty($postsLikes)): ?>
                <?php foreach ($postsLikes as $p):
                    $fotoLike = !empty($p['foto_perfil']) ? 'uploads/'.$p['foto_perfil'] : '../src/logo.png';
                   
                    // Determinar emoción del post
                   
                    if (isset($p['id_emocion'])) {
                        switch($p['id_emocion']) {
                         
                            case 1: $emocionClaseLike = 'emotion-happy-post'; $emocionNombreLike = 'Feliz'; break;
                            case 2: $emocionClaseLike = 'emotion-sad-post'; $emocionNombreLike = 'Triste'; break;
                            case 3: $emocionClaseLike = 'emotion-angry-post'; $emocionNombreLike = 'Enfadado'; break;
                            case 4: $emocionClaseLike = 'emotion-shame-post'; $emocionNombreLike = 'Vergüenza'; break;
                            case 5: $emocionClaseLike = 'emotion-fear-post'; $emocionNombreLike = 'Miedo'; break;
                            case 6: $emocionClaseLike = 'emotion-disgust-post'; $emocionNombreLike = 'Asco'; break;
                            case 7: $emocionClaseLike = 'challenge'; $emocionNombreLike = '🏆 Reto del Día'; break;
                        }                        
                        // Si es un post de reto, cambiar la clase y el nombre
                        if (isset($post['tipo_post']) && $post['tipo_post'] === 'reto') {
                          $emocionClase = 'challenge';
                          $emocionNombre = '🏆 Reto del Día';
                        }
                    }
                ?>
                    <article class="post-card <?php echo $emocionClaseLike; ?> mb-4">
                        <div class="post-header">
                            <div class="d-flex align-items-center">
                                <img src="<?= $fotoLike ?>" class="post-avatar" alt="Avatar">
                                <div class="post-info ms-3">
                                    <h6 class="post-author mb-1">
                                        <a href="visor.php?alies=<?= urlencode($p['nombre_usuario']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= htmlspecialchars($p['nombre_usuario']) ?>
                                        </a>
                                    </h6>
                                    <span class="post-time"><?= $p['fecha'] ?></span>
                                </div>
                            </div>
                            <div class="emotion-indicator">
                                <span><?php echo htmlspecialchars($emocionNombreLike); ?></span>
                            </div>
                        </div>
                       
                        <div class="post-content mt-3">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($p['contenido'])) ?></p>
                        </div>
                       
                        <div class="post-footer mt-3">
                            <button class="post-action like active" data-post-id="<?= $p['id_post'] ?>">
                                <i class="fas fa-heart" style="color: #ff4757;"></i>
                                <span class="count"><?= $u->contarLikes($p['id_post']) ?></span>
                            </button>
                            <button class="post-action comment-toggle">
                                <i class="bi bi-chat"></i>
                                <span class="comment-count"><?= count($comentarios) ?></span>
                            </button>
                            <button class="post-action bookmark" data-post-id="<?= $p['id_post'] ?>">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                       
                            
                        <!-- Sección de comentarios -->
                                   <div class="comments-section">
                        <div class="existing-comments">
                            <?php foreach ($comentarios as $com): 
                                $fotoCom = !empty($com['foto_perfil']) ? "uploads/" . $com['foto_perfil'] : "../src/logo.png";
                            ?>
                                <div class="comment-item">
                                    <img src="<?= $fotoCom ?>" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-author">@<?= htmlspecialchars($com['nombre_usuario']) ?></div>
                                        <div class="comment-text"><?= htmlspecialchars($com['contenido']) ?></div>
                                        <div class="comment-time"><?= date("H:i", strtotime($com['fecha'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="comment-input-container">
                            <img src="<?php echo $foto; ?>" class="comment-avatar">
                            <input type="text" class="comment-input" placeholder="Escribe un comentario...">
                            <button class="comment-submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="glass-card p-5 text-center">
                    <i class="bi bi-heart" style="font-size: 4rem; color: var(--primary-color); opacity: 0.5;"></i>
                    <h5 class="mt-3">Tus reacciones aparecerán aquí</h5>
                    <p class="text-muted">Los posts que te gusten se guardarán en esta sección</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Pestaña: Guardados -->
    <div class="tab-pane fade" id="saved" role="tabpanel">
        <div class="posts-container">
            <?php if (!empty($postsGuardados)): ?>
                <?php foreach ($postsGuardados as $p):
                    $fotoGuardado = !empty($p['foto_perfil']) ? 'uploads/'.$p['foto_perfil'] : '../src/logo.png';
                   
                    // Determinar emoción del post guardado
                   
                    if (isset($p['id_emocion'])) {
                        switch($p['id_emocion']) {
                            case 1: $emocionClaseGuardado = 'emotion-happy-post'; $emocionNombreGuardado = 'Feliz'; break;
                            case 2: $emocionClaseGuardado = 'emotion-sad-post'; $emocionNombreGuardado = 'Triste'; break;
                            case 3: $emocionClaseGuardado = 'emotion-angry-post'; $emocionNombreGuardado = 'Enfadado'; break;
                            case 4: $emocionClaseGuardado = 'emotion-shame-post'; $emocionNombreGuardado = 'Vergüenza'; break;
                            case 5: $emocionClaseGuardado = 'emotion-fear-post'; $emocionNombreGuardado = 'Miedo'; break;
                            case 6: $emocionClaseGuardado = 'emotion-disgust-post'; $emocionNombreGuardado = 'Asco'; break;
                        }                        
                        // Si es un post de reto, cambiar la clase y el nombre
                        if (isset($post['tipo_post']) && $post['tipo_post'] === 'reto') {
                          $emocionClase = 'challenge';
                          $emocionNombre = '🏆 Reto del Día';
                        }
                    }
                ?>
                    <article class="post-card <?php echo $emocionClaseGuardado; ?> mb-4">
                        <div class="post-header">
                            <div class="d-flex align-items-center">
                                <img src="<?= $fotoGuardado ?>" class="post-avatar" alt="Avatar">
                                <div class="post-info ms-3">
                                    <h6 class="post-author mb-1">
                                        <a href="visor.php?alies=<?= urlencode($p['nombre_usuario']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= htmlspecialchars($p['nombre_usuario']) ?>
                                        </a>
                                    </h6>
                                    <span class="post-time"><?= $p['fecha'] ?></span>
                                </div>
                            </div>
                            <div class="emotion-indicator">
                                <span><?php echo htmlspecialchars($emocionNombreGuardado); ?></span>
                            </div>
                        </div>
                       
                        <div class="post-content mt-3">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($p['contenido'])) ?></p>
                        </div>
                       
                        <div class="post-footer mt-3">
                            <button class="post-action like" data-post-id="<?= $p['id_post'] ?>">
                                <i class="far fa-heart"></i>
                                <span class="count"><?= $u->contarLikes($p['id_post']) ?></span>
                            </button>
                            <button class="post-action comment-toggle">
                                <i class="bi bi-chat"></i>
                                <span class="comment-count"><?= count($comentarios) ?></span>
                            </button>
                            <button class="post-action bookmark active" data-post-id="<?= $p['id_post'] ?>">
                                <i class="fas fa-bookmark" style="color: #ffa502;"></i>
                            </button>
                        </div>
                                                  
                        <!-- Sección de comentarios -->
                                   <div class="comments-section">
                        <div class="existing-comments">
                            <?php foreach ($comentarios as $com): 
                                $fotoCom = !empty($com['foto_perfil']) ? "uploads/" . $com['foto_perfil'] : "../src/logo.png";
                            ?>
                                <div class="comment-item">
                                    <img src="<?= $fotoCom ?>" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-author">@<?= htmlspecialchars($com['nombre_usuario']) ?></div>
                                        <div class="comment-text"><?= htmlspecialchars($com['contenido']) ?></div>
                                        <div class="comment-time"><?= date("H:i", strtotime($com['fecha'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="comment-input-container">
                            <img src="<?php echo $foto; ?>" class="comment-avatar">
                            <input type="text" class="comment-input" placeholder="Escribe un comentario...">
                            <button class="comment-submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="glass-card p-5 text-center">
                    <i class="bi bi-bookmark" style="font-size: 4rem; color: var(--primary-color); opacity: 0.5;"></i>
                    <h5 class="mt-3">Tus posts guardados</h5>
                    <p class="text-muted">Los posts que guardes aparecerán en esta sección</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Pestaña: Amigos -->
    <div class="tab-pane fade" id="friends" role="tabpanel">
    <div class="row g-3">
        <?php if (!empty($listaAmigos)): ?>
            <?php foreach ($listaAmigos as $amigo):
                $fotoAmigo = !empty($amigo['foto_perfil']) ? "uploads/".$amigo['foto_perfil'] : "../src/lila.png";
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center">
                            <img src="<?= $fotoAmigo ?>" class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #a29bfe;">
                            <div style="line-height: 1.2;">
                                <h6 class="mb-0 text-purple" style="font-size: 0.95rem;"><?= htmlspecialchars($amigo['nombre']) ?></h6>
                                <small style="color: #a29bfe;">@<?= htmlspecialchars($amigo['nombre_usuario']) ?></small>
                            </div>
                        </div>
                        <a href="profile.php?alies=<?= urlencode($amigo['nombre_usuario']) ?>" class="btn btn-sm btn-primary-gradient rounded-pill px-3" style="font-size: 0.75rem;">
                            Ver
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-people fs-1 opacity-25"></i>
                <p class="mt-3 text-muted">Este usuario aún no sigue a nadie.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
 
  <div class="tab-pane fade" id="likes" role="tabpanel">
    <div class="posts-container">
      <?php if (!empty($postsLikes)): ?>
        <?php foreach ($postsLikes as $p): ?>
          <article class="post-card glass-card mb-4 p-3">
            <div class="post-header d-flex align-items-center">
              <img src="<?= !empty($p['foto_perfil']) ? 'uploads/'.$p['foto_perfil'] : '../src/logo.png' ?>" class="post-avatar" style="width: 45px; height: 45px; border-radius: 50%;">
              <div class="post-info ms-3">
                <h6 class="post-author mb-0"><?= htmlspecialchars($p['nombre_usuario']) ?></h6>
                <small class="text-muted"><?= $p['fecha'] ?></small>
              </div>
            </div>
            <div class="post-content mt-3">
              <p><?= nl2br(htmlspecialchars($p['contenido'])) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-5">
          <i class="bi bi-heartbreak fs-1 opacity-50"></i>
          <p class="text-muted mt-2">No has dado like a ningún post todavía.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <div class="tab-pane fade" id="saved" role="tabpanel">
    <div class="posts-container">
      <?php if (!empty($postsGuardados)): ?>
        <?php foreach ($postsGuardados as $p): ?>
          <article class="post-card glass-card mb-4 p-3">
            <div class="post-header d-flex align-items-center">
              <img src="<?= !empty($p['foto_perfil']) ? 'uploads/'.$p['foto_perfil'] : '../src/logo.png' ?>" class="post-avatar" style="width: 45px; height: 45px; border-radius: 50%;">
              <div class="post-info ms-3">
                <h6 class="post-author mb-0"><?= htmlspecialchars($p['nombre_usuario']) ?></h6>
                <small class="text-muted"><?= $p['fecha'] ?></small>
              </div>
            </div>
            <div class="post-content mt-3">
              <p><?= nl2br(htmlspecialchars($p['contenido'])) ?></p>
            </div>
            <div class="text-end">
              <i class="fas fa-bookmark text-primary"></i>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-5">
          <i class="bi bi-bookmark-x fs-1 opacity-50"></i>
          <p class="text-muted mt-2">No tienes posts guardados.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
          <div class="tab-pane fade" id="friends" role="tabpanel">...</div>
        </div>
      </div>
    </div>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sistema de comentarios funcional
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle comentarios
      const commentToggles = document.querySelectorAll('.comment-toggle');
     
      commentToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
          const post = this.closest('.post-card');
          const commentsSection = post.querySelector('.comments-section');
          commentsSection.classList.toggle('active');
        });
      });
     
      // Enviar comentario
      const commentSubmits = document.querySelectorAll('.comment-submit');
     
      commentSubmits.forEach(submit => {
        submit.addEventListener('click', function() {
          const post = this.closest('.post-card');
          const input = post.querySelector('.comment-input');
          const commentText = input.value.trim();
         
          if (commentText) {
            addComment(post, commentText);
            input.value = '';
          }
        });
      });
     
      // Permitir enviar con Enter
      const commentInputs = document.querySelectorAll('.comment-input');
     
      commentInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            const submit = this.parentElement.querySelector('.comment-submit');
            submit.click();
          }
        });
      });
     
 
       
        existingComments.appendChild(newComment);
       
        const currentCount = parseInt(commentCount.textContent);
        commentCount.textContent = currentCount + 1;
       
        newComment.style.opacity = '0';
        newComment.style.transform = 'translateY(10px)';
        setTimeout(() => {
          newComment.style.transition = 'all 0.3s ease';
          newComment.style.opacity = '1';
          newComment.style.transform = 'translateY(0)';
        }, 10);
      }
     
   
 // === SISTEMA DE INTERACCIONES (LIKE Y GUARDAR) ===
document.querySelectorAll('.post-action.like, .post-action.bookmark').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        if (!postId) return; // Evita errores si no hay ID


        const isLike = this.classList.contains('like');
        const action = isLike ? 'like' : 'save';
        const icon = this.querySelector('i');
       
        // Enviamos la petición al archivo PHP
        fetch('../negocio/gestionar_interacciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_post=${postId}&accion=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Solo si el servidor responde OK, cambiamos la interfaz
                this.classList.toggle('active');
               
                if (isLike) {
                    const countSpan = this.querySelector('.count');
                    let currentCount = parseInt(countSpan.textContent);
                    if (this.classList.contains('active')) {
                        icon.className = 'fas fa-heart text-danger';
                        countSpan.textContent = currentCount + 1;
                    } else {
                        icon.className = 'far fa-heart';
                        countSpan.textContent = currentCount - 1;
                    }
                } else {
                    icon.className = this.classList.contains('active') ? 'fas fa-bookmark text-primary' : 'far fa-bookmark';
                }
            } else {
                alert("Error al procesar la interacción: " + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
    });
  </script>
</body>
</html>

