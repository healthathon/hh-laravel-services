<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsersTestsRecommendation extends Model
{
    protected $table = "users_tests_recommendations";
    protected $fillable = ['user_id', 'test_id'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsToMany('App\Model\User');
    }

    public function test()
    {
        return $this->belongsTo('App\Model\LabsTest', 'id', 'test_id');
    }
}
