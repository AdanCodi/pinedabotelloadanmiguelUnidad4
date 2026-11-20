<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);

$paginas = [
    'index.php' => 'Inicio',
    'registrar.php' => 'Registrar cita',
    'agenda.php' => 'Consultar agenda',
];
?>

<header class="barra">
    <div class="barra-interior">
        <a class="marca" href="index.php">Barbería</a>

        <nav aria-label="Navegación principal">
            <?php foreach ($paginas as $ruta => $titulo): ?>
                <a href="<?= $ruta ?>"
                   <?= $paginaActual === $ruta ? 'aria-current="page"' : '' ?>>
                    <?= $titulo ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>