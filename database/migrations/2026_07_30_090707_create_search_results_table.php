<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_batch_id')->constrained()->cascadeOnDelete();
            $table->string('query');
            $table->unsignedInteger('position')->nullable();
            $table->string('title')->nullable();
            $table->text('link')->nullable();
            $table->text('snippet')->nullable();
            $table->string('displayed_link')->nullable();
            $table->timestamps();

            $table->index(['search_batch_id', 'query']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_results');
    }
};
