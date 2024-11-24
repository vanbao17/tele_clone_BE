<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_account1');
            $table->unsignedBigInteger('id_account2');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_account1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_account2')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation');
    }
};
