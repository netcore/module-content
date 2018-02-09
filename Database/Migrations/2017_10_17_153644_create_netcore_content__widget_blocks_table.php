<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentWidgetBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__widget_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('netcore_content__widget_block_fields', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('widget_block_id');
            $table->foreign('widget_block_id', 'block_id_foreign')->references('id')->on('netcore_content__widget_blocks')->onDelete('cascade');

            $table->string('key');
            $table->longText('value')->nullable();

            // Stapler fields
            $table->string('image_file_name')->nullable();
            $table->integer('image_file_size')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamp('image_updated_at')->nullable();

            $table->index(['key']);

        });

        Schema::create('netcore_content__widget_block_items', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('widget_block_id');
            $table->foreign('widget_block_id')->references('id')->on('netcore_content__widget_blocks')->onDelete('cascade');

            $table->integer('order')->index();
        });

        Schema::create('netcore_content__widget_block_item_fields', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('widget_block_item_id')->nullable();
            $table->foreign('widget_block_item_id', 'item_id_foreign')->references('id')->on('netcore_content__widget_block_items')->onDelete('cascade');

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
        Schema::dropIfExists('netcore_content__widget_block_item_fields');
        Schema::dropIfExists('netcore_content__widget_block_items');

        Schema::dropIfExists('netcore_content__widget_block_fields');
        Schema::dropIfExists('netcore_content__widget_blocks');
    }
}
