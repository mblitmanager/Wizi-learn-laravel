<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            if (!Schema::hasColumn('media', 'size')) {
                $table->bigInteger('size')->nullable()->after('url');
            }
            if (!Schema::hasColumn('media', 'mime')) {
                $table->string('mime')->nullable()->after('size');
            }
            if (!Schema::hasColumn('media', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable()->after('mime');
                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'uploaded_by')) {
                $table->dropForeign(['uploaded_by']);
                $table->dropColumn('uploaded_by');
            }
            if (Schema::hasColumn('media', 'mime')) {
                $table->dropColumn('mime');
            }
            if (Schema::hasColumn('media', 'size')) {
                $table->dropColumn('size');
            }
        });
    }
};
