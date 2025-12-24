<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->string('site_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('wordpress_version')->nullable();
            $table->string('plugin_version')->nullable();
            $table->timestamp('activated_at');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['license_id', 'domain']);
            $table->index('domain');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('license_activations');
    }
};
