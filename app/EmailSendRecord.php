<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSendRecord extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_send_record';

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
       'status' => 'ON_PROGRESS'
    );

    /**
     * Get the email send schedule associated with the email send record.
     */
    public function email_send_schedule()
    {
        return $this->belongsTo('App\EmailSendSchedule');
    }

    /**
     * Get the user associated with the email send record.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

}
