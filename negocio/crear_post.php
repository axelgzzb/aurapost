<?php
session_start();
require_once __DIR__ . "/../negocio/Usuaris.php";

// 1. Verificación de seguridad
if (!isset($_SESSION["usuario"])) {
    header("Location: ../presentacion/inici.php?error=sesion");
    exit;
}

// 2. Verificar que recibimos datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener y limpiar datos
    $contenido = trim($_POST['contenido'] ?? '');
    $id_emocion = intval($_POST['id_emocion'] ?? 0);
    $id_usuario = $_SESSION['usuario']['id_usuario'];

    // 3. Validaciones básicas
    if (empty($contenido)) {
        header("Location: ../presentacion/inici.php?error=vacio");
        exit;
    }

    if ($id_emocion < 1 || $id_emocion > 6) {
        header("Location: ../presentacion/inici.php?error=emocion");
        exit;
    }

    $u = new Usuaris();

    try {
        // 4. Intentar guardar en la base de datos
        // Asegúrate de que el método se llame 'crearPostConEmocion' o 'crearPost'
        $resultado = $u->crearPostConEmocion($id_usuario, $contenido, $id_emocion);

        if ($resultado) {
            // Éxito: Volvemos al inicio
            header("Location: ../presentacion/inici.php?success=post_creado");
            exit;
        } else {
            // Error de base de datos
            header("Location: ../presentacion/inici.php?error=db");
            exit;
        }

    } catch (Exception $e) {
        // Error crítico
        error_log("Error en crear_post.php: " . $e->getMessage());
        header("Location: ../presentacion/inici.php?error=servidor");
        exit;
    }
} else {
    // Si alguien intenta entrar directamente al PHP sin POST
    header("Location: ../presentacion/inici.php");
    exit;
}
?>