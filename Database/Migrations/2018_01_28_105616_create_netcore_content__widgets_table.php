<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreContentWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_content__widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('key');
            $table->boolean('is_enabled')->default(1);
            $table->text('data')->nullable();
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
        Schema::dropIfExists('netcore_content__widgets');
    }
}
