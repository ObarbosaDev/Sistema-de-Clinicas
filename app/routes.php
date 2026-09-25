<?php

declare(strict_types=1);

use Clinica\Core\Router;
use Clinica\Modules\Agenda\AgendaController;
use Clinica\Modules\Auth\AuthController;
use Clinica\Modules\Consultas\ConsultaController;
use Clinica\Modules\Dashboard\DashboardController;
use Clinica\Modules\Medicos\MedicoController;
use Clinica\Modules\Pacientes\PacienteController;

$router = new Router();

$router->get('login', [AuthController::class, 'showLogin'], false);
$router->post('login', [AuthController::class, 'login'], false);
$router->post('logout', [AuthController::class, 'logout']);

$router->get('dashboard', [DashboardController::class, 'index']);

$router->get('medicos', [MedicoController::class, 'index']);
$router->get('medicos/criar', [MedicoController::class, 'create']);
$router->post('medicos/criar', [MedicoController::class, 'store']);
$router->get('medicos/editar', [MedicoController::class, 'edit']);
$router->post('medicos/editar', [MedicoController::class, 'update']);
$router->post('medicos/excluir', [MedicoController::class, 'delete'], true, ['administrador']);

$router->get('pacientes', [PacienteController::class, 'index']);
$router->get('pacientes/criar', [PacienteController::class, 'create']);
$router->post('pacientes/criar', [PacienteController::class, 'store']);
$router->get('pacientes/editar', [PacienteController::class, 'edit']);
$router->post('pacientes/editar', [PacienteController::class, 'update']);
$router->post('pacientes/excluir', [PacienteController::class, 'delete'], true, ['administrador']);

$router->get('consultas', [ConsultaController::class, 'index']);
$router->get('consultas/criar', [ConsultaController::class, 'create']);
$router->post('consultas/criar', [ConsultaController::class, 'store']);
$router->get('consultas/editar', [ConsultaController::class, 'edit']);
$router->post('consultas/editar', [ConsultaController::class, 'update']);
$router->post('consultas/excluir', [ConsultaController::class, 'delete'], true, ['administrador']);

$router->get('agenda/diaria', [AgendaController::class, 'daily']);
$router->get('agenda/calendario', [AgendaController::class, 'calendar']);
$router->get('agenda/relatorio', [AgendaController::class, 'report']);

return $router;
