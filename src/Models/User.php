<?php

namespace EscolaLms\Payments\Models;

use EscolaLms\Core\Models\User as CoreUser;
use EscolaLms\Payments\Concerns\Billable as BillableTrait;
use EscolaLms\Payments\Contracts\Billable as ContractsBillable;

class User extends CoreUser implements ContractsBillable
{
    use BillableTrait;
}
