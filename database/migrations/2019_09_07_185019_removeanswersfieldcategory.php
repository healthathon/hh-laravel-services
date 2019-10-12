<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Removeanswersfieldcategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_health_histories', function (Blueprint $table) {
            $table->integer('question_id')->unsigned()->comment("Question Ref");
            $table->integer('answer_id')->unsigned()->comment("Answer Ref");
            $table->dropColumn([
                'goals',
                'overall_health',
                'existing_conditions',
                'family_history'
            ]);
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_health_histories', function (Blueprint $table) {
            $table->string('goals')->comment("Goals of Users");
            $table->string('overall_health')->comment("Overall Health of Users");
            $table->string('existing_conditions')->comment("Existing Conditions of Users");
            $table->string('family_history')->comment("family history of Users");
            $table->dropColumn([
                'question_id',
                'answer_id'
            ]);
        });
    }
}
