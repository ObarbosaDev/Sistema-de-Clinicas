<?php
session_start();
include('config.php');

// Verifica se o usuário já está logado
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php?page=calendario-disponibilidade");
    exit;
}

// Verifica se houve submissão do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);

    // Busca o usuário no banco de dados
    $query = "SELECT * FROM usuarios WHERE email_usuario = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha
        if (hash('sha256', $senha) === $usuario['senha_usuario']) {
            // Login bem-sucedido
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];

            header("Location: index.php?page=calendario-disponibilidade");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Controle Clínico - Login</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger">
                                <?= $erro ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu e-mail" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="senha">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>Sistema de Controle Clínico</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html