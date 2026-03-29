<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "Debes iniciar sesión para acceder";
    header("Location: error.php");
    exit;
}

$u = new Usuaris();
$usuarioActual = $_SESSION['usuario'];
$id_usuario = $usuarioActual['id_usuario'];

// Obtener datos dinámicos del usuario
$datosUsuario = $u->obtenerDatosUsuario($id_usuario);
$seguidores = $u->contarSeguidores($id_usuario);
$seguidos = $u->contarSeguidos($id_usuario);
$totalPosts = $u->contarPostsUsuario($id_usuario);


// Agafem posts dels usuaris seguits
$posts = $u->getPostsSeguidos($usuarioActual['id_usuario']);

$foto = !empty($datosUsuario["foto_perfil"]) ? "uploads/" . $datosUsuario["foto_perfil"] : "../src/logo.png";
$nombre = $datosUsuario["nombre"] ?? "Usuario";
$apellidos = $datosUsuario["apellidos"] ?? "";
$user = $datosUsuario["nombre_usuario"] ?? "";
$pronombres = $datosUsuario["pronombres"] ?? "";
$racha = $datosUsuario["racha"] ?? 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AuraPost</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="../css/accessibility.css" rel="stylesheet">
  <link href="../css/custom.css" rel="stylesheet">
  <link href="../css/mobile.css" rel="stylesheet">
  <style>
    body {
      margin-top: 0px;
    }
  </style>
</head>

<body>
  <!-- 🌊 Fondo animado premium -->
  <div class="background-scene">
    <div class="betta-fish betta1"></div>
    <div class="betta-fish betta2"></div>
    <div class="betta-fish betta3"></div>
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
        <a href="inici.php" class="nav-icon active" data-tooltip="Inicio"><i class="fa-solid fa-house"></i></a>
        <a href="notificaciones.php" class="nav-icon" data-tooltip="Notificaciones"><i class="bi bi-bell"></i></a>
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
  
  <!-- Contenido Principal -->
  <main class="container-fluid main-container" style="padding-top: 0.5rem;">
    <div class="row g-4 justify-content-center">
      
      <!-- Sidebar Perfil -->
    <div class="col-xl-3 col-lg-4">
      <div class="profile-card glass-card mb-4">
        <div class="profile-header">
          <img src="<?= $foto ?>" class="profile-avatar" alt="Avatar">
        </div>
        <h5 class="profile-name"><?= htmlspecialchars($nombre) ?></h5>
         <?php if ($pronombres): ?>
          <p class="profile-handle"><?= htmlspecialchars($pronombres) ?></p>
          <?php endif; ?>
        <p class="badge bg-primary">@<?= htmlspecialchars($user) ?></p>

          
          <div class="stats-row">
            <div class="stat-item">
               <span class="stat-number"><?php echo $seguidores; ?></span>
              <span class="stat-label">Seguidores</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                   <span class="stat-number"><?php echo $totalPosts; ?></span>
              <span class="stat-label">Posts</span>
            </div>
          </div>

          <div class="action-buttons">
            <button class="btn btn-primary-gradient w-100 mb-2" onclick="location.href='daily.php'">
              <i class="bi bi-stars me-2"></i>Reto Diario
            </button>
            <button class="btn btn-outline-light w-100 mb-2" onclick="location.href='configuracion.php'">
              <i class="fa-solid fa-gear"></i>Editar perfil
            </button>
            <button type="button" class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#logoutModal">
              <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Log out
            </button>
          </div>
        </div>
      </div>

      <!-- 🔵 Feed Central -->
      <div class="col-xl-6 col-lg-8">
        <!-- Crear Post -->  

 <form action="../negocio/crear_post.php" method="POST" class="glass-card create-post p-4">
    <div class="d-flex gap-3 align-items-start">
        <img src="<?php echo $foto; ?>" class="create-avatar" alt="Avatar">
        <div class="flex-grow-1">
            <textarea name="contenido" class="form-control create-input" placeholder="¿Qué quieres compartir?" rows="3" required></textarea>
            
            <input type="hidden" name="id_emocion" id="id_emocion_input" value="">

            <div class="create-actions mt-3">
                <div class="emotion-selector">
                    <button type="button" class="emotion-btn" data-id="1">
                        <i class="fa-solid fa-face-laugh-beam" style="color: #dcb31e;"></i>
                    </button>
                    <button type="button" class="emotion-btn" data-id="2">
                        <i class="fa-solid fa-face-frown" style="color: #5294c7;"></i>
                    </button>
                    <button type="button" class="emotion-btn" data-id="3">
                        <i class="fa-solid fa-face-tired" style="color: #d31212;"></i>
                    </button>
                    <button type="button" class="emotion-btn" data-id="4">
                        <i class="fa-solid fa-face-flushed" style="color: #dc2eff;"></i>
                    </button>
                    <button type="button" class="emotion-btn" data-id="5">
                        <i class="fa-solid fa-ghost" style="color: #4000ff;"></i>
                    </button>
                    <button type="button" class="emotion-btn" data-id="6">
                        <i class="fa-solid fa-face-meh" style="color: #13622b;"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-primary-gradient publish-btn">
                    <i class="bi bi-send-fill me-2"></i>Publicar Aura
                </button>
            </div>
        </div>
    </div>
</form>


<div class="posts-feed-container">
    <?php if ($posts && count($posts) > 0): ?>
        <?php foreach ($posts as $post): 
            $fotoPost = !empty($post['foto_perfil']) ? "uploads/" . $post['foto_perfil'] : "../src/logo.png";
            $pid = $post['id_post'];

            // Determinar la clase de emoción
            $emocionClase = 'emotion-happy-post'; 
            $emocionNombre = $post['emocion_nombre'] ?? 'Feliz';
            
            switch($post['id_emocion']) {
                case 1: $emocionClase = 'emotion-happy-post'; break;
                case 2: $emocionClase = 'emotion-sad-post'; break;
                case 3: $emocionClase = 'emotion-angry-post'; break;
                case 4: $emocionClase = 'emotion-shame-post'; break;
                case 5: $emocionClase = 'emotion-fear-post'; break;
                case 6: $emocionClase = 'emotion-disgust-post'; break;
            }
            if (isset($post['tipo_post']) && $post['tipo_post'] === 'reto') {
                $emocionClase = 'challenge';
                $emocionNombre = '🏆 Reto del Día';
            }
        ?>
            <div class="post-item mb-4">
                <article class="post-card <?php echo $emocionClase; ?>">
                    <div class="post-header">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $fotoPost; ?>" class="post-avatar" alt="Avatar">
                            <div class="post-info ms-3">
                                <h6 class="post-author mb-1">
                                    <a href="visor.php?alies=<?= urlencode($post['nombre_usuario']) ?>" style="color: inherit; text-decoration: none;">
                                        <?= htmlspecialchars($post['nombre_usuario']) ?>
                                    </a>
                                </h6>
                                <span class="post-time"><?= htmlspecialchars($post['fecha']) ?></span>
                            </div>
                        </div>
                        <div class="emotion-indicator">
                            <span><?= htmlspecialchars($emocionNombre) ?></span>
                        </div>
                    </div>
                    
                    <div class="post-content mt-3">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($post['contenido'])) ?></p>
                    </div>

                    <div class="post-footer mt-3">
                        <?php 
                            $db = new Database();
                            $conn = $db->getConnection();

                            // Comprobar LIKE
                            $sqlL = "SELECT 1 FROM GUSTA WHERE id_usuario = ? AND id_post = ?";
                            $stL = $conn->prepare($sqlL);
                            $stL->bind_param("ii", $id_usuario, $pid);
                            $stL->execute();
                            $tieneLike = $stL->get_result()->num_rows > 0;

                            // Comprobar GUARDADO
                            $sqlG = "SELECT 1 FROM GUARDA WHERE id_usuario = ? AND id_post = ?";
                            $stG = $conn->prepare($sqlG);
                            $stG->bind_param("ii", $id_usuario, $pid);
                            $stG->execute();
                            $estaGuardado = $stG->get_result()->num_rows > 0;

                            // Obtener comentarios
                            $comentarios = $u->obtenerComentariosPost($pid); 
                        ?>

                        <button class="post-action like <?= $tieneLike ? 'active' : '' ?>" data-post-id="<?= $pid ?>">
                            <i class="<?= $tieneLike ? 'fas' : 'far' ?> fa-heart" <?= $tieneLike ? 'style="color: #ff4757;"' : '' ?>></i>
                            <span class="count"><?= $u->contarLikes($pid) ?></span>
                        </button>

                        <button class="post-action comment-toggle">
                            <i class="bi bi-chat"></i>
                            <span class="comment-count"><?= count($comentarios) ?></span>
                        </button>

                        <button class="post-action bookmark <?= $estaGuardado ? 'active' : '' ?>" data-post-id="<?= $pid ?>">
                            <i class="<?= $estaGuardado ? 'fas' : 'far' ?> fa-bookmark" <?= $estaGuardado ? 'style="color: #ffa502;"' : '' ?>></i>
                        </button>
                    </div>

                    <div class="comments-section">
                        <div class="existing-comments">
                            <?php foreach ($comentarios as $com): 
                                $fotoCom = !empty($com['foto_perfil']) ? "uploads/" . $com['foto_perfil'] : "../src/logo.png";
                            ?>
                                <div class="comment-item">
                                    <img src="<?= $fotoCom ?>" class="comment-avatar" alt="Avatar">
                                    <div class="comment-content">
                                        <div class="comment-author">@<?= htmlspecialchars($com['nombre_usuario']) ?></div>
                                        <div class="comment-text"><?= htmlspecialchars($com['contenido']) ?></div>
                                        <div class="comment-time"><?= $com['fecha'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="comment-input-container">
                            <img src="<?php echo $foto; ?>" class="comment-avatar" alt="Tu avatar">
                            <input type="text" class="comment-input" placeholder="Escribe un comentario...">
                            <button class="comment-submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="glass-card p-5 text-center">
            <i class="bi bi-inbox" style="font-size: 4rem; color: var(--primary-color); opacity: 0.5;"></i>
            <h5 class="mt-3">No hay posts para mostrar</h5>
            <p class="text-muted">¡Sigue a usuarios para ver su contenido!</p>
        </div>
    <?php endif; ?>
</div>
</div>
      <!-- 🟡 Sidebar Derecho -->
      <div class="col-xl-3 col-lg-4">
        <!-- Racha -->
        <div class="streak-card glass-card mb-4">
          <div class="streak-icon">🔥</div>
          <span class="stat-number"><?php echo $racha; ?></span>
          <p class="streak-label">Días de racha</p>
           <p class="text-muted mb-2"><?php echo $datosUsuario['racha'] ?? 0; ?> días consecutivos activo</p>
                <div class="d-flex justify-content-center align-items-center gap-2">
                  <div class="progress" style="height: 10px; width: 150px;">
                    <div class="progress-bar bg-warning" style="width: <?php echo min(($datosUsuario['racha'] ?? 0) * 10, 100); ?>%"></div>
                  </div>
                  <span class="text-warning fw-bold"><?php echo $datosUsuario['racha'] ?? 0; ?>/10</span>
                </div>
        </div>

        <!-- Reto del día -->
        <div class="challenge-card glass-card mb-4">
          <div class="challenge-header">
            <i class="bi bi-trophy-fill"></i>
            <h6>Reto del Día</h6>
          </div>
          <a href="daily.php" class="btn btn-challenge w-100 mt-2">
            <i class="bi bi-pencil me-2"></i>Participar
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal de confirmación de Log out -->
  <div class="modal fade" id="logoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header border-0">
          <h1 class="modal-title fs-5 fw-bold text-dark" id="logoutModalLabel">
            ¿Cerrar sesión?
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-secondary">
          ¿Estás seguro de que quieres cerrar sesión? Perderás el acceso hasta volver a iniciar sesión.
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            Cancelar
          </button>
          <a href="../negocio/logout.php" class="btn btn-primary-gradient px-4">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Sí, cerrar sesión
          </a>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("PUNTO 1: Script cargado correctamente.");

    // === 1. LÓGICA DE EMOCIÓN Y ENVÍO DE POST ===
    const emocionButtons = document.querySelectorAll('.emotion-btn');
    const inputEmocion = document.getElementById('id_emocion_input');
    const textarea = document.querySelector('.create-input');
    const formPost = document.querySelector('form[action="crear_post.php"]');

    emocionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            emocionButtons.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            const idEmocion = this.getAttribute('data-id');
            if(inputEmocion) inputEmocion.value = idEmocion;

            const colors = {1:'#dcb31e', 2:'#5294c7', 3:'#d31212', 4:'#dc2eff', 5:'#4000ff', 6:'#13622b'};
            if(textarea) {
                textarea.style.borderColor = colors[idEmocion];
                textarea.style.boxShadow = `0 0 0 0.2rem ${colors[idEmocion]}33`;
            }
        });
    });

    if (formPost) {
        formPost.addEventListener('submit', function(e) {
            if (!inputEmocion.value) {
                e.preventDefault();
                alert("Por favor, selecciona una emoción para tu Aura.");
            }
        });
    }

    // === 2. SISTEMA DE COMENTARIOS ===
    document.querySelectorAll('.comment-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const post = this.closest('.post-card');
            post.querySelector('.comments-section').classList.toggle('active');
        });
    });

    document.querySelectorAll('.comment-submit').forEach(submit => {
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

    document.querySelectorAll('.comment-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const btn = this.parentElement.querySelector('.comment-submit');
                if(btn) btn.click();
            }
        });
    });
function addComment(postCard, text) {
    const postId = postCard.querySelector('.post-action.like').getAttribute('data-post-id');
    const existingComments = postCard.querySelector('.existing-comments');
    const commentCount = postCard.querySelector('.comment-count');

    // 1. Enviamos el comentario al servidor para guardarlo en la tabla COMENTARIO
    fetch('../negocio/guardar_comentario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_post=${postId}&texto=${encodeURIComponent(text)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 2. Si se guardó bien, lo pintamos en la pantalla
            const newComment = document.createElement('div');
            newComment.className = 'comment-item';
            // Eliminamos la barra invertida \ que tenías antes
            newComment.innerHTML = `
                <img src="<?php echo $foto; ?>" class="comment-avatar" alt="Avatar">
                <div class="comment-content">
                    <div class="comment-author"><?php echo htmlspecialchars($user); ?></div>
                    <div class="comment-text">${text}</div> 
                    <div class="comment-time">Justo ahora</div>
                </div>`;
            existingComments.appendChild(newComment);
            
            if(commentCount) {
                commentCount.textContent = parseInt(commentCount.textContent) + 1;
            }
        } else {
            alert("Error al guardar el comentario.");
        }
    })
    .catch(error => console.error('Error:', error));
}
    // === 3. SISTEMA DE INTERACCIONES (LIKE Y GUARDAR) ===
    const interaccionButtons = document.querySelectorAll('.post-action.like, .post-action.bookmark');
    console.log("PUNTO 2: Botones de interacción encontrados: " + interaccionButtons.length);

    interaccionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const postId = this.getAttribute('data-post-id');
            const isLike = this.classList.contains('like');
            const accion = isLike ? 'like' : 'save';
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.count');

            console.log("PUNTO 3: Enviando " + accion + " para ID: " + postId);

            fetch('../negocio/gestionar_interacciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_post=' + postId + '&accion=' + accion
        })
            .then(response => response.text())
            .then(text => {
                console.log("PUNTO 4: Respuesta servidor: ", text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        this.classList.toggle('active');
                        if (isLike && countSpan) {
                            let currentCount = parseInt(countSpan.textContent) || 0;
                            if (this.classList.contains('active')) {
                                icon.classList.replace('far', 'fas');
                                icon.style.color = '#ff4757';
                                countSpan.textContent = currentCount + 1;
                            } else {
                                icon.classList.replace('fas', 'far');
                                icon.style.color = '';
                                countSpan.textContent = currentCount - 1;
                            }
                        } else if (!isLike) {
                            icon.classList.toggle('fas');
                            icon.classList.toggle('far');
                            icon.style.color = this.classList.contains('active') ? '#ffa502' : '';
                        }
                    }
                } catch (e) {
                    console.error("Error al procesar JSON:", e);
                }
            })
            .catch(error => console.error('Error en fetch:', error));
        });
    });

}); // CIERRE ÚNICO DE DOMContentLoaded
</script>
</body>
</html>