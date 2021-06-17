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

/**
 * \EscolaLms\Payments\Models\Payment
 * 
 * @property int $id
 * @property Currency $currency
 * @property int $amount
 * @property PaymentStatus $status
 * @property string $description
 * @property string $order_id
 * @property-read Model|\Eloquent $billable
 * @property-read Model|\Eloquent $payable
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @mixin \Eloquent
 */
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

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getPaymentMoney(): Money
    {
        return new Money($this->amount, $this->currency->toMoneyCurrency());
    }
}
