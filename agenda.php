<?php
date_default_timezone_set('America/Monterrey');

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$dia = is_string($fecha) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $fecha)
    ? DateTimeImmutable::createFromFormat('!Y-m-d', $fecha)
    : false;

if (!$dia || $dia->format('Y-m-d') !== $fecha) {
    http_response_code(400);
    exit('Fecha inválida. Regresa y selecciona una fecha del calendario.');
}

$citas = [];
$error = '';

try {
    require __DIR__ . '/conexion.php';

    $consulta = $conexion->prepare(
        'SELECT hora, cliente, servicio
         FROM citas
         WHERE fecha = ?
         ORDER BY hora'
    );
    $consulta->execute([$fecha]);
    $citas = $consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    $error = 'No se pudo consultar la agenda. Revisa que MySQL esté iniciado.';
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda por fecha | Barbería</title>
</head>
<body>
    <main>
        <h1>Agenda de la barbería</h1>

        <form method="get">
            <label for="fecha">Fecha de la agenda</label>
            <input type="date" id="fecha" name="fecha"
                   value="<?= escapar($fecha) ?>" required>
            <button type="submit">Consultar</button>
        </form>

        <h2>Citas del <?= $dia->format('d/m/Y') ?></h2>

        <?php if ($error !== ''): ?>
            <p role="alert"><?= escapar($error) ?></p>
        <?php elseif (count($citas) === 0): ?>
            <p>No hay citas registradas para este día.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Hora</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Servicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= escapar(substr($cita['hora'], 0, 5)) ?></td>
                            <td><?= escapar($cita['cliente']) ?></td>
                            <td><?= escapar($cita['servicio']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="registrar.php">Registrar una cita</a></p>
    </main>
</body>
</html>