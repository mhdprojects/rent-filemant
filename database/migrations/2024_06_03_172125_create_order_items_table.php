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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->index()->unsigned()->constrained();
            $table->foreignUuid('asset_id')->index()->unsigned()->constrained();
            $table->foreignUuid('asset_variant_id')->index()->unsigned()->constrained();
            $table->decimal('duration', 12)->default(0);
            $table->string('period_in', 30);
            $table->decimal('qty', 12)->default(0);
            $table->decimal('price', 12)->default(0);
            $table->decimal('subtotal', 12)->default(0);
            $table->date('start_date')->nullable();
            $table->date('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->date('end_time')->nullable();
            $table->decimal('terlambat', 12)->default(0); //dalam jam
            $table->decimal('denda', 12)->default(0);
            $table->decimal('total_denda', 12)->default(0);
            $table->decimal('total', 12)->default(0);
            $table->boolean('sudah_kembali')->default(false);
            $table->timestamp('tgl_kembali')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
