<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('name', 150);
            $table->string('email')->nullable();
            $table->string('phone', 20);
            $table->string('alamat')->nullable();
            $table->boolean('is_customer')->default(false);
            $table->boolean('is_partner')->default(false);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contact_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('contact_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_tenant');
        Schema::dropIfExists('contacts');
    }
};
