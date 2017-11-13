<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__entries', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('channel_id')->unsigned()->nullable();
            $table->foreign('channel_id')->references('id')->on('netcore_content__channels');

            $table->string('layout')->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_homepage')->default(0);

            // Stapler fields
            $table->string('attachment_file_name')->nullable();
            $table->integer('attachment_file_size')->nullable();
            $table->string('attachment_content_type')->nullable();
            $table->timestamp('attachment_updated_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('netcore_content__entry_translations', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('entry_id');
            $table->foreign('entry_id')->references('id')->on('netcore_content__entries')->onDelete('cascade');

            $table->string('title')->default('');
            $table->string('locale')->index();
            $table->mediumText('content')->nullable();

            $table->string('slug')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__entry_translations');
        Schema::dropIfExists('netcore_content__entries');
    }
}
