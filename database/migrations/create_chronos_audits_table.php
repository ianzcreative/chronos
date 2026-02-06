<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chronos_audits', function (Blueprint $table) {
            $table->id();
            $table->string('event'); // created, updated, deleted, restored
            $table->morphs('auditable'); // Model ID & Type
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); 
            
            $table->timestamps();
            
            $table->index(['auditable_type', 'auditable_id'],'auditable_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chronos_audits');
    }
};
