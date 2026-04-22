<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Registrations', $this->getTotalRegistrations())
        ];
    }


    private function getTotalRegistrations(): int
    {
        $total = \App\Models\Registration::count(); // Assuming you have a Registration model
        return $total;
    }
}
