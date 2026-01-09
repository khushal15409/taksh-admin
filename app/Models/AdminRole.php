<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * Class AdminRole
 *
 * @property int $id
 * @property string $name
 * @property string|null $modules
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AdminRole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'modules',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * @return MorphMany
     */
    public function translations(): MorphMany
    {
        if (class_exists(\App\Models\Translation::class)) {
            return $this->morphMany(\App\Models\Translation::class, 'translationable');
        }
        // Return empty collection if Translation model doesn't exist
        return $this->morphMany(\Illuminate\Database\Eloquent\Relations\Relation::noConstraints(function() {
            return new class extends \Illuminate\Database\Eloquent\Model {
                protected $table = 'translations';
            };
        }), 'translationable');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getNameAttribute($value): mixed
    {
        if (isset($this->relations['translations']) && count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if (isset($translation['key']) && $translation['key'] == 'name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    /**
     * @return void
     */
    protected static function booted(): void
    {
        if (class_exists(\App\Models\Translation::class)) {
            static::addGlobalScope('translate', function (Builder $builder) {
                $builder->with(['translations' => function($query){
                    return $query->where('locale', app()->getLocale());
                }]);
            });
        }
    }
}
