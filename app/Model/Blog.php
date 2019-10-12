<?php

/**
 *  This Model Class represents Blog Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 * Generate Comment for Class Desription
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Blog: Blogs Model
 *
 * Blogs help users to improve their health by following the tips given by health specialist
 */
class Blog extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "blog";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'title',
        'source_link',
        'free_image_link',
        'keywords',
        'description',
        'summary_title',
        'original_article_link',
        'published_date'
    ];
}
