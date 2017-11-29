<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentImageBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__image_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('netcore_content__image_block_items', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('image_block_id');
            $table->foreign('image_block_id')->references('id')->on('netcore_content__image_blocks')->onDelete('cascade');

            // Stapler fields
            $table->string('image_file_name')->nullable();
            $table->integer('image_file_size')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamp('image_updated_at')->nullable();

            $table->integer('order')->index();
        });

        Schema::create('netcore_content__image_block_item_fields', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('image_block_item_id');
            $table->foreign('image_block_item_id', 'item_id_foreign')->references('id')->on('netcore_content__image_block_items')->onDelete('cascade');

            $table->string('key');
            $table->longText('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__image_block_item_fields');
        Schema::dropIfExists('netcore_content__image_block_items');

        Schema::dropIfExists('netcore_content__image_blocks');
    }
}
