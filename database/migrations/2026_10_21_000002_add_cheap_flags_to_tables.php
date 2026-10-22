<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->boolean('is_cheap')->default(false)->after('price');
            $table->string('price_unit', 30)->default('100 gr')->after('price');
        });

        Schema::table('brew_methods', function (Blueprint $table) {
            $table->boolean('is_cheap')->default(false)->after('description');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->boolean('cheap')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->dropColumn(['is_cheap', 'price_unit']);
        });

        Schema::table('brew_methods', function (Blueprint $table) {
            $table->dropColumn('is_cheap');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('cheap');
        });
    }
};
