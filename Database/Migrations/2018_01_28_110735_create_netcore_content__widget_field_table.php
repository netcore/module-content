<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentWidgetFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__widget_field', function (Blueprint $table) {
            $table->unsignedInteger('field_id');
            $table->foreign('field_id')->references('id')->on('netcore_content__fields')->onDelete('cascade');

            $table->unsignedInteger('widget_id');
            $table->foreign('widget_id')->references('id')->on('netcore_content__widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_content__widget_field');
    }
}
