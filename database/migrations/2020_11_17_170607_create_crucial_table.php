<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrucialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crucial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("assignment_id");
            $table->boolean("status");
            $table->timestamps();
            $table->foreign("assignment_id")->references("id")->on("assignment")->onCascade("delete");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crucial');
    }
}
