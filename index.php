<?php
$totalCitas = null;

try {
    require __DIR__ . '/conexion.php';
    $totalCitas = (int) $conexion
        ->query('SELECT COUNT(*) FROM citas')
        ->fetchColumn();
} catch (PDOException $error) {
    http_response_code(500);
    error_log($error->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio | Barbería</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <main>
        <h1>Agenda de la barbería</h1>

        <section class="resumen" aria-label="Resumen de citas">
            <?php if ($totalCitas !== null): ?>
                <strong class="total"><?= $totalCitas ?></strong>
                <p><?= $totalCitas === 1 ? 'Cita registrada' : 'Citas registradas' ?></p>
            <?php else: ?>
                <p role="alert">No se pudo consultar el total de citas.</p>
            <?php endif; ?>
        </section>

        <div class="opciones">
            <section class="tarjeta">
                <h2>Nueva cita</h2>
                <p>Elige el servicio, la fecha y la hora para reservar un turno.</p>
                <a class="boton" href="registrar.php">Registrar cita</a>
            </section>

            <section class="tarjeta">
                <h2>Agenda por día</h2>
                <p>Consulta los horarios reservados y el servicio de cada cliente.</p>
                <a class="boton" href="agenda.php">Consultar agenda</a>
            </section>
        </div>
    </main>
</body>
</html>