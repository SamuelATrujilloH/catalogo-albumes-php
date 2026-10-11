<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }
if (($_SESSION['rol'] ?? '') !== 'admin') { die("<h2 style='color:red;'>❌ No tienes permisos</h2>"); }

/* Traer listas para selects */
$generos = sqlsrv_query($conn, "SELECT id_genero, nombre FROM generos ORDER BY nombre");
$formatos = sqlsrv_query($conn, "SELECT id_formato, nombre FROM formatos ORDER BY nombre");
$idiomas = sqlsrv_query($conn, "SELECT id_idioma, nombre FROM idiomas ORDER BY nombre");
$sellos = sqlsrv_query($conn, "SELECT id_sello, nombre FROM sellos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Añadir Álbum</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h1>Añadir Álbum</h1>
    <div class="nav-link"><a href="ver_catalogo.php">Volver al catálogo</a></div>

    <form action="procesar_album.php" method="post">
        <div class="form-row">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label>Artista Principal:</label>
                <!-- ahora un input de texto en lugar de multi-select -->
                <input type="text" name="artista_principal" required placeholder="Nombre del artista principal">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fecha lanzamiento:</label>
                <input type="date" name="fecha_lanzamiento">
            </div>

            <div class="form-group">
                <label>Número de pistas:</label>
                <input type="number" name="num_pistas" min="1" value="1">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Género:</label>
                <select name="id_genero">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($generos, SQLSRV_FETCH_ASSOC)) {
                        echo "<option value=\"{$r['id_genero']}\">".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Formato:</label>
                <select name="id_formato">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($formatos, SQLSRV_FETCH_ASSOC)) {
                        echo "<option value=\"{$r['id_formato']}\">".htmlspecialchars($r['nombre'])."</option>";
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
                        echo "<option value=\"{$r['id_idioma']}\">".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Sello:</label>
                <select name="id_sello">
                    <option value="">--</option>
                    <?php while($r = sqlsrv_fetch_array($sellos, SQLSRV_FETCH_ASSOC)) {
                        echo "<option value=\"{$r['id_sello']}\">".htmlspecialchars($r['nombre'])."</option>";
                    } ?>
                </select>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn" type="submit">Añadir Álbum</button>
        </div>
    </form>
</div>
</body>
</html>
