<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetcorePageWidget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore__page_widget', function (Blueprint $table) {

            $table->unsignedInteger('page_id');
            $table->foreign('page_id')->references('id')->on('netcore__pages')->onDelete('cascade');

            $table->integer('widgetable_id')->unsigned();
            $table->string('widgetable_type');
            
            $table->integer('order');

            $table->unique([
                'page_id',
                'widgetable_type',
                'widgetable_id',
                'order'
            ], 'page_widget_order_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore__page_widget');
    }
}
