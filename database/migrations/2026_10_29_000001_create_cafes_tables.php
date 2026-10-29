<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Official Kemendagri/BPS kode wilayah, levels 1-2 only (provinsi +
        // kabupaten/kota, ~552 rows) — all the cascade needs. Kecamatan level
        // lives in cafe_areas (OSM place nodes), not here.
        Schema::create('wilayah', function (Blueprint $table) {
            $table->string('kode', 10)->primary();       // '31', '31.74'
            $table->string('nama');
            $table->string('level', 20);                  // 'provinsi' | 'kabupaten_kota'
            $table->string('parent_kode', 10)->nullable(); // provinsi kode for kab/kota
            $table->string('osm_area_id', 20)->nullable(); // resolved Overpass area id
            $table->timestamps();
        });

        Schema::create('cafes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('osm_id');
            $table->string('osm_type', 16);       // node | way | relation
            $table->string('name');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('region_kode', 10);    // kode wilayah of the imported region
            $table->string('area_name')->nullable(); // nearest cafe_areas name (1km), or null
            $table->text('opening_hours')->nullable(); // raw OSM opening_hours tag
            $table->string('addr_street')->nullable();
            $table->string('addr_housenumber')->nullable();
            $table->string('addr_city')->nullable();
            $table->json('tags')->nullable();     // full raw OSM tags (audit trail)
            $table->timestamp('osm_timestamp')->nullable(); // OSM's own last edit (shown to users)
            $table->timestamp('last_seen_at')->nullable();  // our import run
            $table->boolean('is_missing')->default(false);  // flagged, never deleted
            $table->timestamps();

            // Composite: node 1 and way 1 are distinct OSM elements.
            $table->unique(['osm_id', 'osm_type']);
            $table->index(['region_kode', 'is_missing']);
            $table->index(['region_kode', 'area_name']);
        });

        // OSM place nodes per region (suburb/neighbourhood/village/...) — the
        // in-map area dropdown's backbone, plus curated alias rows (is_alias).
        Schema::create('cafe_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('osm_id')->nullable(); // null for aliases
            $table->string('name');
            $table->string('place_type', 32)->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('region_kode', 10);
            $table->boolean('is_alias')->default(false);
            $table->timestamps();

            $table->unique(['osm_id', 'region_kode']);
            $table->index('region_kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cafe_areas');
        Schema::dropIfExists('cafes');
        Schema::dropIfExists('wilayah');
    }
};
