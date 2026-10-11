<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }
if (($_SESSION['rol'] ?? '') !== 'admin') { die("❌ No tienes permisos"); }

$id_album = intval($_POST['id_album']);
$titulo = $_POST['titulo'] ?? '';
$artista_principal = $_POST['artista_principal'] ?? '';
$fecha = $_POST['fecha_lanzamiento'] ?: null;
$num_pistas = intval($_POST['num_pistas'] ?? 0);
$id_genero = $_POST['id_genero'] ?: null;
$id_formato = $_POST['id_formato'] ?: null;
$id_idioma = $_POST['id_idioma'] ?: null;
$id_sello = $_POST['id_sello'] ?: null;

$sql = "UPDATE albumes SET titulo=?, artista_principal=?, fecha_lanzamiento=?, num_pistas=?, id_genero=?, id_formato=?, id_idioma=?, id_sello=? WHERE id_album=?";
$params = [$titulo, $artista_principal, $fecha, $num_pistas, $id_genero, $id_formato, $id_idioma, $id_sello, $id_album];
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) { echo "<pre>"; print_r(sqlsrv_errors()); echo "</pre>"; exit; }
header("Location: ver_catalogo.php");
exit;
