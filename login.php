<?php
session_start();
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?";
    $stmt = sqlsrv_query($conn, $sql, [$usuario]);

    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $passwordDB = unpack("H*", $row['password'])[1]; // hex string
        $passwordInput = hash("sha256", $password);

        if ($passwordDB === $passwordInput) {
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];
            header("Location: ver_catalogo.php");
            exit;
        }
    }
    $error = "Usuario o contraseña incorrectos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión</title>

<style>
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

    .error {
        background: #5a1010;
        color: #ffb3b3;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        border: 1px solid #8a1c1c;
    }
</style>
</head>

<body>

<div class="login-container">

    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Usuario:</label>
            <input type="text" name="usuario" required>
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <<button type="submit">Iniciar Sesión</button>
        
        <div style="margin-top: 20px; font-size: 14px;">
            ¿No tienes cuenta? <a href="registro.php" style="color: #00ff99; text-decoration: none;">Regístrate aquí</a>
        </div>
    </form>

</div>
    </form>

</div>

</body>
</html>