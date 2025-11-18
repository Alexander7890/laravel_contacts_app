<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
        });

        DB::table('contact_groups')->insert([
            ['name' => 'Work'],
            ['name' => 'Friends'],
            ['name' => 'Family'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_groups');
    }
};
