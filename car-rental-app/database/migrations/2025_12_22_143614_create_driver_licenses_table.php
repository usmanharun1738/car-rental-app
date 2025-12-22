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
        Schema::create('driver_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('license_number')->unique();
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('license_class')->default('E'); // Nigerian license classes
            $table->string('sex', 1)->default('M');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('issuing_authority')->default('FRSC');
            $table->string('state_of_issue');
            $table->string('front_image_path')->nullable();
            $table->string('back_image_path')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected, expired
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_licenses');
    }
};
