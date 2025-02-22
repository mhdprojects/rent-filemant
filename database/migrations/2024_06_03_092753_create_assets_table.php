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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('name', 200);
            $table->foreignUuid('brand_id')->index()->unsigned()->constrained()->nullOnDelete();
            $table->foreignUuid('type_model_id')->index()->unsigned()->constrained()->nullOnDelete();
            $table->string('warna', 30)->nullable();
            $table->string('tahun', 4);
            $table->boolean('is_partner')->default(false);
            $table->foreignUuid('contact_id')->nullable()->unsigned()->constrained()->nullOnDelete();
            $table->decimal('stock', 12)->default(0);
            $table->text('description')->nullable();
            $table->text('images')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('asset_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_tenant');
        Schema::dropIfExists('assets');
    }
};
