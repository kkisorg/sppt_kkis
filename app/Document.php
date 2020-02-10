<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document';

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
}
