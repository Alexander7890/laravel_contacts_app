<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 180);
            $table->string('phone', 50)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();

            $table->foreign('group_id')
                ->references('id')
                ->on('contact_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
