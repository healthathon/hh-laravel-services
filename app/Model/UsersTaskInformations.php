<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsersTaskInformations extends Model
{
    protected $table = "users_task_informations";

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id', 'overall_score',
        'physical_level', 'nutrition_level', 'mental_level',
        'physical_task_completed', 'nutrition_task_completed', 'mental_task_completed',
        'physical_score', 'nutrition_score', 'mental_score'
    ];

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'id', 'user_id');
    }
}
