<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('fuel_type')->nullable()->after('image_url');
            $table->string('transmission')->nullable()->after('fuel_type');
            $table->unsignedTinyInteger('seats')->nullable()->after('transmission');
            $table->unsignedInteger('mileage')->nullable()->after('seats')->comment('km per day, 0 = unlimited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['fuel_type', 'transmission', 'seats', 'mileage']);
        });
    }
};
