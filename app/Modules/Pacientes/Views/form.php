<?php
/** @var array<string, mixed>|null $paciente */
/** @var string $heading */
/** @var string $action */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1"><?= e($heading) ?></h1><p class="text-secondary mb-0">Mantenha os dados pessoais atualizados e protegidos.</p></div>
    <a class="btn btn-outline-secondary" href="<?= e(url('pacientes')) ?>">Voltar</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="post" action="<?= e(url($action)) ?>" novalidate>
            <?= csrf_field() ?>
            <?php if ($paciente !== null): ?><input type="hidden" name="id" value="<?= e($paciente['id_paciente']) ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="nome_paciente">Nome completo</label>
                    <input class="form-control<?= invalid_class('nome_paciente') ?>" id="nome_paciente" name="nome_paciente" value="<?= e(old('nome_paciente', $paciente['nome_paciente'] ?? '')) ?>" maxlength="100" required>
                    <?php if ($error = field_error('nome_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="cpf_paciente">CPF</label>
                    <input class="form-control<?= invalid_class('cpf_paciente') ?>" id="cpf_paciente" name="cpf_paciente" value="<?= e(old('cpf_paciente', $paciente['cpf_paciente'] ?? '')) ?>" inputmode="numeric" maxlength="14" required>
                    <?php if ($error = field_error('cpf_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="dt_nasc_paciente">Data de nascimento</label>
                    <input class="form-control<?= invalid_class('dt_nasc_paciente') ?>" id="dt_nasc_paciente" name="dt_nasc_paciente" type="date" value="<?= e(old('dt_nasc_paciente', $paciente['dt_nasc_paciente'] ?? '')) ?>" max="<?= e(date('Y-m-d')) ?>" required>
                    <?php if ($error = field_error('dt_nasc_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="sexo_paciente">Sexo</label>
                    <?php $selectedSex = (string) old('sexo_paciente', $paciente['sexo_paciente'] ?? ''); ?>
                    <select class="form-select<?= invalid_class('sexo_paciente') ?>" id="sexo_paciente" name="sexo_paciente" required>
                        <option value="">Selecione</option>
                        <option value="m" <?= $selectedSex === 'm' ? 'selected' : '' ?>>Masculino</option>
                        <option value="f" <?= $selectedSex === 'f' ? 'selected' : '' ?>>Feminino</option>
                        <option value="o" <?= $selectedSex === 'o' ? 'selected' : '' ?>>Outro</option>
                        <option value="n" <?= $selectedSex === 'n' ? 'selected' : '' ?>>Prefiro não informar</option>
                    </select>
                    <?php if ($error = field_error('sexo_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="fone_paciente">Telefone</label>
                    <input class="form-control<?= invalid_class('fone_paciente') ?>" id="fone_paciente" name="fone_paciente" type="tel" value="<?= e(old('fone_paciente', $paciente['fone_paciente'] ?? '')) ?>" maxlength="20">
                    <?php if ($error = field_error('fone_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email_paciente">E-mail</label>
                    <input class="form-control<?= invalid_class('email_paciente') ?>" id="email_paciente" name="email_paciente" type="email" value="<?= e(old('email_paciente', $paciente['email_paciente'] ?? '')) ?>" maxlength="190">
                    <?php if ($error = field_error('email_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="endereco_paciente">Endereço</label>
                    <input class="form-control<?= invalid_class('endereco_paciente') ?>" id="endereco_paciente" name="endereco_paciente" value="<?= e(old('endereco_paciente', $paciente['endereco_paciente'] ?? '')) ?>" maxlength="150">
                    <?php if ($error = field_error('endereco_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light" href="<?= e(url('pacientes')) ?>">Cancelar</a>
                <button class="btn btn-primary" type="submit">Salvar paciente</button>
            </div>
        </form>
    </div>
</div>
