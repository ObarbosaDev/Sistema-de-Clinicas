<?php
/** @var string $title */
/** @var string $message */
/** @var bool $debug */
/** @var string $details */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | <?= e(app_name()) ?></title>
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="bg-body-tertiary">
<main class="error-page card border-0 shadow-sm">
    <div class="card-body p-5">
    <p><strong>Erro <?= e((string) http_response_code()) ?></strong></p>
    <h1><?= e($title) ?></h1>
    <p><?= e($message) ?></p>
    <p><a href="<?= e(url('dashboard')) ?>">Voltar ao início</a></p>
    <?php if ($debug): ?><pre class="bg-dark text-light rounded p-3 overflow-auto"><?= e($details) ?></pre><?php endif; ?>
    </div>
</main>
</body>
</html>
