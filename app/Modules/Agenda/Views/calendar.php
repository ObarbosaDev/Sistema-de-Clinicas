<?php
/** @var int $month */
/** @var int $year */
/** @var string $monthName */
/** @var int $leadingDays */
/** @var list<array{date: string, day: int, weekday: int, count: int}> $days */
$months = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h1 class="h2 mb-1">Calendário da agenda</h1><p class="text-secondary mb-0">Volume de consultas por dia — não representa lotação ou indisponibilidade.</p></div>
    <form class="row g-2 align-items-end" method="get" action="index.php">
        <input type="hidden" name="route" value="agenda/calendario">
        <div class="col-auto"><label class="form-label" for="mes">Mês</label><select class="form-select" id="mes" name="mes"><?php foreach ($months as $value => $label): ?><option value="<?= $value ?>" <?= $value === $month ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
        <div class="col-auto"><label class="form-label" for="ano">Ano</label><select class="form-select" id="ano" name="ano"><?php for ($value = date('Y') - 2; $value <= date('Y') + 2; $value++): ?><option value="<?= e((string) $value) ?>" <?= $value === $year ? 'selected' : '' ?>><?= e((string) $value) ?></option><?php endfor; ?></select></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit">Mostrar</button></div>
    </form>
</div>
<section class="card border-0 shadow-sm" aria-labelledby="calendar-title">
    <div class="card-header bg-white py-3"><h2 class="h5 mb-0" id="calendar-title"><?= e($monthName) ?> de <?= e((string) $year) ?></h2></div>
    <div class="card-body">
        <div class="calendar-weekdays" aria-hidden="true"><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span><span>Dom</span></div>
        <div class="calendar-grid">
            <?php for ($blank = 0; $blank < $leadingDays; $blank++): ?><div class="calendar-day calendar-day-empty" aria-hidden="true"></div><?php endfor; ?>
            <?php foreach ($days as $day): ?>
                <a class="calendar-day <?= $day['count'] > 0 ? 'has-appointments' : '' ?>" href="<?= e(url('agenda/diaria', ['data' => $day['date']])) ?>" aria-label="<?= e((string) $day['day']) ?> de <?= e($monthName) ?>: <?= e((string) $day['count']) ?> consultas">
                    <strong><?= e((string) $day['day']) ?></strong>
                    <span><?= $day['count'] === 0 ? 'Sem consultas' : e($day['count'] . ($day['count'] === 1 ? ' consulta' : ' consultas')) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
