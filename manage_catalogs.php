<?php
session_start();
require "conexion.php";
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; }
if ($_SESSION['rol'] !== 'admin') { die("❌ No tienes permisos"); }

$entity = $_GET['entity'] ?? 'artistas'; // artistas, generos, formatos, idiomas, sellos
$action = $_GET['action'] ?? ''; // add, edit, delete
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* Map entity -> table & id column */
$map = [
    'artistas' => ['table'=>'artistas','id'=>'id_artista','col'=>'nombre'],
    'generos' => ['table'=>'generos','id'=>'id_genero','col'=>'nombre'],
    'formatos' => ['table'=>'formatos','id'=>'id_formato','col'=>'nombre'],
    'idiomas' => ['table'=>'idiomas','id'=>'id_idioma','col'=>'nombre'],
    'sellos'  => ['table'=>'sellos','id'=>'id_sello','col'=>'nombre'],
];

if (!isset($map[$entity])) $entity = 'artistas';
$tbl = $map[$entity]['table'];
$idcol = $map[$entity]['id'];
$col = $map[$entity]['col'];

/* Procesar POST para add/edit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $edit_id = intval($_POST['edit_id'] ?? 0);

    if (!empty($name) && isset($_POST['save'])) {
        if ($edit_id > 0) {
            sqlsrv_query($conn, "UPDATE $tbl SET $col = ? WHERE $idcol = ?", [$name, $edit_id]);
        } else {
            sqlsrv_query($conn, "INSERT INTO $tbl ($col) VALUES (?)", [$name]);
        }
        header("Location: manage_catalogs.php?entity=$entity");
        exit;
    }
}

/* Delete */
if ($action === 'delete' && $id > 0) {
    sqlsrv_query($conn, "DELETE FROM $tbl WHERE $idcol = ?", [$id]);
    header("Location: manage_catalogs.php?entity=$entity");
    exit;
}

/* Cargar lista */
$listQ = sqlsrv_query($conn, "SELECT $idcol, $col FROM $tbl ORDER BY $col");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestionar Catálogos</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h1>Gestionar: <?= ucfirst($entity) ?></h1>
    <div class="top-bar">
        <div>
            <a href="ver_catalogo.php" class="btn-new">⬅ Volver al catálogo</a>
            <a href="manage_catalogs.php?entity=artistas" class="btn-new">Artistas</a>
            <a href="manage_catalogs.php?entity=generos" class="btn-new">Géneros</a>
            <a href="manage_catalogs.php?entity=formatos" class="btn-new">Formatos</a>
            <a href="manage_catalogs.php?entity=idiomas" class="btn-new">Idiomas</a>
            <a href="manage_catalogs.php?entity=sellos" class="btn-new">Sellos</a>
        </div>
        <a href="logout.php" class="btn-logout">⛔ Cerrar Sesión</a>
    </div>

    <h3>Añadir / Editar <?= ucfirst($entity) ?></h3>
    <?php
    $editName = '';
    $editId = 0;
    if ($action === 'edit' && $id > 0) {
        $qr = sqlsrv_query($conn, "SELECT $col FROM $tbl WHERE $idcol = ?", [$id]);
        if ($qr && $rr = sqlsrv_fetch_array($qr, SQLSRV_FETCH_ASSOC)) {
            $editName = $rr[$col];
            $editId = $id;
        }
    }
    ?>
    <form method="post" style="margin-bottom:20px;">
        <input type="hidden" name="edit_id" value="<?= $editId ?>">
        <input type="text" name="name" placeholder="Nombre" value="<?= htmlspecialchars($editName) ?>" required>
        <button class="btn" name="save" type="submit"><?= $editId ? 'Guardar cambios' : 'Añadir' ?></button>
    </form>

    <h3>Listado</h3>
    <table class="tabla">
        <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php while($r = sqlsrv_fetch_array($listQ, SQLSRV_FETCH_ASSOC)): ?>
            <tr>
                <td><?= $r[$idcol] ?></td>
                <td><?= htmlspecialchars($r[$col]) ?></td>
                <td>
                    <a class="btn-edit" href="manage_catalogs.php?entity=<?= $entity ?>&action=edit&id=<?= $r[$idcol] ?>">✏️</a>
                    <a class="btn-delete" href="manage_catalogs.php?entity=<?= $entity ?>&action=delete&id=<?= $r[$idcol] ?>"
                       onclick="return confirm('Eliminar <?= htmlspecialchars($r[$col]) ?>?');">🗑️</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>
</body>
</html>
