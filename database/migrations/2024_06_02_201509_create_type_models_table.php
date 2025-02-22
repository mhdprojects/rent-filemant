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
        Schema::create('type_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('name', 100);
            $table->foreignUuid('brand_id')->index()->unsigned()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenant_type_model', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('type_model_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_type_model');
        Schema::dropIfExists('type_models');
    }
};
