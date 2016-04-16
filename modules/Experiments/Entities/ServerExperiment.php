<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;

class ServerExperiment extends Model {

	protected $table = "experiment_server";
    protected $fillable = ["server_id","experiment_id"];

}