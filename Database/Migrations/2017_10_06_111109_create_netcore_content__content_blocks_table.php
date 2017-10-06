<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentContentBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__content_blocks', function (Blueprint $table) {
            
            $table->increments('id');

            $table->morphs('contentable', 'contentable_id_contentable_type_index');
            $table->string('widget');
            $table->text('data');
            $table->integer('order');

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
        Schema::dropIfExists('netcore_content__content_blocks');
    }
}
