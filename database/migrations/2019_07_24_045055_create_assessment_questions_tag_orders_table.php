<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentQuestionsTagOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_questions_tag_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_seq')->nullable(false)->comment('Order of Question Decided by Admin');
            $table->boolean('is_active')->default(0)->comment("Is Current Order Sequence should be followed");
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
        Schema::dropIfExists('assessment_questions_tag_orders');
    }
}
