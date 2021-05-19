<?php

namespace EscolaLms\Payments\Models;

use EscolaLms\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionRegistration
{
    private string  $id;
    private integer $amount;
    private string $currency;
    private string  $description;
    private User    $buyer;
}
