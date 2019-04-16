<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

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
     * Get the announcement request associated with the media.
     */
    public function announcement_request()
    {
        return $this->belongsToMany('App\AnnouncementRequest', 'announcement_request_media');
    }

    /**
     * Get the announcement request history associated with the media.
     */
    public function announcement_request_history()
    {
        return $this->belongsToMany('App\AnnouncementRequestHistory', 'announcement_request_history_media');
    }

}
