<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

class Lesson extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lesson_infos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'cat_id','title','note','type','cover','price','net','tag','active','room_id'
    ];


}
