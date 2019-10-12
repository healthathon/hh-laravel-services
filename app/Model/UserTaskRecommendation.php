<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserTaskRecommendation extends Model
{
    protected $table = "user_task_recommendations";
    protected $fillable = ['user_id', 'regimen_id'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsToMany('App\Model\User');
    }

    public function regimen()
    {
        return $this->belongsTo('App\Model\Tasks\taskBank', 'id', 'regimen_id');
    }
}
