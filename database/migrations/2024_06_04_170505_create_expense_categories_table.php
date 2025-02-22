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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_category_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('expense_category_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_category_tenant');
        Schema::dropIfExists('expense_categories');
    }
};
