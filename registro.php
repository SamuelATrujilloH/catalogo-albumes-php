<?php
// registro.php
session_start();
require "conexion.php";

$mensaje = "";
$tipo_mensaje = ""; // 'error' o 'exito'

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validaciones básicas
    if (empty($usuario) || empty($password)) {
        $mensaje = "Todos los campos son obligatorios";
        $tipo_mensaje = "error";
    } else {
        // 1. Verificar si el usuario ya existe
        $checkSql = "SELECT count(*) as conteo FROM usuarios WHERE usuario = ?";
        $checkStmt = sqlsrv_query($conn, $checkSql, [$usuario]);
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

        if ($row['conteo'] > 0) {
            $mensaje = "El nombre de usuario ya existe.";
            $tipo_mensaje = "error";
        } else {
            // 2. Insertar nuevo usuario
            // IMPORTANTE: Usamos HASHBYTES en SQL para que coincida con tu sistema de login
            // Forzamos el rol 'usuario' por seguridad
            // Usamos CAST(? AS VARCHAR(8000)) para asegurar que se encripte igual que en el login
                $insertSql = "INSERT INTO usuarios (usuario, password, rol) 
              VALUES (?, HASHBYTES('SHA2_256', CAST(? AS VARCHAR(8000))), 'usuario')";
            
            $insertStmt = sqlsrv_query($conn, $insertSql, [$usuario, $password]);

            if ($insertStmt) {
                $mensaje = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al registrar usuario.";
                $tipo_mensaje = "error";
                // Descomentar para depurar si falla:
                // print_r(sqlsrv_errors());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrarse</title>
<style>
    /* Mismos estilos que login.php para mantener consistencia */
    body {
        background: #0d0d0d;
        font-family: 'Inter', sans-serif;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background: #1c1c1c;
        padding: 40px;
        width: 380px;
        border-radius: 12px;
        box-shadow: 0 0 25px rgba(0,0,0,0.7);
        border: 1px solid #333;
        text-align: center;
    }

    h1 {
        color: #00ff99;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
        border-bottom: 2px solid #00ff99;
        padding-bottom: 10px;
    }

    .form-group {
        text-align: left;
        margin-bottom: 18px;
    }

    label {
        font-weight: bold;
        color: #b3b3b3;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        background: #2a2a2a;
        border: 1px solid #444;
        border-radius: 6px;
        color: white;
        font-size: 15px;
        box-sizing: border-box; /* Para que el padding no rompa el ancho */
    }

    input:focus {
        border-color: #00ff99;
        outline: none;
        box-shadow: 0 0 8px rgba(0,255,153,0.6);
    }

    button {
        width: 100%;
        background: #00ff99;
        color: #121212;
        border: none;
        padding: 14px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 15px;
        transition: 0.3s;
    }

    button:hover {
        background: #00e68c;
        transform: translateY(-2px);
        box-shadow: 0 5px 13px rgba(0,255,153,0.4);
    }

    .alert {
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        border: 1px solid transparent;
    }
    .alert.error { background: #5a1010; color: #ffb3b3; border-color: #8a1c1c; }
    .alert.exito { background: #105a20; color: #b3ffc6; border-color: #1c8a30; }

    .link-login {
        display: block;
        margin-top: 20px;
        color: #00ff99;
        text-decoration: none;
        font-size: 14px;
    }
    .link-login:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="login-container">
    <h1>Crear Cuenta</h1>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($tipo_mensaje !== 'exito'): ?>
    <form method="POST">
        <div class="form-group">
            <label>Nuevo Usuario:</label>
            <input type="text" name="usuario" required autocomplete="off">
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Registrarse</button>
    </form>
    <?php endif; ?>

    <a href="login.php" class="link-login">¿Ya tienes cuenta? Inicia sesión aquí</a>
</div>

</body>
</html>