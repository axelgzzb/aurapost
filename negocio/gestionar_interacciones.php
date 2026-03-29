<?php
session_start();
require_once __DIR__ . "/Usuaris.php";

if (!isset($_SESSION["usuario"]) || !isset($_POST['id_post'])) {
    echo json_encode(['success' => false]);
    exit;
}

$u = new Usuaris();
$id_usuario = $_SESSION['usuario']['id_usuario'];
$id_post = $_POST['id_post'];
$accion = $_POST['accion'];

$resultado = false;

if ($accion === 'like') {
    $resultado = $u->toggleLike($id_usuario, $id_post);
} elseif ($accion === 'save') {
    $resultado = $u->toggleGuardar($id_usuario, $id_post);
} else if ($accion === 'comment') {
    $texto = $_POST['texto'] ?? '';
    if (!empty($texto)) {
        // Ejecutamos la función y guardamos el resultado
        $resultado = $u->insertarComentario($id_usuario, $id_post, $texto);
    }}


echo json_encode(['success' => $resultado]);