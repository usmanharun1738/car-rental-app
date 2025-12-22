<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DriverLicense extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'full_name',
        'date_of_birth',
        'license_class',
        'sex',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'state_of_issue',
        'front_image_path',
        'back_image_path',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'verified_at' => 'datetime',
            'status' => LicenseStatus::class,
        ];
    }

    /**
     * Get the user who owns this license
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified this license
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the license is valid (verified and not expired)
     */
    public function isValid(): bool
    {
        return $this->status === LicenseStatus::VERIFIED 
            && $this->expiry_date->isFuture();
    }

    /**
     * Check if the license is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * Get the front image URL
     */
    public function getFrontImageUrlAttribute(): ?string
    {
        return $this->front_image_path 
            ? Storage::url($this->front_image_path) 
            : null;
    }

    /**
     * Get the back image URL
     */
    public function getBackImageUrlAttribute(): ?string
    {
        return $this->back_image_path 
            ? Storage::url($this->back_image_path) 
            : null;
    }

    /**
     * Verify the license
     */
    public function verify(User $admin): void
    {
        $this->update([
            'status' => LicenseStatus::VERIFIED,
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject the license
     */
    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status' => LicenseStatus::REJECTED,
            'verified_at' => null,
            'verified_by' => $admin->id,
            'rejection_reason' => $reason,
        ]);
    }
}
