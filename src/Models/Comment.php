<?php

namespace AoComments\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    //------------------------------------------------------------------------------------------------------------------
    // DYNAMIC
    //------------------------------------------------------------------------------------------------------------------

    public $dynamicClass;

    public $dynamicTable;

    public $dynamicForeign;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dynamicWith()
    {
        return $this->belongsToMany($this->dynamicClass, $this->dynamicTable, 'comment_id', $this->dynamicForeign);
    }

    //------------------------------------------------------------------------------------------------------------------
    // ATTRIBUTES
    //------------------------------------------------------------------------------------------------------------------

    protected $table = 'ao_comments_comments';

    protected $fillable = ['user_id', 'message'];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS BY OTHER PACKAGES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('ao.models.users'));
    }

}