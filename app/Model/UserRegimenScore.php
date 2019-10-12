<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserRegimenScore extends Model
{
    protected $table = "user_regimen_scores";
    protected $primaryKey = "id";
    protected $fillable = [
        'regimen_id',
        'user_id',
        'secured_score'
    ];

    public function regimen()
    {
        return $this->belongsTo('App\Model\Tasks\taskBank', 'id', 'regimen_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'id', 'user_id');
    }
}
