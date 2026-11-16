<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['registration_id', 'order_id', 'amount', 'payment_status', 'snap_token', 'payment_type', 'payment_type_id', 'paid_at'])]
class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SETTLEMENT = 'settlement';

    public const STATUS_CAPTURE = 'capture';

    public const STATUS_DENY = 'deny';

    public const STATUS_EXPIRE = 'expire';

    public const STATUS_CANCEL = 'cancel';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * @return BelongsTo<PaymentType, $this>
     */
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentTypeLabel(): string
    {
        return $this->paymentType?->name
            ?? ($this->payment_type ? ucfirst(str_replace('_', ' ', $this->payment_type)) : '-');
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, [self::STATUS_SETTLEMENT, self::STATUS_CAPTURE], true);
    }
}
