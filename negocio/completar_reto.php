<?php
session_start();
require_once __DIR__ . "/Usuaris.php";

// 1. Verificación de seguridad
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "Debes iniciar sesión";
    $_SESSION["volver_a"] = "daily.php";
    header("Location: ../presentacion/error.php");
    exit;
}

// 2. Verificar que recibimos datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../presentacion/daily.php");
    exit;
}

// Obtener y limpiar datos
$contenido = trim($_POST['contenido'] ?? '');
$id_emocion = intval($_POST['id_emocion'] ?? 1);
$id_usuario = $_SESSION['usuario']['id_usuario'];

// 3. Validaciones básicas
if (empty($contenido)) {
    header("Location: ../presentacion/daily.php?error=vacio");
    exit;
}

// Validar que la emoción esté en el rango correcto
if ($id_emocion < 1 || $id_emocion > 6) {
    $id_emocion = 1; // Forzar a emoción Feliz si es inválida
}

$u = new Usuaris();

try {
    // 4. Verificar si ya completó el reto hoy
    if ($u->verificarRetoCompletadoHoy($id_usuario)) {
        header("Location: ../presentacion/daily.php?error=ya_completado");
        exit;
    }

    // 5. Crear el post del reto diario (incluye dar recompensas)
    $resultado = $u->crearPostRetoDiario($id_usuario, $contenido, $id_emocion);

    if ($resultado) {
        // Éxito: Post creado y reto completado
        header("Location: ../presentacion/daily.php?success=reto_completado");
        exit;
    } else {
        // Error al crear el post
        header("Location: ../presentacion/daily.php?error=db");
        exit;
    }

} catch (Exception $e) {
    // Error crítico
    error_log("Error en completar_reto.php: " . $e->getMessage());
    header("Location: ../presentacion/daily.php?error=servidor");
    exit;
}
?>