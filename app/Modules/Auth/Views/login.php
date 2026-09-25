<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="text-center mb-4">
            <span class="brand-mark" aria-hidden="true">SCC</span>
            <h1 class="h3 mt-3 mb-1"><?= e(app_name()) ?></h1>
            <p class="text-secondary">Entre para acessar a gestão da clínica.</p>
        </div>

        <?php require BASE_PATH . '/app/Shared/Views/partials/alerts.php'; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url('login')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input class="form-control<?= invalid_class('email') ?>" id="email" name="email" type="email" value="<?= e(old('email')) ?>" autocomplete="username" maxlength="190" required autofocus>
                        <?php if ($error = field_error('email')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="senha">Senha</label>
                        <input class="form-control<?= invalid_class('senha') ?>" id="senha" name="senha" type="password" autocomplete="current-password" maxlength="255" required>
                        <?php if ($error = field_error('senha')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
