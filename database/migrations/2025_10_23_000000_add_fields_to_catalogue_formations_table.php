<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalogue_formations', function (Blueprint $table) {
            $table->text('objectifs')->nullable();
            $table->text('programme')->nullable();
            $table->text('modalites')->nullable();
            $table->text('modalites_accompagnement')->nullable();
            $table->text('moyens_pedagogiques')->nullable();
            $table->text('modalites_suivi')->nullable();
            $table->text('evaluation')->nullable();
            $table->string('lieu')->nullable();
            $table->string('niveau')->nullable();
            $table->string('public_cible')->nullable();
            $table->integer('nombre_participants')->nullable();
        });
    }

    public function down()
    {
        Schema::table('catalogue_formations', function (Blueprint $table) {
            $table->dropColumn([
                'objectifs',
                'programme',
                'modalites',
                'modalites_accompagnement',
                'moyens_pedagogiques',
                'modalites_suivi',
                'evaluation',
                'lieu',
                'niveau',
                'public_cible',
                'nombre_participants',
            ]);
        });
    }
};
