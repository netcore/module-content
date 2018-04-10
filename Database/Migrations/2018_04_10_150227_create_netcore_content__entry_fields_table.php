<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentEntryFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__entry_fields', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('entry_id');
            $table->foreign('entry_id', 'field_entry_id')->references('id')->on('netcore_content__entries')->onDelete('cascade');

            $table->string('key');
            $table->longText('value')->nullable();

            // Stapler fields
            $table->string('image_file_name')->nullable();
            $table->integer('image_file_size')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamp('image_updated_at')->nullable();
            $table->index(['key']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__entry_fields');
    }
}
