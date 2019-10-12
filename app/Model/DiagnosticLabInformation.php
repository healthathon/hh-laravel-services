<?php

/**
 * This Model Class represents DiagnosticLabInformation Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DiagnosticLabInformation
 *
 * Stores information about labs like address,name,description.
 */
class DiagnosticLabInformation extends Model
{
    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    /**
     * @var string Name of the table
     */
    protected $table = "diagnostic_labs_informations";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = ["name", "description", "address", "offer_data", "test_data", "profile_data"];

    public function tests()
    {
        return $this->hasMany('App\Model\LabsTest', 'lab_id', 'id')
            ->select(['id', 'profile', 'test_name', 'test_code', 'abbr', 'sample_type', 'price', 'process_duration', 'result_duration']);
    }
}
