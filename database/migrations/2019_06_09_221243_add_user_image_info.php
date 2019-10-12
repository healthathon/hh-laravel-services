<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserImageInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_filename')->default(null)->after('email');
            $table->binary('profile_image_data')->default(null)->after('profile_image_filename');
            $table->dropColumn('user_image_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_image_path')->default(null)->after('email');
            $table->dropColumn(['profile_image_filename', 'profile_image_data']);
        });
    }
}
