<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__channels', function (Blueprint $table) {

            $table->increments('id');

            $table->string('layout')->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('allow_attachments')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::create('netcore_content__channel_translations', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('channel_id');
            $table->foreign('channel_id')->references('id')->on('netcore_content__channels')->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('slug');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__channel_translations');
        Schema::dropIfExists('netcore_content__channels');
    }
}
