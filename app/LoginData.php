<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoginData extends Model
{
    protected $table = "account_accesses";
    public $timestamps = false;

    protected $geofields = array('location');


    public function setLocationAttribute($value) {
        $this->attributes['location'] = DB::raw("POINT($value)");
    }

    public function getLocationAttribute($value){

        $loc =  substr($value, 6);
        $loc = preg_replace('/[ ,]+/', ',', $loc, 1);

        return substr($loc,0,-1);
    }

    public function newQuery($excludeDeleted = true)
    {
        $raw='';
        foreach($this->geofields as $column){
            $raw .= ' astext('.$column.') as '.$column.' ';
        }

        return parent::newQuery($excludeDeleted)->addSelect('*',DB::raw($raw));
    }

    protected $fillable = ['account_id', 'ip', 'os', 'created_at', 'location', 'country'];

    public function account() {
        return $this->belongsTo('App\Account','account_id');
    }
}
