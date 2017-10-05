<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetcoreHtmlBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore__html_blocks', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('page_id');
            $table->foreign('page_id')->references('id')->on('netcore__pages')->onDelete('cascade');
        });
        
        Schema::create('netcore__html_block_translations', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('html_block_id');
            $table->foreign('html_block_id')->references('id')->on('netcore__html_blocks')->onDelete('cascade');

            $table->string('locale')->index();
            
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore__html_block_translations');
        Schema::dropIfExists('netcore__html_blocks');
    }
}
