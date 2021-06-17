<?php

namespace EscolaLms\Payments\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read \Illuminate\Support\Collection $payments
 */
interface Billable
{
    public function payments(): MorphMany;
}
