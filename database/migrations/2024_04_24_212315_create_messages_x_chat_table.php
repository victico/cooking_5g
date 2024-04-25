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
        Schema::create('messages_x_chat', function (Blueprint $table) {
            $table->id();
            $table->longText('message');
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->string('type_messages');
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('read');
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
        Schema::dropIfExists('messages_x_chat');
    }
};
