<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodeDetails extends Model
{
    public $timestamps = false;
    protected $table = 'code_details';
    protected $fillable = ['state_id', 'a_type','ref_no','d_type','user_code','created_at'];

    public function stateName()
    {
        return $this->belongsTo('App\StateList', 'state_id');
    }
}
