<?php

declare(strict_types=1);

namespace Clinica\Modules\Agenda;

use Clinica\Core\Session;
use Clinica\Core\Validator;
use Clinica\Core\View;
use DateTimeImmutable;

final class AgendaController
{
    private AgendaRepository $repository;

    public function __construct()
    {
        $this->repository = new AgendaRepository();
    }

    public function daily(): void
    {
        $date = is_string($_GET['data'] ?? null) ? $_GET['data'] : date('Y-m-d');
        $errors = Validator::validate(['data' => $date], ['data' => ['required', 'date']], ['data' => 'data']);

        if ($errors !== []) {
            Session::flash('error', 'Selecione uma data válida para consultar a agenda.');
            \redirect('agenda/diaria');
        }

        View::render('Modules/Agenda/Views/daily', [
            'title' => 'Agenda diária',
            'date' => $date,
            'appointments' => $this->repository->byDate($date),
        ]);
    }

    public function calendar(): void
    {
        $currentYear = (int) date('Y');
        $month = filter_var($_GET['mes'] ?? date('n'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 12],
        ]);
        $year = filter_var($_GET['ano'] ?? $currentYear, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $currentYear - 2, 'max_range' => $currentYear + 2],
        ]);

        if ($month === false || $year === false) {
            Session::flash('error', 'Selecione um mês e um ano válidos.');
            \redirect('agenda/calendario');
        }

        $firstDay = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        $nextMonth = $firstDay->modify('first day of next month');
        $counts = $this->repository->countsByDate($firstDay->format('Y-m-d'), $nextMonth->format('Y-m-d'));
        $days = [];

        for ($day = 1; $day <= (int) $firstDay->format('t'); $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $days[] = [
                'date' => $date,
                'day' => $day,
                'weekday' => (int) date('N', strtotime($date)),
                'count' => $counts[$date] ?? 0,
            ];
        }

        View::render('Modules/Agenda/Views/calendar', [
            'title' => 'Calendário da agenda',
            'month' => $month,
            'year' => $year,
            'monthName' => $this->monthName($month),
            'leadingDays' => (int) $firstDay->format('N') - 1,
            'days' => $days,
        ]);
    }

    public function report(): void
    {
        $defaultStart = date('Y-m-01');
        $defaultEnd = date('Y-m-t');
        $start = is_string($_GET['inicio'] ?? null) ? $_GET['inicio'] : $defaultStart;
        $end = is_string($_GET['fim'] ?? null) ? $_GET['fim'] : $defaultEnd;
        $doctorValue = $_GET['medico'] ?? '';
        $doctorId = $doctorValue === '' ? null : filter_var($doctorValue, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $errors = Validator::validate(
            ['inicio' => $start, 'fim' => $end],
            ['inicio' => ['required', 'date'], 'fim' => ['required', 'date']],
            ['inicio' => 'data inicial', 'fim' => 'data final'],
        );

        if ($doctorValue !== '' && $doctorId === false) {
            $errors['medico'][] = 'Selecione um médico válido.';
        }

        if ($errors === [] && $start > $end) {
            $errors['fim'][] = 'A data final deve ser igual ou posterior à data inicial.';
        }

        if ($errors === [] && is_int($doctorId) && !$this->repository->doctorExists($doctorId)) {
            $errors['medico'][] = 'O médico selecionado não existe.';
        }

        if ($errors !== []) {
            Session::flash('error', (string) reset($errors)[0]);
            \redirect('agenda/relatorio');
        }

        $appointments = $this->repository->report($start, $end, is_int($doctorId) ? $doctorId : null);
        $groups = [];

        foreach ($appointments as $appointment) {
            $groups[(int) $appointment['id_medico']][] = $appointment;
        }

        View::render('Modules/Agenda/Views/report', [
            'title' => 'Relatório de consultas',
            'start' => $start,
            'end' => $end,
            'doctorId' => is_int($doctorId) ? $doctorId : null,
            'doctors' => $this->repository->doctors(),
            'groups' => $groups,
            'total' => count($appointments),
        ]);
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ][$month];
    }
}
