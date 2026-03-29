<?php
session_start();
require_once __DIR__ . "/Usuaris.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario'])) {
    $u = new Usuaris();
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $id_post = $_POST['id_post'];
    $texto = $_POST['texto'];

    // Usamos el método que creamos antes en Usuaris.php
    $resultado = $u->insertarComentario($id_usuario, $id_post, $texto);

    echo json_encode(['success' => $resultado]);
} else {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
}
?>