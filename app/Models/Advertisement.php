<?php

namespace App\Models;

use App\Traits\Slugify;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Advertisement extends Model
{
    use HasFactory, SoftDeletes, Slugify;

    const STATUS_APPROVED = 'approved';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'category_id',
        'latitude',
        'longitude',
        'country_id',
        'city_id',
        'country_code',
        'phone_number',
        'price',
        'status',
        'user_id',
        'reject_reason',
        'sub_category_id',
        'sub_sub_category_id',
        'custom_fields',
        'fake_views',
    ];

    protected $hidden   = [
        'user_id',
        'updated_at',
    ];
    protected $casts    = [
        'created_at' => 'datetime:d-m-Y H:i',
        'updated_at' => 'datetime:d-m-Y H:i',
        'deleted_at' => 'datetime:d-m-Y H:i',
        'custom_fields' => 'array'
    ];
    public $appends     = [
        'clean_description'
    ];

    /**
     * Generate a unique slug for the advertisement based on the name.
     */
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        return $slug;
    }

    public static function boot(): void
    {
        parent::boot();

        /**
         * Create
         */
        static::creating(function ($ads) {
            $ads->fake_views = rand(0, 80);
            if (empty($ads->slug) && !empty($ads->name)) {
                $ads->slug = self::generateUniqueSlug($ads->name);
            }
        });

        /**
         * Delete
         */
        static::deleting(function ($ads) {
            DB::table('user_notifications')->where('ad_id', $ads->id)->delete();
        });
    }

    public function getCleanDescriptionAttribute(): string
    {
        return Str::limit(strip_tags($this->description), 150);
    }

    /**
     * Define the many-to-many relationship between advertisements and media files.
     *
     * @return BelongsToMany
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'advertisements_files', 'advertisements_id', 'file_id')
            ->withPivot('is_main')
            ->withTimestamps();
    }

    /**
     * Define the one-to-many relationship with Advertisement files.
     *
     * @return HasMany
     */
    public function mediaRelation(): HasMany
    {
        return $this->hasMany(AdvertisementsFile::class, 'advertisements_id', 'id');
    }

    /**
     * Retrieve the main media file associated with the advertisement.
     *
     * @return HasOneThrough
     */
    public function mainMedia(): HasOneThrough
    {
        return $this->hasOneThrough(File::class, AdvertisementsFile::class, 'advertisements_id', 'id', 'id', 'file_id')
            ->where('is_main', true);
    }

    /**
     * Define the many-to-one relationship with the category.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define the many-to-one relationship with the subcategory.
     *
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Define the many-to-one relationship with the sub-subcategory.
     *
     * @return BelongsTo
     */
    public function subSubCategory(): BelongsTo
    {
        return $this->belongsTo(SubSubCategory::class, 'sub_sub_category_id');
    }

    /**
     * Define the one-to-many relationship with advertisement comments.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(AdvertisementComment::class);
    }

    /**
     * Define the many-to-one relationship with the user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    /**
     * Define the many-to-many relationship with category filters.
     *
     * @return BelongsToMany
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'advertisement_filters', 'advertisement_id', 'filter_id')->withPivot('value');
    }

    /**
     * Define the one-to-many relationship with advertisement views.
     *
     * @return HasMany
     */
    public function views(): HasMany
    {
        return $this->hasMany(AdvertisementView::class);
    }

    /**
     * Define the one-to-one relationship with advertisement story.
     * @return HasOne
     */
    public function advertisementStory(): HasOne
    {
        return $this->hasOne(AdvertisementStory::class);
    }
}
