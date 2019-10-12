<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosticLabsInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnostic_labs_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', '255')->nullable(false)->comment("Lab Name");
            $table->string('description', '255')->nullable(true)->comment("Lab basic Information");
            $table->text('address')->nullable(true);
            // Json because using already developed structure is developed by Thyrocare Api
            // TODO : In future may create separate table for all labs and link each test details with respective labs
            $table->json('details')->nullable(true)->comment("Information Regarding Tests and many more");
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
        Schema::dropIfExists('diagnostic_labs_informations');
    }
}
