<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;

class Software extends Model {

    protected $fillable = [];

    public function experiments() {
    	return $this->belongsToMany(Experiment::class);
    }

}