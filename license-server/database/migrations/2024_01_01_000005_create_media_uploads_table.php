<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->foreignId('license_activation_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->bigInteger('attachment_id')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->default(0); // in bytes
            $table->string('s3_key')->nullable();
            $table->string('s3_url')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
            
            $table->index('license_id');
            $table->index('domain');
            $table->index('uploaded_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_uploads');
    }
};
