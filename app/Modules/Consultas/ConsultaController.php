<?php

declare(strict_types=1);

namespace Clinica\Modules\Consultas;

use Clinica\Core\HttpException;
use Clinica\Core\Session;
use Clinica\Core\Validator;
use Clinica\Core\View;
use DateTimeImmutable;
use mysqli_sql_exception;

final class ConsultaController
{
    private ConsultaRepository $repository;

    public function __construct()
    {
        $this->repository = new ConsultaRepository();
    }

    public function index(): void
    {
        View::render('Modules/Consultas/Views/index', [
            'title' => 'Consultas',
            'consultas' => $this->repository->all(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm(null, 'Cadastrar consulta', 'consultas/criar');
    }

    public function store(): void
    {
        $data = $this->input();

        if (!$this->validate($data, null, true)) {
            \redirect('consultas/criar');
        }

        try {
            $this->repository->create($data);
            Session::flash('success', 'Consulta agendada com sucesso.');
            \redirect('consultas');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $this->conflictFailure($data, 'consultas/criar');
            }

            if ($exception->getCode() === 1452) {
                $this->relationshipFailure($data, 'consultas/criar');
            }

            throw $exception;
        }
    }

    public function edit(): void
    {
        $id = $this->id($_GET['id'] ?? null);
        $consulta = $this->repository->find($id);

        if ($consulta === null) {
            throw new HttpException(404, 'Consulta não encontrada.');
        }

        $this->renderForm($consulta, 'Editar consulta', 'consultas/editar');
    }

    public function update(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Consulta não encontrada.');
        }

        $data = $this->input();

        if (!$this->validate($data, $id, false)) {
            \redirect('consultas/editar', ['id' => $id]);
        }

        try {
            $this->repository->update($id, $data);
            Session::flash('success', 'Consulta atualizada com sucesso.');
            \redirect('consultas');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $this->conflictFailure($data, 'consultas/editar', $id);
            }

            if ($exception->getCode() === 1452) {
                $this->relationshipFailure($data, 'consultas/editar', $id);
            }

            throw $exception;
        }
    }

    public function delete(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Consulta não encontrada.');
        }

        $this->repository->delete($id);
        Session::flash('success', 'Consulta excluída com sucesso.');
        \redirect('consultas');
    }

    /** @param array<string, mixed>|null $consulta */
    private function renderForm(?array $consulta, string $heading, string $action): void
    {
        View::render('Modules/Consultas/Views/form', [
            'title' => $heading,
            'heading' => $heading,
            'action' => $action,
            'consulta' => $consulta,
            'medicos' => $this->repository->doctors(),
            'pacientes' => $this->repository->patients(),
        ]);
    }

    /** @return array<string, mixed> */
    private function input(): array
    {
        return [
            'paciente_id_paciente' => (int) ($_POST['paciente_id_paciente'] ?? 0),
            'medico_id_medico' => (int) ($_POST['medico_id_medico'] ?? 0),
            'data_consulta' => trim(is_string($_POST['data_consulta'] ?? null) ? $_POST['data_consulta'] : ''),
            'hora_consulta' => trim(is_string($_POST['hora_consulta'] ?? null) ? $_POST['hora_consulta'] : ''),
            'descricao_consulta' => trim(is_string($_POST['descricao_consulta'] ?? null) ? $_POST['descricao_consulta'] : ''),
        ];
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data, ?int $ignoreId, bool $isCreation): bool
    {
        $errors = Validator::validate(
            $data,
            [
                'paciente_id_paciente' => ['required', 'integer', 'positive'],
                'medico_id_medico' => ['required', 'integer', 'positive'],
                'data_consulta' => ['required', 'date'],
                'hora_consulta' => ['required', 'time'],
                'descricao_consulta' => ['nullable', 'string', 'max:2000'],
            ],
            [
                'paciente_id_paciente' => 'paciente',
                'medico_id_medico' => 'médico',
                'data_consulta' => 'data',
                'hora_consulta' => 'horário',
                'descricao_consulta' => 'descrição',
            ],
        );

        if ($errors === [] && $isCreation) {
            $scheduledAt = new DateTimeImmutable(
                (string) $data['data_consulta'] . ' ' . (string) $data['hora_consulta'],
            );

            if ($scheduledAt < new DateTimeImmutable()) {
                $errors['hora_consulta'][] = 'Uma nova consulta não pode ser agendada no passado.';
            }
        }

        if ($errors === [] && !$this->repository->doctorExists((int) $data['medico_id_medico'])) {
            $errors['medico_id_medico'][] = 'O médico selecionado não existe.';
        }

        if ($errors === [] && !$this->repository->patientExists((int) $data['paciente_id_paciente'])) {
            $errors['paciente_id_paciente'][] = 'O paciente selecionado não existe.';
        }

        if ($errors === []) {
            $conflict = $this->repository->findConflict(
                (int) $data['medico_id_medico'],
                (int) $data['paciente_id_paciente'],
                (string) $data['data_consulta'],
                (string) $data['hora_consulta'],
                $ignoreId,
            );

            if ($conflict !== null) {
                $errors['hora_consulta'][] = $this->conflictMessage($conflict, $data);
            }
        }

        if ($errors === []) {
            return true;
        }

        Session::flash('errors', $errors);
        Session::flash('old', $data);

        return false;
    }

    /** @param array<string, mixed> $conflict @param array<string, mixed> $data */
    private function conflictMessage(array $conflict, array $data): string
    {
        if ((int) $conflict['medico_id_medico'] === (int) $data['medico_id_medico']) {
            return 'O médico já possui uma consulta nesse horário.';
        }

        return 'O paciente já possui uma consulta nesse horário.';
    }

    /** @param array<string, mixed> $data */
    private function conflictFailure(array $data, string $route, ?int $id = null): never
    {
        Session::flash('error', 'O médico ou o paciente já possui uma consulta nesse horário.');
        Session::flash('old', $data);
        \redirect($route, $id === null ? [] : ['id' => $id]);
    }

    /** @param array<string, mixed> $data */
    private function relationshipFailure(array $data, string $route, ?int $id = null): never
    {
        Session::flash('error', 'O médico ou o paciente selecionado não está mais disponível.');
        Session::flash('old', $data);
        \redirect($route, $id === null ? [] : ['id' => $id]);
    }

    private function id(mixed $value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new HttpException(404, 'Identificador de consulta inválido.');
        }

        return $id;
    }
}
