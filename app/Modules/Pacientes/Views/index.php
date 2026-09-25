<?php /** @var list<array<string, mixed>> $pacientes */ ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Pacientes</h1><p class="text-secondary mb-0">Cadastros e contatos dos pacientes.</p></div>
    <a class="btn btn-primary" href="<?= e(url('pacientes/criar')) ?>">Cadastrar paciente</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Lista de pacientes cadastrados</caption>
            <thead class="table-light"><tr><th scope="col">Nome</th><th scope="col">CPF</th><th scope="col">Nascimento</th><th scope="col">Contato</th><th scope="col" class="text-end">Ações</th></tr></thead>
            <tbody>
            <?php if ($pacientes === []): ?>
                <tr><td colspan="5" class="text-center text-secondary py-5">Nenhum paciente cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($paciente['nome_paciente']) ?></td>
                        <td><?= e(mask_cpf((string) $paciente['cpf_paciente'])) ?></td>
                        <td><?= e(format_date((string) $paciente['dt_nasc_paciente'])) ?></td>
                        <td><span class="d-block"><?= e($paciente['email_paciente'] ?: '—') ?></span><small class="text-secondary"><?= e($paciente['fone_paciente'] ?: '—') ?></small></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('pacientes/editar', ['id' => (int) $paciente['id_paciente']])) ?>" aria-label="Editar <?= e($paciente['nome_paciente']) ?>">Editar</a>
                            <?php if (is_admin()): ?>
                                <form class="d-inline" method="post" action="<?= e(url('pacientes/excluir')) ?>" data-confirm="Excluir este paciente?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($paciente['id_paciente']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir <?= e($paciente['nome_paciente']) ?>">Excluir</button>
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
