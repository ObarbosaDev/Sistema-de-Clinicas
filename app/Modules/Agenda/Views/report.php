<?php
/** @var string $start */
/** @var string $end */
/** @var int|null $doctorId */
/** @var list<array<string, mixed>> $doctors */
/** @var array<int, list<array<string, mixed>>> $groups */
/** @var int $total */
?>
<div class="mb-4"><h1 class="h2 mb-1">Relatório de consultas</h1><p class="text-secondary mb-0">Consulte atendimentos por período e profissional.</p></div>
<form class="card border-0 shadow-sm mb-4" method="get" action="index.php">
    <input type="hidden" name="route" value="agenda/relatorio">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label" for="inicio">Data inicial</label><input class="form-control" id="inicio" name="inicio" type="date" value="<?= e($start) ?>" required></div>
        <div class="col-md-3"><label class="form-label" for="fim">Data final</label><input class="form-control" id="fim" name="fim" type="date" value="<?= e($end) ?>" required></div>
        <div class="col-md-4"><label class="form-label" for="medico">Médico</label><select class="form-select" id="medico" name="medico"><option value="">Todos os médicos</option><?php foreach ($doctors as $doctor): ?><option value="<?= e($doctor['id_medico']) ?>" <?= $doctorId === (int) $doctor['id_medico'] ? 'selected' : '' ?>><?= e($doctor['nome_medico']) ?> — <?= e($doctor['crm_medico']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Filtrar</button></div>
    </div>
</form>
<div class="d-flex justify-content-between align-items-center mb-3"><p class="mb-0 text-secondary">Período: <?= e(format_date($start)) ?> a <?= e(format_date($end)) ?></p><span class="badge text-bg-primary rounded-pill"><?= e((string) $total) ?> consulta<?= $total === 1 ? '' : 's' ?></span></div>
<?php if ($groups === []): ?>
    <div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><h2 class="h5">Nenhum resultado</h2><p class="text-secondary mb-0">Não há consultas para os filtros selecionados.</p></div></div>
<?php else: ?>
    <div class="vstack gap-4">
        <?php foreach ($groups as $appointments): $doctor = $appointments[0]; ?>
            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h2 class="h5 mb-1"><?= e($doctor['nome_medico']) ?></h2><span class="text-secondary small"><?= e($doctor['crm_medico']) ?> · <?= e((string) count($appointments)) ?> consulta<?= count($appointments) === 1 ? '' : 's' ?></span></div>
                <div class="table-responsive"><table class="table align-middle mb-0"><caption class="visually-hidden">Consultas de <?= e($doctor['nome_medico']) ?></caption><thead class="table-light"><tr><th scope="col">Data</th><th scope="col">Hora</th><th scope="col">Paciente</th><th scope="col">Descrição</th></tr></thead><tbody>
                <?php foreach ($appointments as $appointment): ?><tr><td><?= e(format_date((string) $appointment['data_consulta'])) ?></td><td><?= e(format_time((string) $appointment['hora_consulta'])) ?></td><td><?= e($appointment['nome_paciente']) ?></td><td><?= e($appointment['descricao_consulta'] ?: '—') ?></td></tr><?php endforeach; ?>
                </tbody></table></div>
            </section>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
