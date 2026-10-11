<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }
if (($_SESSION['rol'] ?? '') !== 'admin') { die("❌ No tienes permisos"); }

$id = intval($_GET['id'] ?? 0);
sqlsrv_query($conn, "DELETE FROM albumes WHERE id_album = ?", [$id]);
header("Location: ver_catalogo.php");
exit;
