<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetcorePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore__pages', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_active')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('netcore__page_translations', function (Blueprint $table) {
            
            $table->increments('id');
            
            $table->unsignedInteger('page_id');
            $table->foreign('page_id')->references('id')->on('netcore__pages')->onDelete('cascade');
            
            $table->string('locale')->index();

            // Fields that are translatable..
            $table->string('title');
            $table->string('slug');
            $table->text('content');

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
        Schema::dropIfExists('netcore__page_translations');
        Schema::dropIfExists('netcore__pages');
    }
}
