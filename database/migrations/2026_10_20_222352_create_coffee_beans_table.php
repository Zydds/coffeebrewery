<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coffee_beans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('origin_country');
            $table->smallInteger('acidity_level');
            $table->smallInteger('body_level');
            $table->json('flavor_notes');
            $table->json('recommended_drinks');
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable();
            $table->timestamps();
        });

        // CHECK constraints are also enforced in Form Requests validation;
        // PostgreSQL ADD CONSTRAINT syntax works only on pgsql, not SQLite.
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE coffee_beans ADD CONSTRAINT chk_acidity_level CHECK (acidity_level >= 1 AND acidity_level <= 5)');
            DB::statement('ALTER TABLE coffee_beans ADD CONSTRAINT chk_body_level CHECK (body_level >= 1 AND body_level <= 5)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coffee_beans');
    }
};
