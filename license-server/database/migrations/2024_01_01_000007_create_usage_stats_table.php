<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usage_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('uploads_count')->default(0);
            $table->bigInteger('total_size')->default(0); // in bytes
            $table->integer('api_calls')->default(0);
            $table->timestamps();
            
            $table->unique(['license_id', 'date']);
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usage_stats');
    }
};
