<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentChannelFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__channel_field', function (Blueprint $table) {
            $table->unsignedInteger('channel_id');
            $table->foreign('channel_id')->references('id')->on('netcore_content__channels')->onDelete('cascade');

            $table->unsignedInteger('field_id');
            $table->foreign('field_id')->references('id')->on('netcore_content__fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__channel_field');
    }
}
