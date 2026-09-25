<?php /** @var list<array<string, mixed>> $medicos */ ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Médicos</h1><p class="text-secondary mb-0">Profissionais cadastrados na clínica.</p></div>
    <a class="btn btn-primary" href="<?= e(url('medicos/criar')) ?>">Cadastrar médico</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Lista de médicos cadastrados</caption>
            <thead class="table-light"><tr><th scope="col">Nome</th><th scope="col">CRM</th><th scope="col">Especialidade</th><th scope="col" class="text-end">Ações</th></tr></thead>
            <tbody>
            <?php if ($medicos === []): ?>
                <tr><td colspan="4" class="text-center text-secondary py-5">Nenhum médico cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($medicos as $medico): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($medico['nome_medico']) ?></td>
                        <td><?= e($medico['crm_medico']) ?></td>
                        <td><?= e($medico['especialidade_medico']) ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('medicos/editar', ['id' => (int) $medico['id_medico']])) ?>" aria-label="Editar <?= e($medico['nome_medico']) ?>">Editar</a>
                            <?php if (is_admin()): ?>
                                <form class="d-inline" method="post" action="<?= e(url('medicos/excluir')) ?>" data-confirm="Excluir este médico?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($medico['id_medico']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir <?= e($medico['nome_medico']) ?>">Excluir</button>
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
