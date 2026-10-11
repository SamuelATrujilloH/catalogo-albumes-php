<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }

/* filtros */
$conditions = [];
$params = [];

if (!empty($_GET['buscar'])) {
    $text = "%" . $_GET['buscar'] . "%";
    $conditions[] = "(a.titulo LIKE ? OR a.artista_principal LIKE ? OR s.nombre LIKE ?)";
    $params[] = $text; $params[] = $text; $params[] = $text;
}

if (!empty($_GET['f_artista'])) {
    $conditions[] = "a.artista_principal LIKE ?";
    $params[] = "%" . $_GET['f_artista'] . "%";
}

if (!empty($_GET['f_genero'])) {
    $conditions[] = "g.nombre = ?";
    $params[] = $_GET['f_genero'];
}

if (!empty($_GET['f_desde'])) {
    $conditions[] = "a.fecha_lanzamiento >= ?";
    $params[] = $_GET['f_desde'];
}

if (!empty($_GET['f_hasta'])) {
    $conditions[] = "a.fecha_lanzamiento <= ?";
    $params[] = $_GET['f_hasta'];
}

$where = "";
if (count($conditions) > 0) $where = "WHERE " . implode(" AND ", $conditions);

/* consulta: trae artistas relacionados como backup (si existen) */
$sql = "
SELECT 
    a.id_album,
    a.titulo,
    a.artista_principal,
    a.fecha_lanzamiento,
    a.num_pistas,
    g.nombre AS genero,
    f.nombre AS formato,
    i.nombre AS idioma,
    s.nombre AS sello,
    STUFF((
        SELECT ', ' + ar2.nombre
        FROM album_artista aa2
        JOIN artistas ar2 ON aa2.id_artista = ar2.id_artista
        WHERE aa2.id_album = a.id_album
        FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS artistas_rel
FROM albumes a
LEFT JOIN generos g ON a.id_genero = g.id_genero
LEFT JOIN formatos f ON a.id_formato = f.id_formato
LEFT JOIN idiomas i ON a.id_idioma = i.id_idioma
LEFT JOIN sellos s ON a.id_sello = s.id_sello
$where
ORDER BY a.titulo ASC, a.fecha_lanzamiento DESC
";

$stmt = sqlsrv_query($conn, $sql, $params);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Catálogo</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h1>Catálogo de Álbumes</h1>

    <div class="top-bar">
        <span class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? '') ?> (<?= htmlspecialchars($_SESSION['rol'] ?? '') ?>)</span>

        <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
            <a href="catalogo.php" class="btn-new">➕ Añadir Nuevo</a>
        <?php endif; ?>

        <a href="logout.php" class="btn-logout">⛔ Cerrar Sesión</a>
    </div>

    <!-- filtros -->
    <form method="GET" class="filtros">
        <div class="form-row">
            <div class="form-group">
                <label>Artista:</label>
                <input type="text" name="f_artista" value="<?= htmlspecialchars($_GET['f_artista'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Género:</label>
                <select name="f_genero">
                    <option value="">Todos</option>
                    <?php
                    $qg = sqlsrv_query($conn, "SELECT nombre FROM generos ORDER BY nombre");
                    while ($rg = sqlsrv_fetch_array($qg, SQLSRV_FETCH_ASSOC)) {
                        $sel = (isset($_GET['f_genero']) && $_GET['f_genero'] === $rg['nombre']) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($rg['nombre']) . "\" $sel>" . htmlspecialchars($rg['nombre']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fecha desde:</label>
                <input type="date" name="f_desde" value="<?= htmlspecialchars($_GET['f_desde'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Fecha hasta:</label>
                <input type="date" name="f_hasta" value="<?= htmlspecialchars($_GET['f_hasta'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Búsqueda general:</label>
            <input type="text" name="buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" placeholder="Título, artista o sello...">
        </div>

        <button class="btn">Buscar 🔍</button>
    </form>

    <?php if ($stmt && sqlsrv_has_rows($stmt)): ?>
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Título</th><th>Artistas</th><th>Lanzamiento</th><th>Género</th><th>Sello</th><th>Pistas</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <?php
                $artistDisplay = trim($row['artista_principal'] ?? '') !== '' ? $row['artista_principal'] : ($row['artistas_rel'] ?? '');
            ?>
            <tr>
                <td><?= htmlspecialchars($row['id_album'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['titulo'] ?? '') ?></td>
                <td><?= htmlspecialchars($artistDisplay ?? '') ?></td>
                <td><?= $row['fecha_lanzamiento'] ? $row['fecha_lanzamiento']->format('Y-m-d') : '' ?></td>
                <td><?= htmlspecialchars($row['genero'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['sello'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['num_pistas'] ?? '') ?></td>
                <td>
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <a class="btn-edit" href="editar_album.php?id=<?= htmlspecialchars($row['id_album']) ?>">✏️ Editar</a>
                        <a class="btn-delete" href="eliminar_album.php?id=<?= htmlspecialchars($row['id_album']) ?>"
                           onclick="return confirm('¿Eliminar este álbum?');">🗑️ Eliminar</a>
                    <?php else: ?>
                        <span style="color:#888; font-size:13px;">Sin acciones</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="no-data">No se encontraron resultados.</p>
    <?php endif; ?>

</div>
</body>
</html>
<?php sqlsrv_close($conn); ?>

