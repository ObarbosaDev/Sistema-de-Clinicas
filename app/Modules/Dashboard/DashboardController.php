<?php

declare(strict_types=1);

namespace Clinica\Modules\Dashboard;

use Clinica\Core\View;

final class DashboardController
{
    public function index(): void
    {
        $repository = new DashboardRepository();

        View::render('Modules/Dashboard/Views/index', [
            'title' => 'Visão geral',
            'statistics' => $repository->statistics(),
            'appointments' => $repository->nextAppointments(),
        ]);
    }
}
