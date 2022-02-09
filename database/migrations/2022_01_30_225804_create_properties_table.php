<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('rooms')->nullable();
            $table->text('description')->nullable();
            $table->string('surface')->nullable();
            $table->string('location')->nullable();
            $table->integer('hosting_capacity')->nullable();
            $table->json("equipments")->nullable();
            $table->json("images")->nullable();
            $table->json('dynamic_attributes')->nullable();
            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('reviews')->nullable();
            $table->float('price')->nullable();
            $table->boolean('is_activated')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
