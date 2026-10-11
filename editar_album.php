<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }
if (($_SESSION['rol'] ?? '') !== 'admin') { die("❌ No tienes permisos"); }

$id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM albumes WHERE id_album = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
if ($stmt === false || !sqlsrv_has_rows($stmt)) { die("Álbum no encontrado"); }
$album = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

/* listas para selects */
$generos = sqlsrv_query($conn, "SELECT id_genero, nombre FROM generos ORDER BY nombre");
$formatos = sqlsrv_query($conn, "SELECT id_formato, nombre FROM formatos ORDER BY nombre");
$idiomas = sqlsrv_query($conn, "SELECT id_idioma, nombre FROM idiomas ORDER BY nombre");
$sellos = sqlsrv_query($conn, "SELECT id_sello, nombre FROM sellos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar Álbum</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h1>Editar Álbum</h1>
    <div class="nav-link"><a href="ver_catalogo.php">Volver</a></div>

    <form action="actualizar_album.php" method="post">
        <input type="hidden" name="id_album" value="<?= htmlspecialchars($album['id_album']) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" required value="<?= htmlspecialchars($album['titulo'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Artista Principal:</label>
                <input type="text" name="artista_principal" required value="<?= htmlspecialchars($album['artista_principal'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fecha lanzamiento:</label>
                <input type="date" name="fecha_lanzamiento" value="<?= $album['fecha_lanzamiento'] ? $album['fecha_lanzamiento']->format('Y-m-d') : '' ?>">
            </div>
            <div class="form-group">
                <label>Número de pistas:</label>
                <input type="number" name="num_pistas" min="1" value="<?= htmlspecialchars($album['num_pistas'] ?? 1) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Género:</label>
                <select name="id_genero">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($generos, SQLSRV_FETCH_ASSOC)) {
                        $sel = ($r['id_genero'] == $album['id_genero']) ? 'selected' : '';
                        echo "<option value=\"{$r['id_genero']}\" $sel>".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Formato:</label>
                <select name="id_formato">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($formatos, SQLSRV_FETCH_ASSOC)) {
                        $sel = ($r['id_formato'] == $album['id_formato']) ? 'selected' : '';
                        echo "<option value=\"{$r['id_formato']}\" $sel>".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Idioma:</label>
                <select name="id_idioma">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($idiomas, SQLSRV_FETCH_ASSOC)) {
                        $sel = ($r['id_idioma'] == $album['id_idioma']) ? 'selected' : '';
                        echo "<option value=\"{$r['id_idioma']}\" $sel>".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Sello:</label>
                <select name="id_sello">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($sellos, SQLSRV_FETCH_ASSOC)) {
                        $sel = ($r['id_sello'] == $album['id_sello']) ? 'selected' : '';
                        echo "<option value=\"{$r['id_sello']}\" $sel>".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn" type="submit">Actualizar Álbum</button>
        </div>
    </form>
</div>
</body>
</html>

