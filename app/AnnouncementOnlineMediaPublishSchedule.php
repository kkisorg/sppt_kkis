<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementOnlineMediaPublishSchedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcement_online_media_publish_schedule';

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
     * The default value of some attributes.
     *
     * @var string
     */
    protected $attributes = array(
       'status' => 'INITIAL'
    );

    /**
     * Get the announcement associated with the online media publish schedule.
     */
    public function announcement()
    {
        return $this->belongsTo('App\Announcement');
    }

    /**
     * Get the media associated with the online media publish schedule.
     */
    public function media()
    {
        return $this->hasOneThrough(
            'App\Media',
            'App\OnlineMedia',
            'media_id',
            'id',
            'online_media_id',
            'media_id'
        );
    }

    /**
     * Get the announcement associated with the online media publish schedule.
     */
    public function announcement_online_media_publish_record()
    {
        return $this->hasMany('App\AnnouncementOnlineMediaPublishRecord');
    }
}
