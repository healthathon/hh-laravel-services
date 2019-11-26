<?php

/**
 * This Model Class represents Category Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 *  Represents Categories of Task User is doing to become healthier and fit
 */
class Category extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "categories";
    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'name'
    ];

    /**
     * This function is use to return the name of Category which is actually given in documents
     * @param $name : The name of Category
     *
     * @return string The proper name of Category
     */
    //but since previous developer made mistakes, refactoring entire code was not an option in respective of deadline
    public function mapCategoryName($name)
    {
        switch (strtolower($name)) {
            case "physics":
                return "Physical Fitness";
            case "mental":
                return "Mental Well-being";
            default:
                return $name;
        }
    }
}
