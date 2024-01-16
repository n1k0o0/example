<?php

namespace App\Models;

use App\Enums\League\LeagueStatusEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class League extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const AVATAR_MEDIA_COLLECTION = 'avatar';

    protected $fillable = [
        //SomeCode

    ];
    protected $casts = [
        //SomeCode

    ];
    //SomeCode


    /**
     * @return MorphOne
     * @noinspection PhpUnused
     */
    public function avatar(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', self::AVATAR_MEDIA_COLLECTION);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::AVATAR_MEDIA_COLLECTION)
            ->singleFile();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    public function isNotStarted(): bool
    {
        return $this->status === LeagueStatusEnum::NOT_STARTED;
    }

    public function isCurrent(): bool
    {
        return $this->status === LeagueStatusEnum::CURRENT;
    }

    public function isArchived(): bool
    {
        return $this->status === LeagueStatusEnum::ARCHIVED;
    }
}
