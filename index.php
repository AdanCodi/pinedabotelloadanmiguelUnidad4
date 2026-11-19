<?php

try {
    require __DIR__ . '/conexion.php';

    $total = (int) $conexion->query('SELECT COUNT(*) FROM citas')->fetchColumn();
    $mensaje = "Conexión correcta. Citas registradas: $total.";
} catch (PDOException $error) {
    http_response_code(500);
    error_log($error->getMessage());
    $mensaje = 'Error de conexión. Revisa MySQL y el nombre de la base de datos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de la barbería</title>
</head>
<body>
    <h1>Agenda de la barbería</h1>
    <p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>