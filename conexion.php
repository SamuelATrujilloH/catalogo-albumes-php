<?php
// conexion.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "ASUSALEJANDRO"; // o: "NOMBRE_DEL_SERVIDOR\\INSTANCIA"

$connectionInfo = [
    "Database" => "discografica3",
    "CharacterSet" => "UTF-8",
    "ConnectionPooling" => 0, // opcional, evita cache de conexión
    "TrustServerCertificate" => 1 // opcional si usas localhost
];

// ✅ Autenticación de Windows
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("<pre>Error de conexión:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}
?>

