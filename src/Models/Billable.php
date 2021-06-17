<?php

namespace EscolaLms\Payments\Models;

use EscolaLms\Core\Models\User;
use EscolaLms\Payments\Concerns\Billable as ConcernsBillable;
use EscolaLms\Payments\Contracts\Billable as ContractsBillable;

final class Billable extends User implements ContractsBillable
{
    protected $table = 'users';

    use ConcernsBillable;
}
