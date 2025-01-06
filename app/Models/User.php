<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employment_type',
        'hourly_rate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
    /**
     * @var int|mixed|null
     */
    private mixed $hourly_rate;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Dodaj accessor
    public function getHourlyRateInZlotyAttribute(): ?float
    {
        return $this->hourly_rate ? $this->hourly_rate / 100 : null;
    }

    // Dodaj mutator
    public function setHourlyRateInZlotyAttribute($value): void
    {
        $this->hourly_rate = $value ? intval($value * 100) : null;
    }

    public function actionHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActionHistory::class);
    }

    public function setHourlyRateAttribute($value)
    {
        $this->attributes['hourly_rate'] = $value * 100;
    }

    public function getHourlyRateAttribute($value)
    {
        return $value / 100;
    }

}
