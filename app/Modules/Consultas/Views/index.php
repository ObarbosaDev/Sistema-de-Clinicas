<?php /** @var list<array<string, mixed>> $consultas */ ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Consultas</h1><p class="text-secondary mb-0">Histórico e próximos atendimentos agendados.</p></div>
    <a class="btn btn-primary" href="<?= e(url('consultas/criar')) ?>">Agendar consulta</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Lista de consultas cadastradas</caption>
            <thead class="table-light"><tr><th scope="col">Data e horário</th><th scope="col">Paciente</th><th scope="col">Médico</th><th scope="col">Descrição</th><th scope="col" class="text-end">Ações</th></tr></thead>
            <tbody>
            <?php if ($consultas === []): ?>
                <tr><td colspan="5" class="text-center text-secondary py-5">Nenhuma consulta cadastrada.</td></tr>
            <?php else: ?>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td class="text-nowrap"><strong><?= e(format_date((string) $consulta['data_consulta'])) ?></strong><br><small class="text-secondary"><?= e(format_time((string) $consulta['hora_consulta'])) ?></small></td>
                        <td><?= e($consulta['nome_paciente']) ?></td>
                        <td><?= e($consulta['nome_medico']) ?><br><small class="text-secondary"><?= e($consulta['crm_medico']) ?></small></td>
                        <td class="description-cell"><?= e($consulta['descricao_consulta'] ?: '—') ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('consultas/editar', ['id' => (int) $consulta['id_consulta']])) ?>">Editar</a>
                            <?php if (is_admin()): ?>
                                <form class="d-inline" method="post" action="<?= e(url('consultas/excluir')) ?>" data-confirm="Excluir esta consulta?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($consulta['id_consulta']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
