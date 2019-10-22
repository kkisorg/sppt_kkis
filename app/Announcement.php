<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcement';

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
     * Get the user who created the announcement.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    /**
     * Get the user who edited the announcement.
     */
    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id');
    }

    /**
     * Get the announcement request associated with the announcement.
     */
    public function announcement_request()
    {
        return $this->belongsTo('App\AnnouncementRequest');
    }

    /**
     * Get the media associated with the announcement.
     */
    public function media()
    {
        return $this->belongsToMany('App\Media', 'announcement_media')->withPivot('content');
    }

    /**
     * Get the offline distribution associated with the announcement.
     */
    public function offline_distribution()
    {
        return $this->belongsToMany('App\OfflineDistribution', 'announcement_offline_distribution')->withPivot('content');
    }

    /**
     * Get the online media publish schedule associated with the announcement.
     */
    public function announcement_online_media_publish_schedule()
    {
        return $this->hasMany('App\AnnouncementOnlineMediaPublishSchedule');
    }
}
