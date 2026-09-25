<?php
/** @var array{medicos: int, pacientes: int, consultas_hoje: int, consultas_futuras: int} $statistics */
/** @var list<array<string, mixed>> $appointments */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">PAINEL OPERACIONAL</p>
        <h1 class="h2 mb-1">Visão geral</h1>
        <p class="text-secondary mb-0">Acompanhe os principais números e os próximos atendimentos.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(url('consultas/criar')) ?>">Nova consulta</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['label' => 'Médicos', 'value' => $statistics['medicos'], 'route' => 'medicos'],
        ['label' => 'Pacientes', 'value' => $statistics['pacientes'], 'route' => 'pacientes'],
        ['label' => 'Consultas hoje', 'value' => $statistics['consultas_hoje'], 'route' => 'agenda/diaria'],
        ['label' => 'Próximas consultas', 'value' => $statistics['consultas_futuras'], 'route' => 'consultas'],
    ] as $card): ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <a class="card metric-card h-100 text-decoration-none" href="<?= e(url($card['route'])) ?>">
                <div class="card-body">
                    <span class="text-secondary small"><?= e($card['label']) ?></span>
                    <strong class="d-block display-6 text-body mt-2"><?= e((string) $card['value']) ?></strong>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<section class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h2 class="h5 mb-0">Próximas consultas</h2>
        <a href="<?= e(url('agenda/diaria')) ?>">Ver agenda</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Cinco próximas consultas agendadas</caption>
            <thead class="table-light"><tr><th scope="col">Data</th><th scope="col">Horário</th><th scope="col">Paciente</th><th scope="col">Médico</th></tr></thead>
            <tbody>
            <?php if ($appointments === []): ?>
                <tr><td class="text-center text-secondary py-4" colspan="4">Nenhuma consulta futura agendada.</td></tr>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= e(format_date((string) $appointment['data_consulta'])) ?></td>
                        <td><?= e(format_time((string) $appointment['hora_consulta'])) ?></td>
                        <td><?= e($appointment['nome_paciente']) ?></td>
                        <td><?= e($appointment['nome_medico']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
