<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

class LogWallet extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'log_wallets';

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
      'wallet_id','current','type','note','files','amount','status',
      'created_uid','updated_uid'
    ];


}
