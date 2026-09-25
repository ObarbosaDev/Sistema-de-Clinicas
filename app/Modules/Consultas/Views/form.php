<?php
/** @var array<string, mixed>|null $consulta */
/** @var list<array<string, mixed>> $medicos */
/** @var list<array<string, mixed>> $pacientes */
/** @var string $heading */
/** @var string $action */
$selectedPatient = (string) old('paciente_id_paciente', $consulta['paciente_id_paciente'] ?? '');
$selectedDoctor = (string) old('medico_id_medico', $consulta['medico_id_medico'] ?? '');
$timeValue = old('hora_consulta', isset($consulta['hora_consulta']) ? format_time((string) $consulta['hora_consulta']) : '');
$canSubmit = $medicos !== [] && $pacientes !== [];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1"><?= e($heading) ?></h1><p class="text-secondary mb-0">Selecione profissional, paciente, data e horário.</p></div>
    <a class="btn btn-outline-secondary" href="<?= e(url('consultas')) ?>">Voltar</a>
</div>
<?php if (!$canSubmit): ?>
    <div class="alert alert-warning" role="alert">Cadastre pelo menos um médico e um paciente antes de agendar uma consulta.</div>
<?php endif; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="post" action="<?= e(url($action)) ?>" novalidate>
            <?= csrf_field() ?>
            <?php if ($consulta !== null): ?><input type="hidden" name="id" value="<?= e($consulta['id_consulta']) ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="paciente_id_paciente">Paciente</label>
                    <select class="form-select<?= invalid_class('paciente_id_paciente') ?>" id="paciente_id_paciente" name="paciente_id_paciente" required>
                        <option value="">Selecione um paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= e($paciente['id_paciente']) ?>" <?= $selectedPatient === (string) $paciente['id_paciente'] ? 'selected' : '' ?>><?= e($paciente['nome_paciente']) ?> — <?= e(mask_cpf((string) $paciente['cpf_paciente'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($error = field_error('paciente_id_paciente')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="medico_id_medico">Médico</label>
                    <select class="form-select<?= invalid_class('medico_id_medico') ?>" id="medico_id_medico" name="medico_id_medico" required>
                        <option value="">Selecione um médico</option>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?= e($medico['id_medico']) ?>" <?= $selectedDoctor === (string) $medico['id_medico'] ? 'selected' : '' ?>><?= e($medico['nome_medico']) ?> — <?= e($medico['crm_medico']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($error = field_error('medico_id_medico')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="data_consulta">Data</label>
                    <input class="form-control<?= invalid_class('data_consulta') ?>" id="data_consulta" name="data_consulta" type="date" value="<?= e(old('data_consulta', $consulta['data_consulta'] ?? date('Y-m-d'))) ?>" required>
                    <?php if ($error = field_error('data_consulta')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="hora_consulta">Horário</label>
                    <input class="form-control<?= invalid_class('hora_consulta') ?>" id="hora_consulta" name="hora_consulta" type="time" value="<?= e($timeValue) ?>" required>
                    <?php if ($error = field_error('hora_consulta')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label" for="descricao_consulta">Descrição</label>
                    <textarea class="form-control<?= invalid_class('descricao_consulta') ?>" id="descricao_consulta" name="descricao_consulta" rows="4" maxlength="2000" placeholder="Motivo ou observações do atendimento"><?= e(old('descricao_consulta', $consulta['descricao_consulta'] ?? '')) ?></textarea>
                    <?php if ($error = field_error('descricao_consulta')): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light" href="<?= e(url('consultas')) ?>">Cancelar</a>
                <button class="btn btn-primary" type="submit" <?= $canSubmit ? '' : 'disabled' ?>>Salvar consulta</button>
            </div>
        </form>
    </div>
</div>
