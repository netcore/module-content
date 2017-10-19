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
        /**
         *  image_blocks and their translations
         */
        Schema::create('netcore_content__image_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('netcore_content__image_block_translations', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('image_block_id');
            $table->foreign('image_block_id')->references('id')->on('netcore_content__image_blocks')->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('title');
        });

        /**
         *  image_block_items and their translations
         */
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

        Schema::create('netcore_content__image_block_item_translations', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('image_block_item_id');
            $table->foreign('image_block_item_id', 'image_block_item_id_foreign')->references('id')->on('netcore_content__image_block_items')->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('title')->default('');
            $table->string('subtitle')->default('');
            $table->text('content')->nullable();
            $table->string('link')->default('');
            $table->text('json')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__image_block_item_translations');
        Schema::dropIfExists('netcore_content__image_block_items');

        Schema::dropIfExists('netcore_content__image_block_translations');
        Schema::dropIfExists('netcore_content__image_blocks');
    }
}
