<?php

namespace EscolaLms\Payments\Models;

use BenSampo\Enum\Traits\CastsEnums;
use EscolaLms\Payments\Enums\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Money\Money;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Models\Schemas\PaymentSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model implements PaymentSchema
{
    use CastsEnums;
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'currency' => Currency::class,
        'status' => PaymentStatus::class,
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPaymentMoney(): Money
    {
        return new Money($this->amount, $this->currency->toMoneyCurrency());
    }
}
