<?php
/** @var array<string, mixed>|null $medico */
/** @var string $heading */
/** @var string $action */
?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div><h1 class="h2 mb-1"><?= e($heading) ?></h1><p class="text-secondary mb-0">Informe os dados profissionais.</p></div>
            <a class="btn btn-outline-secondary" href="<?= e(url('medicos')) ?>">Voltar</a>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url($action)) ?>" novalidate>
                    <?= csrf_field() ?>
                    <?php if ($medico !== null): ?><input type="hidden" name="id" value="<?= e($medico['id_medico']) ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label" for="nome_medico">Nome completo</label>
                        <input class="form-control<?= invalid_class('nome_medico') ?>" id="nome_medico" name="nome_medico" value="<?= e(old('nome_medico', $medico['nome_medico'] ?? '')) ?>" maxlength="100" required>
                        <?php if ($error = field_error('nome_medico')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="crm_medico">CRM</label>
                            <input class="form-control text-uppercase<?= invalid_class('crm_medico') ?>" id="crm_medico" name="crm_medico" value="<?= e(old('crm_medico', $medico['crm_medico'] ?? '')) ?>" maxlength="20" required>
                            <?php if ($error = field_error('crm_medico')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" for="especialidade_medico">Especialidade</label>
                            <input class="form-control<?= invalid_class('especialidade_medico') ?>" id="especialidade_medico" name="especialidade_medico" value="<?= e(old('especialidade_medico', $medico['especialidade_medico'] ?? '')) ?>" maxlength="80" required>
                            <?php if ($error = field_error('especialidade_medico')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a class="btn btn-light" href="<?= e(url('medicos')) ?>">Cancelar</a>
                        <button class="btn btn-primary" type="submit">Salvar médico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
