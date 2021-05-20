<?php

namespace EscolaLms\Payments\Models;

use EscolaLms\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="TransactionRegistration",
 *     required={"amount","currency"},
 *     @OA\Property(
 *          property="amount",
 *          type="integer",
 *          description="amount of currency to charge"
 *     ),
 *     @OA\Property(
 *          property="currency",
 *          type="string",
 *          description="Currency code defined as alpha 3 in ISO 4217"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         maxLength=255,
 *         description="short description text that will show up in payment dialog"
 *     ),
 *     @OA\Property(
 *         property="buyer_id",
 *         type="integer",
 *         description="identifier of the user object buys a good"
 *     ),
 * )
 *
 * @property integer $id
 * @property integer $amount
 * @property string $currency
 * @property string $description
 * @property integer $buyer_id
 */
class TransactionRegistration extends Model
{
    use HasFactory;

    public $table = 'escolalms_payments_transaction_registration';
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'integer',
        'currency' => 'string',
        'description' => 'string',
        'buyer_id' => 'integer',
    ];

    public $fillable = [
        'amount',
        'currency',
        'description',
        'buyer_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
