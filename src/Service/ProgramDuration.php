<?php

namespace App\Service;

use App\Entity\Program;
class ProgramDuration
{
    public function calculate(Program $program): string
    {
        $totalDuration = 0;

        foreach ($program->getSeasons() as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $totalDuration += $episode->getDuration();
            }
        }

        $hours = floor($totalDuration / 60);
        $minutes = $totalDuration % 60;

        $result = '';

        if ($hours > 0) {
            $result .= $hours . ' hour(s) ';
        }

        $result .= $minutes . ' minute(s)';

        return $result;
    }
}
