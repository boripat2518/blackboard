<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

class Payment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wallet_wips';

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
      'user_id','type','note','files','amount','status',
      'created_uid','updated_uid'
    ];


}
