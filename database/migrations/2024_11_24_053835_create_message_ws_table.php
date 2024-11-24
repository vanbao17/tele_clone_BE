<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageWsTable extends Migration
{
    public function up()
    {
        Schema::create('message_ws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_conversation_ws')->constrained('conversation_ws')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('content_type', ['text', 'mention']);
            $table->text('content');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_ws');
    }
}
