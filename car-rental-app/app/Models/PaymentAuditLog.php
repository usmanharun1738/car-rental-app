<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAuditLog extends Model
{
    protected $fillable = [
        'payment_id',
        'event',
        'reference',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Event constants for consistency
     */
    public const EVENT_INITIATED = 'initiated';
    public const EVENT_WEBHOOK_RECEIVED = 'webhook_received';
    public const EVENT_VERIFIED = 'verified';
    public const EVENT_FAILED = 'failed';
    public const EVENT_CALLBACK_VERIFIED = 'callback_verified';
    public const EVENT_AMOUNT_MISMATCH = 'amount_mismatch';
    public const EVENT_INVALID_SIGNATURE = 'invalid_signature';

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Create an audit log entry
     */
    public static function log(
        string $event,
        ?int $paymentId = null,
        ?string $reference = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'payment_id' => $paymentId,
            'event' => $event,
            'reference' => $reference,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'metadata' => $metadata,
        ]);
    }
}
