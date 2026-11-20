<?php
session_start();
date_default_timezone_set('America/Monterrey');

$servicios = ['Corte de cabello', 'Arreglo de barba', 'Corte y barba'];
$horarios = [];

for ($hora = 9; $hora < 18; $hora++) {
    $horarios[] = sprintf('%02d:00', $hora);
    $horarios[] = sprintf('%02d:30', $hora);
}

$_SESSION['token_cita'] ??= bin2hex(random_bytes(32));

$cliente = '';
$servicio = '';
$fecha = '';
$horaCita = '';
$error = '';
$confirmacion = $_SESSION['confirmacion_cita'] ?? '';
unset($_SESSION['confirmacion_cita']);

function campo(string $nombre): string
{
    $valor = $_POST[$nombre] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = campo('cliente');
    $servicio = campo('servicio');
    $fecha = campo('fecha');
    $horaCita = campo('hora');

    $inicio = DateTimeImmutable::createFromFormat('!Y-m-d H:i', "$fecha $horaCita");

    if (!hash_equals($_SESSION['token_cita'], campo('token'))) {
        $error = 'Recarga la página e intenta de nuevo.';
    } elseif ($cliente === '' || mb_strlen($cliente, 'UTF-8') > 100) {
        $error = 'Escribe un nombre de entre 1 y 100 caracteres.';
    } elseif (!in_array($servicio, $servicios, true)) {
        $error = 'Selecciona un servicio de la lista.';
    } elseif (!in_array($horaCita, $horarios, true)) {
        $error = 'Selecciona un horario de la lista.';
    } elseif (!$inicio || $inicio->format('Y-m-d H:i') !== "$fecha $horaCita"
        || $inicio <= new DateTimeImmutable()) {
        $error = 'Elige una fecha y hora futuras.';
    } else {
        try {
            require __DIR__ . '/conexion.php';

            $consulta = $conexion->prepare(
                'INSERT INTO citas (cliente, servicio, fecha, hora) VALUES (?, ?, ?, ?)'
            );
            $consulta->execute([$cliente, $servicio, $fecha, $horaCita]);

            $_SESSION['confirmacion_cita'] = "Cita registrada para $cliente el "
                . $inicio->format('d/m/Y') . " a las $horaCita.";

            header('Location: registrar.php', true, 303);
            exit;
        } catch (PDOException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                $error = 'Ese horario ya está ocupado. Elige otro.';
            } else {
                error_log($e->getMessage());
                $error = 'No se pudo guardar la cita. Revisa la conexión con MySQL.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar cita | Barbería</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <?php require __DIR__ . '/menu.php'; ?>
    <main>
        <h1>Registrar cita en la barbería</h1>

        <?php if ($confirmacion !== ''): ?>
            <p role="status"><?= escapar($confirmacion) ?></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p role="alert"><?= escapar($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="token" value="<?= escapar($_SESSION['token_cita']) ?>">

            <p>
                <label for="cliente">Nombre del cliente</label><br>
                <input id="cliente" name="cliente" maxlength="100"
                       value="<?= escapar($cliente) ?>" required>
            </p>

            <p>
                <label for="servicio">Servicio</label><br>
                <select id="servicio" name="servicio" required>
                    <option value="">Selecciona un servicio</option>
                    <?php foreach ($servicios as $opcion): ?>
                        <option value="<?= escapar($opcion) ?>"
                            <?= $opcion === $servicio ? 'selected' : '' ?>>
                            <?= escapar($opcion) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="fecha">Fecha de la cita</label><br>
                <input type="date" id="fecha" name="fecha" min="<?= date('Y-m-d') ?>"
                       value="<?= escapar($fecha) ?>" required>
            </p>

            <p>
                <label for="hora">Hora de la cita</label><br>
                <select id="hora" name="hora" required>
                    <option value="">Selecciona una hora</option>
                    <?php foreach ($horarios as $opcion): ?>
                        <option value="<?= $opcion ?>" <?= $opcion === $horaCita ? 'selected' : '' ?>>
                            <?= $opcion ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <button type="submit">Guardar cita</button>
        </form>
    </main>
</body>
</html>