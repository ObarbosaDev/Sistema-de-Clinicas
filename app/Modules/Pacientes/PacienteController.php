<?php

declare(strict_types=1);

namespace Clinica\Modules\Pacientes;

use Clinica\Core\HttpException;
use Clinica\Core\Session;
use Clinica\Core\Validator;
use Clinica\Core\View;
use mysqli_sql_exception;

final class PacienteController
{
    private PacienteRepository $repository;

    public function __construct()
    {
        $this->repository = new PacienteRepository();
    }

    public function index(): void
    {
        View::render('Modules/Pacientes/Views/index', [
            'title' => 'Pacientes',
            'pacientes' => $this->repository->all(),
        ]);
    }

    public function create(): void
    {
        View::render('Modules/Pacientes/Views/form', [
            'title' => 'Cadastrar paciente',
            'heading' => 'Cadastrar paciente',
            'action' => 'pacientes/criar',
            'paciente' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();

        if (!$this->validate($data)) {
            \redirect('pacientes/criar');
        }

        try {
            $this->repository->create($data);
            Session::flash('success', 'Paciente cadastrado com sucesso.');
            \redirect('pacientes');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $this->duplicateFailure($data, 'pacientes/criar');
            }

            throw $exception;
        }
    }

    public function edit(): void
    {
        $id = $this->id($_GET['id'] ?? null);
        $paciente = $this->repository->find($id);

        if ($paciente === null) {
            throw new HttpException(404, 'Paciente não encontrado.');
        }

        View::render('Modules/Pacientes/Views/form', [
            'title' => 'Editar paciente',
            'heading' => 'Editar paciente',
            'action' => 'pacientes/editar',
            'paciente' => $paciente,
        ]);
    }

    public function update(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Paciente não encontrado.');
        }

        $data = $this->input();

        if (!$this->validate($data, $id)) {
            \redirect('pacientes/editar', ['id' => $id]);
        }

        try {
            $this->repository->update($id, $data);
            Session::flash('success', 'Paciente atualizado com sucesso.');
            \redirect('pacientes');
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $this->duplicateFailure($data, 'pacientes/editar', $id);
            }

            throw $exception;
        }
    }

    public function delete(): void
    {
        $id = $this->id($_POST['id'] ?? null);

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'Paciente não encontrado.');
        }

        if ($this->repository->hasAppointments($id)) {
            Session::flash('error', 'O paciente possui consultas vinculadas e não pode ser excluído.');
            \redirect('pacientes');
        }

        try {
            $this->repository->delete($id);
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1451) {
                Session::flash('error', 'O paciente possui consultas vinculadas e não pode ser excluído.');
                \redirect('pacientes');
            }

            throw $exception;
        }

        Session::flash('success', 'Paciente excluído com sucesso.');
        \redirect('pacientes');
    }

    /** @return array<string, string> */
    private function input(): array
    {
        $cpf = is_string($_POST['cpf_paciente'] ?? null) ? $_POST['cpf_paciente'] : '';

        return [
            'nome_paciente' => trim(is_string($_POST['nome_paciente'] ?? null) ? $_POST['nome_paciente'] : ''),
            'cpf_paciente' => (string) preg_replace('/\D/', '', $cpf),
            'dt_nasc_paciente' => trim(is_string($_POST['dt_nasc_paciente'] ?? null) ? $_POST['dt_nasc_paciente'] : ''),
            'sexo_paciente' => trim(is_string($_POST['sexo_paciente'] ?? null) ? $_POST['sexo_paciente'] : ''),
            'endereco_paciente' => trim(is_string($_POST['endereco_paciente'] ?? null) ? $_POST['endereco_paciente'] : ''),
            'fone_paciente' => trim(is_string($_POST['fone_paciente'] ?? null) ? $_POST['fone_paciente'] : ''),
            'email_paciente' => strtolower(trim(is_string($_POST['email_paciente'] ?? null) ? $_POST['email_paciente'] : '')),
        ];
    }

    /** @param array<string, string> $data */
    private function validate(array $data, ?int $ignoreId = null): bool
    {
        $errors = Validator::validate(
            $data,
            [
                'nome_paciente' => ['required', 'string', 'max:100'],
                'cpf_paciente' => ['required', 'string', 'cpf'],
                'dt_nasc_paciente' => ['required', 'date'],
                'sexo_paciente' => ['required', 'in:m,f,o,n'],
                'endereco_paciente' => ['nullable', 'string', 'max:150'],
                'fone_paciente' => ['nullable', 'string', 'max:20'],
                'email_paciente' => ['nullable', 'string', 'email', 'max:190'],
            ],
            [
                'nome_paciente' => 'nome',
                'cpf_paciente' => 'CPF',
                'dt_nasc_paciente' => 'data de nascimento',
                'sexo_paciente' => 'sexo',
                'endereco_paciente' => 'endereço',
                'fone_paciente' => 'telefone',
                'email_paciente' => 'e-mail',
            ],
        );

        if ($data['dt_nasc_paciente'] > date('Y-m-d')) {
            $errors['dt_nasc_paciente'][] = 'A data de nascimento não pode estar no futuro.';
        }

        if ($errors === [] && $this->repository->cpfExists($data['cpf_paciente'], $ignoreId)) {
            $errors['cpf_paciente'][] = 'Já existe um paciente com este CPF.';
        }

        if ($errors === [] && $this->repository->emailExists($data['email_paciente'], $ignoreId)) {
            $errors['email_paciente'][] = 'Já existe um paciente com este e-mail.';
        }

        if ($errors === []) {
            return true;
        }

        Session::flash('errors', $errors);
        Session::flash('old', $data);

        return false;
    }

    /** @param array<string, string> $data */
    private function duplicateFailure(array $data, string $route, ?int $id = null): never
    {
        Session::flash('error', 'CPF ou e-mail já cadastrado para outro paciente.');
        Session::flash('old', $data);
        \redirect($route, $id === null ? [] : ['id' => $id]);
    }

    private function id(mixed $value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new HttpException(404, 'Identificador de paciente inválido.');
        }

        return $id;
    }
}
