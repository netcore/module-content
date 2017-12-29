<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentMetaTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__meta_tags', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('entry_translation_id');
            $table->foreign('entry_translation_id')
                ->references('id')
                ->on('netcore_content__entry_translations')
                ->onDelete('cascade');

            $table->string('name')->default('');
            $table->string('property')->default('');
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('netcore_content__meta_tags');
    }
}
