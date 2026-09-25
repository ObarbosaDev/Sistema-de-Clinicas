<?php

declare(strict_types=1);

namespace Clinica\Modules\Auth;

use Clinica\Core\Auth;
use Clinica\Core\Session;
use Clinica\Core\Validator;
use Clinica\Core\View;

final class AuthController
{
    private const DUMMY_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    private UsuarioRepository $users;
    private LoginAttemptRepository $attempts;

    public function __construct()
    {
        $this->users = new UsuarioRepository();
        $this->attempts = new LoginAttemptRepository();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            \redirect('dashboard');
        }

        View::render('Modules/Auth/Views/login', ['title' => 'Entrar'], 'guest');
    }

    public function login(): void
    {
        if (Auth::check()) {
            \redirect('dashboard');
        }

        $email = strtolower(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''));
        $password = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
        $data = ['email' => $email, 'senha' => $password];
        $errors = Validator::validate(
            $data,
            [
                'email' => ['required', 'string', 'email', 'max:190'],
                'senha' => ['required', 'string', 'max:255'],
            ],
            ['email' => 'e-mail', 'senha' => 'senha'],
        );

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            \redirect('login');
        }

        $attemptKey = hash('sha256', $email);

        if ($this->attempts->isBlocked($attemptKey)) {
            Session::flash('error', 'Muitas tentativas. Aguarde cinco minutos e tente novamente.');
            Session::flash('old', ['email' => $email]);
            \redirect('login');
        }

        $user = $this->users->findByEmail($email);
        $storedHash = $user !== null && is_string($user['senha_usuario'])
            ? $user['senha_usuario']
            : null;
        $validPassword = $this->verifyPassword($password, $storedHash);

        if ($user === null || !$validPassword) {
            $this->attempts->recordFailure($attemptKey);
            Session::flash('error', 'E-mail ou senha inválidos.');
            Session::flash('old', ['email' => $email]);
            \redirect('login');
        }

        $userId = (int) $user['id_usuario'];

        if ($storedHash !== null && strlen($storedHash) === 64 && ctype_xdigit($storedHash)) {
            $this->users->updatePasswordHash($userId, password_hash($password, PASSWORD_DEFAULT));
        } elseif ($storedHash !== null && password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
            $this->users->updatePasswordHash($userId, password_hash($password, PASSWORD_DEFAULT));
        }

        $this->attempts->clear($attemptKey);
        Auth::login($userId, (string) $user['nome_usuario'], (string) $user['perfil_usuario']);
        Session::flash('success', 'Login realizado com sucesso.');
        \redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        \redirect('login');
    }

    private function verifyPassword(string $password, ?string $storedHash): bool
    {
        if ($storedHash === null) {
            password_verify($password, self::DUMMY_PASSWORD_HASH);

            return false;
        }

        if (strlen($storedHash) === 64 && ctype_xdigit($storedHash)) {
            password_verify($password, self::DUMMY_PASSWORD_HASH);

            return hash_equals(strtolower($storedHash), hash('sha256', $password));
        }

        return password_verify($password, $storedHash);
    }
}
