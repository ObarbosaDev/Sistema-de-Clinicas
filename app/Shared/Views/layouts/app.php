<?php

use Clinica\Core\Auth;

/** @var string $content */
/** @var string|null $title */
$user = Auth::user();
$appName = app_name();
$pageTitle = isset($title) ? $title . ' | ' . $appName : $appName;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="bg-body-tertiary">
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top" aria-label="Navegação principal">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?= e(url('dashboard')) ?>">SCC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link<?= route_is('dashboard') ? ' active' : '' ?>" href="<?= e(url('dashboard')) ?>">Início</a></li>
                <li class="nav-item"><a class="nav-link<?= route_is('medicos', 'medicos/criar', 'medicos/editar') ? ' active' : '' ?>" href="<?= e(url('medicos')) ?>">Médicos</a></li>
                <li class="nav-item"><a class="nav-link<?= route_is('pacientes', 'pacientes/criar', 'pacientes/editar') ? ' active' : '' ?>" href="<?= e(url('pacientes')) ?>">Pacientes</a></li>
                <li class="nav-item"><a class="nav-link<?= route_is('consultas', 'consultas/criar', 'consultas/editar') ? ' active' : '' ?>" href="<?= e(url('consultas')) ?>">Consultas</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle<?= route_is('agenda/diaria', 'agenda/calendario', 'agenda/relatorio') ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Agenda</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= e(url('agenda/diaria')) ?>">Agenda diária</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('agenda/calendario')) ?>">Calendário</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('agenda/relatorio')) ?>">Relatório</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-secondary">Olá, <?= e($user['name'] ?? 'Usuário') ?></span>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger btn-sm" type="submit">Sair</button>
                </form>
            </div>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php require BASE_PATH . '/app/Shared/Views/partials/alerts.php'; ?>
    <?= $content ?>
</main>
<script src="<?= e(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
