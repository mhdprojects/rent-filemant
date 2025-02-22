<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->string('number', 32);
            $table->date('tgl');
            $table->time('jam');
            $table->foreignUuid('contact_id')->index()->unsigned()->constrained();
            $table->string('status',32)->default(\App\Enum\OrderStatus::New);
            $table->decimal('subtotal', 12)->default(0);
            $table->string('payment_status', 32)->default(\App\Enum\PaymentStatus::Unpaid);
            $table->decimal('paid', 12)->default(0);
            $table->decimal('kurang', 12)->default(0);
            $table->text('description')->nullable();
            $table->foreignUuid('user_id')->index()->unsigned()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->index()->unsigned()->constrained();
            $table->foreignUuid('order_id')->index()->unsigned()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_tenant');
        Schema::dropIfExists('orders');
    }
};
