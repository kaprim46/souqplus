<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'country_code',
        'phone_number',
        'uuid',
        'avatar',
        'is_verified',
        'business_info',
        'bio',
        'otp_code',
        'fcm_token',
        'forget_password_code',
        'city_id',
        'country_id',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'otp_code',
        'forget_password_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'     => 'datetime',
        'deleted_at'            => 'datetime:Y-m-d H:i',
        'password'              => 'hashed',
        'is_verified'           => 'boolean',
        'business_info'         => 'array'
    ];

    /**
     * The attributes that should be appended.
     */
    public $appends = [
        'avatar_url'
    ];

    /**
     * Generate unique uuid for user when creating
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            while (true) {
                $uuid = \Illuminate\Support\Str::uuid();
                if (!static::query()->where('uuid', $uuid)->exists()) {
                    $model->uuid = $uuid;
                    break;
                }
            }
        });
    }

    /**
     * Get the user's avatar url.
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        $name = urlencode($this->name);

        return $this->avatar ?
            asset("storage/avatars/{$this->avatar}")
            :
            "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";
    }

    /**
     * Get the advertisements for the user.
     * @return HasMany
     */
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    /**
     * Get the advertisements for the user.
     * @return HasManyThrough
     */
    public function favorites(): HasManyThrough
    {
        return $this->hasManyThrough(
            Advertisement::class,
            FavoriteAdvertisement::class,
            'user_id',
            'id',
            'id',
            'advertisements_id'
        )
            ->with([
                'category' => function($q){
                    $q->select(['id', 'name', 'slug']);
                },
                'subCategory' => function($q){
                    $q->select(['id', 'name', 'slug']);
                },
                'user' => function($q){
                    $q->select(['id', 'name', 'email', 'country_code', 'phone_number']);
                },
                'mainMedia' => function($q){
                    $q->select(['file_name', 'path']);
                },
                'city' => function($q){
                    $q->select(['id', 'name']);
                },
                'country' => function($q){
                    $q->select(['id', 'name']);
                },
        ]);
    }


    /**
     * Get the notifications for the user.
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'user_id')->with([
            'user:id,name,email,country_code,phone_number',
            'ad' => function($q){
                $q->select(['id', 'name', 'slug'])->withTrashed();
            },
            'sender' => function($q){
                $q->select(['id', 'name', 'email', 'country_code', 'phone_number'])->withTrashed();
            },
        ]);
    }

    /**
     * unreadNotifications
     * Get the unread notifications for the user.
     * @return HasMany
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'user_id')->where('read', 0);
    }

    /**
     * Get the user following.
     * @return BelongsToMany
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * Get the user followers.
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Badges
     * @return HasMany
     */
    public function badges():HasMany {
        return $this->hasMany(UserBadge::class);
    }


    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * isStore
     * @return bool
     */
    public function isStore(): bool
    {
        return $this->role === 'business';
    }

    /**
     * isCustomer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }


    /**
     * Define the many-to-one relationship with the country.
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Define the many-to-one relationship with the city.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
