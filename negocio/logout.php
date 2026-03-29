<?php
session_start();

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión completamente
session_destroy();

// Redirige al login
header("Location: ../presentacion/index.html");
exit;
?>
