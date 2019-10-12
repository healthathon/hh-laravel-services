<?php

/**
 * This Model Class represents ThyrocareTests Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * ThyrocareTests Model
 *
 * This file contains all information about thyrocare tests
 * and relationships with other model as needed
 *
 * @package App\Model
 */
class LabsTest extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "labs_tests";

    /**
     * @var array Hidden Items (Not to Show in Response)
     */
    protected $hidden = [
        'lab_id',
        'created_at',
        'updated_at'
    ];
    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'profile', 'test_code', 'abbr',
        'about', 'reason_to_do', 'sample_type',
        'preparation', 'process_duration', 'result_duration',
        'results', 'age_group', 'good_range',
        'parameters_tested', 'parameters_tested_unit', 'price',
        'test_suggestions', 'lab_id'
    ];

    public function lab()
    {
        return $this->belongsTo('App\Model\DiagnosticLabInformation', 'lab_id', 'id');
    }
}
