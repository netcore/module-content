<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * - Site pages
         *   - static pages
         *
         * - Company
         *   - news
         *   - blog
         *   - bonds
         *
         * - Additional
         *   - Partner logos
         *   - Team
         *   - Testimonials
         * */
        Schema::create('content__sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        /*
         * - Static pages
         * - News
         * - Blogs
         * - Vacancies
         * */
        Schema::create('content__channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('section_id');
            $table->timestamps();
        });

        /*
         * real content, news, pages, custom pages
         * */
        Schema::create('content__entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });


        //default fieldi
        Schema::create('content__defaults', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });


        //pievienotie fieldi
        Schema::create('content__fields', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        /*
         * katra lietotāja uzstādījumi
         * */
        Schema::create('content__tableviews', function (Blueprint $table) {
            $table->increments('id');
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
        Schema::dropIfExists('fields');
    }
}
