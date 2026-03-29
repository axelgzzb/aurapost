<?php
session_start();
require_once __DIR__ . "/Usuaris.php";

// Verificamos que el usuario esté logueado y que tengamos el ID del seguido
if (!isset($_SESSION["usuario"]) || !isset($_POST["id_seguido"])) {
    header("Location: ../presentacion/inici.php");
    exit;
}

$u = new Usuaris();
$id_seguidor = $_SESSION["usuario"]["id_usuario"];
$id_seguido = $_POST["id_seguido"];
// Capturamos el alias (si no viene, por defecto vamos a inici.php)
$alias = $_POST["alias"] ?? null;
$accion = $_POST["accion"] ?? "";

if ($accion === "seguir") {
    $u->seguirUsuario($id_seguidor, $id_seguido);
} elseif ($accion === "unfollow") {
    $u->dejarDeSeguir($id_seguidor, $id_seguido);
}

// Redirigir de vuelta al perfil o a inicio si no hay alias
if ($alias) {
    header("Location: ../presentacion/visor.php?alies=" . urlencode($alias));
} else {
    header("Location: ../presentacion/inici.php");
}
exit;