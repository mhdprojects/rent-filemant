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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('order_id')->index()->unsigned()->constrained();
            $table->string('number', 32);
            $table->date('tgl');
            $table->foreignUuid('payment_method_id')->index()->unsigned()->constrained();
            $table->decimal('nominal', 12)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_payment_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('order_payment_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment_tenant');
        Schema::dropIfExists('order_payments');
    }
};
