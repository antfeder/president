<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_survey', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained();
            $table->foreignId('survey_id')->constrained();
            $table->timestamps();
            $table->integer('stat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates_surveys');
    }
};
