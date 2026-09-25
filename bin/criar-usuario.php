<?php

declare(strict_types=1);

use Clinica\Core\ErrorHandler;
use Clinica\Modules\Auth\UsuarioRepository;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/app/bootstrap.php';

$options = getopt('', ['nome:', 'email:', 'perfil::']);
$name = trim(is_string($options['nome'] ?? null) ? $options['nome'] : '');
$email = strtolower(trim(is_string($options['email'] ?? null) ? $options['email'] : ''));
$role = is_string($options['perfil'] ?? null) ? $options['perfil'] : 'administrador';
$password = getenv('CLINICA_USER_PASSWORD');
$length = static fn (string $value): int => function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);

if ($name === '' || $length($name) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false || $length($email) > 190) {
    fwrite(STDERR, "Uso: php bin/criar-usuario.php --nome=\"Nome\" --email=usuario@exemplo.com [--perfil=administrador|atendente]" . PHP_EOL);
    exit(1);
}

if (!in_array($role, ['administrador', 'atendente'], true)) {
    fwrite(STDERR, "Perfil inválido. Use administrador ou atendente." . PHP_EOL);
    exit(1);
}

if (!is_string($password) || strlen($password) < 12) {
    fwrite(STDERR, "Defina CLINICA_USER_PASSWORD com pelo menos 12 caracteres antes de executar." . PHP_EOL);
    exit(1);
}

try {
    $repository = new UsuarioRepository();

    if ($repository->findByEmail($email) !== null) {
        fwrite(STDERR, "Já existe um usuário com esse e-mail." . PHP_EOL);
        exit(1);
    }

    $repository->create($name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
    fwrite(STDOUT, "Usuário criado com sucesso." . PHP_EOL);
    exit(0);
} catch (Throwable $exception) {
    ErrorHandler::report($exception);
    fwrite(STDERR, "Não foi possível criar o usuário. Consulte storage/logs/app.log." . PHP_EOL);
    exit(1);
}
