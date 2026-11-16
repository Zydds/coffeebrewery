<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->json('recommended_methods')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->dropColumn('recommended_methods');
        });
    }
};
