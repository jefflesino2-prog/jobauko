<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('provider');
            $t->string('category');
            $t->string('location');
            $t->date('deadline');
            $t->string('eligibility');
            $t->text('description');
            $t->string('status')->default('published'); // published | unpublished | archived
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
