<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNetcoreContentEntriesAddTypeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('netcore_content__entries', function (Blueprint $table) {
            $table->enum('type', [
                'revision',
                'preview',
                'draft',
                'active'
            ])
                ->default('active')
                ->after('layout');

            $table->unsignedInteger('parent_id')->after('id');
            $table->foreign('parent_id')->references('id')->on('netcore_content__entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('netcore_content__entries', function (Blueprint $table) {
            $table->dropColumn('type');

            $table->dropForeign('netcore_content__entries_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
}
