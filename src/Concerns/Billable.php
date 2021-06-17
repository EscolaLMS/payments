<?php

namespace EscolaLms\Payments\Concerns;

use EscolaLms\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Billable
{
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'billable');
    }
}
