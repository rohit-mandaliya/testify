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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->foreignId('project_id');
            $table->foreignId('folder_id');
            $table->string('name', 50);
            $table->string('title', 155);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->comment("0=>Closed 1=>Open 2=>In Progress 3=>Fixed 4=>Reopened 5=>Intended")->default(1);
            $table->tinyInteger('type')->comment("1=>UI/UX 2=>Functional 3=>Suggestion");
            $table->tinyInteger('priority')->comment("1=>Low 2=>Medium 3=>High 4=>Critical");
            $table->string('app_version', 20)->nullable();
            $table->date('due_date')->nullable();
            $table->json('assignee')->nullable();
            $table->tinyInteger('is_active')->comment("0=>Inactive 1=>Active")->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
