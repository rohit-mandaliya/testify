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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('slug', 50)->unique('UQ_Settings_Slug');
            $table->text('value');
            $table->boolean('is_required')->comment("0=>Not Required 1=>Required")->default(1);
            $table->boolean('is_active')->comment("0=>Inactive 1=>Active")->default(1);
            $table->string('group', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
