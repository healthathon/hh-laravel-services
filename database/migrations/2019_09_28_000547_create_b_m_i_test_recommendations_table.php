<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBMITestRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bmi_test_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("test_id")->unsigned()->comment("Reference to test id");
            $table->integer("answer_id")->unsigned()->comment("BMI Deviation Field Reference");
            $table->engine = "InnoDB";
            $table->foreign("test_id")
                ->references("id")
                ->on("labs_tests")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("answer_id")
                ->references("id")
                ->on("bmi_scores")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bmi_test_recommendations');
    }
}
