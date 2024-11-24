<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversation_member', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_conversation');
            $table->unsignedBigInteger('id_user');
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_conversation')->references('id')->on('conversation_ws')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation_member');
    }
};
