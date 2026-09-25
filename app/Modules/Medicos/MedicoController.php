<?php

declare(strict_types=1);

namespace Clinica\Modules\Medicos;

use Clinica\Core\HttpException;
use Clinica\Core\Session;
use Clinica\Core\Validator;
use Clinica\Core\View;
use mysqli_sql_exception;

final class MedicoController
{
    private MedicoRepository $repository;

    public function __construct()
    {
        $this->repository = new MedicoRepository();
    }

    public function index(): void
    {
        View::render('Modules/Medicos/Views/index', [
            'title' => 'Médicos',
            'medicos' => $this->repository->all(),
        ]);
    }

    public function create(): void
    {
        View::render('Modules/Medicos/Views/form', [
            'title' => 'Cadastrar médico',
            'heading' => 'Cadastrar médico',
            'action' => 'medicos/criar',
            'medico' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();

        if (!$this->validate($data)) {
            \redirect('medicos/criar');
        }

        try {
            $this->repository->create($data);
            Session::flash('success', 'Médico cadastrado com sucesso.');
            \redirect('medicos');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                Session::flash('errors', ['crm_medico' => ['Já existe um médico com este CRM.']]);
                Session::flash('old', $data);
                \redirect('medicos/criar');
            }

            throw $exception;
        }
    }

    public function edit(): void
    {
        $id = $this->id($_GET['id'] ?? null);
        $medico = $this->repository->find($id);

        if ($medico === null) {
            throw new HttpException(404, 'Médico não encontrado.');
        }

        View::render('Modules/Medicos/Views/form', [
            'title' => 'Editar médico',
            'heading' => 'Editar médico',
            'action' => 'medicos/editar',
            'medico' => $medico,
        ]);
    }

    public function update(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Médico não encontrado.');
        }

        $data = $this->input();

        if (!$this->validate($data)) {
            Session::flash('old', array_merge($data, ['id' => $id]));
            \redirect('medicos/editar', ['id' => $id]);
        }

        try {
            $this->repository->update($id, $data);
            Session::flash('success', 'Médico atualizado com sucesso.');
            \redirect('medicos');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                Session::flash('errors', ['crm_medico' => ['Já existe um médico com este CRM.']]);
                Session::flash('old', $data);
                \redirect('medicos/editar', ['id' => $id]);
            }

            throw $exception;
        }
    }

    public function delete(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Médico não encontrado.');
        }

        if ($this->repository->hasAppointments($id)) {
            Session::flash('error', 'O médico possui consultas vinculadas e não pode ser excluído.');
            \redirect('medicos');
        }

        try {
            $this->repository->delete($id);
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1451) {
                Session::flash('error', 'O médico possui consultas vinculadas e não pode ser excluído.');
                \redirect('medicos');
            }

            throw $exception;
        }

        Session::flash('success', 'Médico excluído com sucesso.');
        \redirect('medicos');
    }

    /** @return array{nome_medico: string, crm_medico: string, especialidade_medico: string} */
    private function input(): array
    {
        return [
            'nome_medico' => trim(is_string($_POST['nome_medico'] ?? null) ? $_POST['nome_medico'] : ''),
            'crm_medico' => strtoupper(trim(is_string($_POST['crm_medico'] ?? null) ? $_POST['crm_medico'] : '')),
            'especialidade_medico' => trim(is_string($_POST['especialidade_medico'] ?? null) ? $_POST['especialidade_medico'] : ''),
        ];
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data): bool
    {
        $errors = Validator::validate(
            $data,
            [
                'nome_medico' => ['required', 'string', 'max:100'],
                'crm_medico' => ['required', 'string', 'max:20'],
                'especialidade_medico' => ['required', 'string', 'max:80'],
            ],
            [
                'nome_medico' => 'nome',
                'crm_medico' => 'CRM',
                'especialidade_medico' => 'especialidade',
            ],
        );

        if ($errors === []) {
            return true;
        }

        Session::flash('errors', $errors);
        Session::flash('old', $data);

        return false;
    }

    private function id(mixed $value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new HttpException(404, 'Identificador de médico inválido.');
        }

        return $id;
    }
}
