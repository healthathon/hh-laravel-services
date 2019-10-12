<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToBlogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->text('source_link')->nullable(true)->after('title');
            $table->text('keywords')->nullable(true)->after('source_link');
            $table->string('summary_title', 255)->nullable(true)->after('description');
            $table->text('original_article_link')->nullable(true)->after('summary_title');
            $table->text('free_image_link')->nullable(true)->after('original_article_link');
            $table->string('published_date', 255)->nullable(true)->after('free_image_link');
            $table->dropColumn(['videos_link', 'images_link']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->dropColumn([
                'source_link',
                'keywords',
                'summary_title',
                'original_article_link',
                'free_image_link',
                'published_date'
            ]);
            $table->string('videos_link')->nullable(true);
            $table->string('images_link')->nullable(true);
        });
    }
}
