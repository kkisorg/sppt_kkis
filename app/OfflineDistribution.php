<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfflineDistribution extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offline_distribution';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_timestamp';

    const UPDATED_AT = 'update_timestamp';

    /**
     * Get the media associated with the offline distribution.
     */
    public function media()
    {
        return $this->hasOneThrough(
            'App\Media',
            'App\OfflineMedia',
            'media_id',
            'id',
            'offline_media_id',
            'media_id'
        );
    }

    /**
     * Get the announcement associated with the offline distribution.
     */
    public function announcement()
    {
        return $this->belongsToMany('App\Announcement', 'announcement_offline_distribution')->withPivot('content');
    }
}
