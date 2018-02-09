<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentContentBlockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__content_block_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('content_block_id');
            $table->foreign('content_block_id', 'content_block_id')->references('id')->on('netcore_content__content_blocks')->onDelete('cascade');

            $table->string('key');
            $table->longText('value')->nullable();

            // Stapler fields
            $table->string('image_file_name')->nullable();
            $table->integer('image_file_size')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamp('image_updated_at')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('netcore_content__content_block_items');
    }
}
