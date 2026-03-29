<?php
session_start();
require_once "../negocio/Usuaris.php";
require_once "../dades/Database.php"; // Asegúrate de que la ruta es correcta

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["alies"])) {
   header("Location: inici.php");
   exit;
}

$alias = $_GET["alies"];
$u = new Usuaris();
$usuario = $u->getUsuarioPorAlias($alias);

if (!$usuario) {
   echo "<h2>Usuario no encontrado</h2>";
   exit;
}

// Datos del usuario que estamos visitando
$id_usuario_perfil = $usuario["id_usuario"];
$id_usuario_sesion = $_SESSION['usuario']['id_usuario'];

$fotoPerfilVisitado = !empty($usuario["avatar"]) ? "../uploads/" . $usuario["avatar"] : "../src/logo.png";
$nombre = $usuario["nombre"] ?? $alias;
$user = $usuario["nombre_usuario"] ?? $alias;
$pronombres = $usuario["pronombres"] ?? null;
$seguidores = $u->contarSeguidores($id_usuario_perfil);
$totalPosts = $u->contarPostsUsuario($id_usuario_perfil);

// Datos del usuario en sesión (para pintar tus comentarios nuevos)
$datosSesion = $u->obtenerDatosUsuario($id_usuario_sesion);
$fotoSesion = !empty($datosSesion["foto_perfil"]) ? "uploads/" . $datosSesion["foto_perfil"] : "../src/logo.png";

// Posts del usuario
$posts = $u->getPostsUsuario($id_usuario_perfil);
?>

<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>AuraPost - <?= htmlspecialchars($alias) ?></title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
 <link href="../css/custom.css" rel="stylesheet">
 <link href="../css/mobile.css" rel="stylesheet">
</head>
<body>
 <div class="background-scene">
   <div class="water-waves"></div>
 </div>

  <nav class="navbar navbar-expand-lg navbar-light aura-nav fixed-top">
    <div class="container-fluid px-4">
      <a class="navbar-brand d-flex align-items-center" href="inici.php">
        <div class="logo-container"><img src="../src/logo.png" alt="AuraPost" style="width: 90%"></div>
        <span class="brand-text">AuraPost</span>
      </a>
      <div class="d-flex align-items-center gap-3">
        <a href="inici.php" class="nav-icon"><i class="fa-solid fa-house"></i></a>
        <a href="profile.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
      </div>
    </div>
  </nav>

 <main class="container-fluid main-container" style="padding-top: 5rem;">
   <div class="row g-4 justify-content-center">
    
    <div class="col-xl-3 col-lg-4">
      <div class="profile-card glass-card mb-4 text-center p-4">
        <img src="<?= $fotoPerfilVisitado ?>" class="profile-avatar mb-3" style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
        <h5 class="profile-name"><?= htmlspecialchars($nombre) ?></h5>
        <p class="badge bg-primary">@<?= htmlspecialchars($user) ?></p>

         <div class="stats-row d-flex justify-content-around my-3">
           <div><strong><?= $seguidores ?></strong><br><small>Seguidores</small></div>
           <div><strong><?= $totalPosts ?></strong><br><small>Posts</small></div>
         </div>

                <div class="action-buttons">
                    <?php if ($id_usuario_perfil != $id_usuario_sesion): 
                        $yaLoSigo = $u->esSeguidor($id_usuario_sesion, $id_usuario_perfil);
                    ?>
                        <form action="../negocio/gestionar_seguimiento.php" method="POST">
                            <input type="hidden" name="id_seguido" value="<?= $id_usuario_perfil ?>">
                            <input type="hidden" name="alias" value="<?= htmlspecialchars($alias) ?>">
                            
                            <button type="submit" name="accion" value="<?= $yaLoSigo ? 'unfollow' : 'seguir' ?>" class="btn <?= $yaLoSigo ? 'btn-outline-danger' : 'btn-primary-gradient' ?> w-100">
                                <?= $yaLoSigo ? 'Dejar de seguir' : 'Seguir' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                    </div>
    </div>

     <div class="col-xl-6 col-lg-8">
       <div class="posts-feed-container">
         <?php if (!empty($posts)): ?>
           <?php foreach ($posts as $post): 
                $pid = $post['id_post'];
                // Lógica de colores por emoción
                $emocionClase = 'emotion-happy-post'; 
                switch($post['id_emocion'] ?? 1) {
                    case 2: $emocionClase = 'emotion-sad-post'; break;
                    case 3: $emocionClase = 'emotion-angry-post'; break;
                    case 4: $emocionClase = 'emotion-shame-post'; break;
                    case 5: $emocionClase = 'emotion-fear-post'; break;
                    case 6: $emocionClase = 'emotion-disgust-post'; break;
                }

                // Verificaciones de interacción
                $tieneLike = $u->yaDioLike($pid, $id_usuario_sesion);
                $estaGuardado = $u->yaEstaGuardado($pid, $id_usuario_sesion); // Asegúrate de tener este método en Usuaris.php
                $comentarios = $u->obtenerComentariosPost($pid);
           ?>
             <div class="post-item mb-4">
               <article class="post-card <?= $emocionClase ?>">
                 <div class="post-header d-flex align-items-center justify-content-between">
                   <div class="d-flex align-items-center">
                        <img src="<?= $fotoPerfilVisitado ?>" class="post-avatar">
                        <div class="ms-3">
                            <h6 class="mb-0"><?= htmlspecialchars($user) ?></h6>
                            <small><?= $post["fecha"] ?></small>
                        </div>
                   </div>
                 </div>
                 
                 <div class="post-content mt-3">
                   <p><?= nl2br(htmlspecialchars($post["contenido"])) ?></p>
                 </div>

                 <div class="post-footer mt-3">
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
                            $fotoC = !empty($com['foto_perfil']) ? "uploads/" . $com['foto_perfil'] : "../src/logo.png";
                        ?>
                            <div class="comment-item">
                                <img src="<?= $fotoC ?>" class="comment-avatar">
                                <div class="comment-content">
                                    <div class="comment-author">@<?= htmlspecialchars($com['nombre_usuario']) ?></div>
                                    <div class="comment-text"><?= htmlspecialchars($com['contenido']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="comment-input-container">
                        <input type="text" class="comment-input" placeholder="Escribe un comentario...">
                        <button class="comment-submit"><i class="bi bi-send-fill"></i></button>
                    </div>
                 </div>
               </article>
             </div>
           <?php endforeach; ?>
         <?php else: ?>
            <div class="glass-card p-5 text-center"><h5>No hay posts todavía</h5></div>
         <?php endif; ?>
       </div>
     </div>
   </div>
 </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle comentarios
    document.querySelectorAll('.comment-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.post-card').querySelector('.comments-section').classList.toggle('active');
        });
    });

    // Enviar comentario
    document.querySelectorAll('.comment-submit').forEach(btn => {
        btn.addEventListener('click', function() {
            const post = this.closest('.post-card');
            const input = post.querySelector('.comment-input');
            const text = input.value.trim();
            const postId = post.querySelector('.like').getAttribute('data-post-id');

            if(!text) return;

            fetch('../negocio/guardar_comentario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_post=${postId}&texto=${encodeURIComponent(text)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const container = post.querySelector('.existing-comments');
                    container.innerHTML += `
                        <div class="comment-item">
                            <img src="<?= $fotoSesion ?>" class="comment-avatar">
                            <div class="comment-content">
                                <div class="comment-author">@<?= $_SESSION['usuario']['nombre_usuario'] ?></div>
                                <div class="comment-text">${text}</div>
                            </div>
                        </div>`;
                    input.value = '';
                    const count = post.querySelector('.comment-count');
                    count.textContent = parseInt(count.textContent) + 1;
                }
            });
        });
    });

    // Likes y Bookmarks
    document.querySelectorAll('.like, .bookmark').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const accion = this.classList.contains('like') ? 'like' : 'save';
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.count');

            fetch('../negocio/gestionar_interacciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_post=${postId}&accion=${accion}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.classList.toggle('active');
                    if(accion === 'like') {
                        let val = parseInt(countSpan.textContent);
                        if(this.classList.contains('active')) {
                            icon.className = 'fas fa-heart';
                            icon.style.color = '#ff4757';
                            countSpan.textContent = val + 1;
                        } else {
                            icon.className = 'far fa-heart';
                            icon.style.color = '';
                            countSpan.textContent = val - 1;
                        }
                    } else {
                        icon.className = this.classList.contains('active') ? 'fas fa-bookmark' : 'far fa-bookmark';
                        icon.style.color = this.classList.contains('active') ? '#ffa502' : '';
                    }
                }
            });
        });
    });
});
</script>
</body>
</html>