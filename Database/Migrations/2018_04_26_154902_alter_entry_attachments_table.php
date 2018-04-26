<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEntryAttachmentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('netcore_content__entry_attachments', function (Blueprint $table) {
            $table->boolean('is_featured')->default(0)->after('entry_id');
            $table->text('media')->nullable()->after('is_featured');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('netcore_content__entry_attachments', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'media']);
        });
    }
}
