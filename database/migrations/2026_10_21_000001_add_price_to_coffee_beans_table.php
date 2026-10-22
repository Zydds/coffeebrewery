<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('body_level');
        });
    }

    public function down(): void
    {
        Schema::table('coffee_beans', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
