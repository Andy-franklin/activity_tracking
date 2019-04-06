<?php

namespace App\Twig;

use Carbon\CarbonInterval;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CarbonIntervalFormatter extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('carbonInterval', [$this, 'carbonInterval']),
        ];
    }

    public function carbonInterval($interval): string
    {
        $carbonInterval = $this->dateIntervalToCarbonInterval($interval);
        return $carbonInterval->forHumans();
    }

    /**
     * @param \DateInterval $interval
     *
     * @return CarbonInterval
     */
    private function dateIntervalToCarbonInterval($interval)
    {
        return CarbonInterval::create(
            $interval->y,
            $interval->m,
            0,
            $interval->d,
            $interval->h,
            $interval->i,
            $interval->s
        );
    }
}
