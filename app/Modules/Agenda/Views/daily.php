<?php
/** @var string $date */
/** @var list<array<string, mixed>> $appointments */
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h1 class="h2 mb-1">Agenda diária</h1><p class="text-secondary mb-0">Atendimentos de <?= e(format_date($date)) ?>.</p></div>
    <form class="d-flex gap-2" method="get" action="index.php">
        <input type="hidden" name="route" value="agenda/diaria">
        <div><label class="visually-hidden" for="data">Data</label><input class="form-control" id="data" name="data" type="date" value="<?= e($date) ?>" required></div>
        <button class="btn btn-primary" type="submit">Consultar</button>
    </form>
</div>
<?php if ($appointments === []): ?>
    <div class="empty-state card border-0 shadow-sm text-center"><div class="card-body py-5"><h2 class="h5">Agenda livre nesta data</h2><p class="text-secondary mb-3">Não há consultas cadastradas para o dia selecionado.</p><a class="btn btn-outline-primary" href="<?= e(url('consultas/criar')) ?>">Agendar consulta</a></div></div>
<?php else: ?>
    <div class="vstack gap-3">
        <?php foreach ($appointments as $appointment): ?>
            <article class="card border-0 shadow-sm appointment-card">
                <div class="card-body d-flex flex-column flex-md-row gap-3 align-items-md-center">
                    <div class="appointment-time"><?= e(format_time((string) $appointment['hora_consulta'])) ?></div>
                    <div class="flex-grow-1">
                        <h2 class="h5 mb-1"><?= e($appointment['nome_paciente']) ?></h2>
                        <p class="text-secondary mb-1"><?= e($appointment['nome_medico']) ?> · <?= e($appointment['crm_medico']) ?></p>
                        <?php if ($appointment['descricao_consulta']): ?><p class="mb-0"><?= e($appointment['descricao_consulta']) ?></p><?php endif; ?>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="<?= e(url('consultas/editar', ['id' => (int) $appointment['id_consulta']])) ?>">Ver consulta</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
