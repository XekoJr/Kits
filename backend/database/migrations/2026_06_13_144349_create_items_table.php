<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->boolean('purchased')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
