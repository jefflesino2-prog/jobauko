<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('contact');
            $t->string('barangay');
            $t->text('message')->nullable();
            $t->string('status')->default('Submitted');
            $t->timestamps();
            $t->unique(['user_id', 'opportunity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
