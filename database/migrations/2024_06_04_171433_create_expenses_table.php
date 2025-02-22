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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('number', 32);
            $table->date('tgl');
            $table->foreignUuid('contact_id')->index()->nullable()->unsigned()->constrained();
            $table->foreignUuid('asset_id')->index()->nullable()->unsigned()->constrained();
            $table->foreignUuid('expense_category_id')->index()->unsigned()->constrained();
            $table->decimal('nominal', 12)->default(0);
            $table->foreignUuid('payment_method_id')->index()->unsigned()->constrained();
            $table->text('description')->nullable();
            $table->text('images')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('expense_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_tenant');
        Schema::dropIfExists('expenses');
    }
};
