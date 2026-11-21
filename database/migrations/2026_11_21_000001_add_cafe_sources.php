<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->string('source', 10)->default('osm')->after('id');
            $table->unsignedBigInteger('osm_id')->nullable()->change();
            $table->string('osm_type', 16)->nullable()->change();
            $table->index(['source', 'region_kode']);
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropIndex(['cafes_source_region_kode_index']);
            $table->dropColumn('source');
            $table->unsignedBigInteger('osm_id')->nullable(false)->change();
            $table->string('osm_type', 16)->nullable(false)->change();
        });
    }
};
