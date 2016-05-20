<?php namespace Modules\Controller\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Schema extends Model {
    protected $fillable = ['title', 'type', 'filename', 'image' , 'software', 'note'];
    protected $with = ['regulators'];

    public function regulators() {
        return $this->hasMany('Modules\Controller\Entities\Regulator','schema_id');
    }

    public function experiment() {
        return $this->belongsTo('Modules\Experiments\Entities\Experiment','experiment_id');
    }

    public function getFileContent(){
        return \File::get(storage_path('schemas/'.$this->id.'/file/'.$this->filename));
    }

    public function getFilePath(){
        return storage_path('schemas/'.$this->id.'/file/'.$this->filename);
    }
}