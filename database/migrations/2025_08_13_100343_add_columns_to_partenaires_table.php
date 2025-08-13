<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('partenaires', function (Blueprint $table) {
            // $table->string('logo')->nullable()->after('type');
            if (!Schema::hasColumn('partenaires', 'contacts')) {
                $table->json('contacts')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('partenaires', 'actif')) {
                $table->boolean('actif')->default(true)->after('contacts');
            }
        });
    }

    public function down()
    {
        Schema::table('partenaires', function (Blueprint $table) {
            if (Schema::hasColumn('partenaires', 'contacts')) {
                $table->dropColumn('contacts');
            }
            if (Schema::hasColumn('partenaires', 'actif')) {
                $table->dropColumn('actif');
            }
            // $table->dropColumn('logo');
        });
    }
};
