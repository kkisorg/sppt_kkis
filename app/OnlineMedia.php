<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineMedia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'online_media';

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
     * Get the media associated with the online media.
     */
    public function media()
    {
        return $this->belongsTo('App\Media');
    }

    /**
     * Get the online media publish schedule associated with the announcement.
     */
    public function announcement_online_media_publish_schedule()
    {
        return $this->hasMany('App\AnnouncementOnlineMediaPublishSchedule', 'media_id', 'media_id');
    }
}
