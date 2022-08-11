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
        Schema::create('todonotes', function (Blueprint $table) {
            $table->increments('id'); // primary key
            $table->integer('user_id');
            $table->text('note');
            $table->integer('status')->default(null)->nullable();
            $table->timestamp('completed_at')->default(null)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todonotes');
    }
};
