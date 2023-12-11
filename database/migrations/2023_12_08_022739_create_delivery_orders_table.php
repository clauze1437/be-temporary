<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admin_id')->unique()->constrained('users')->cascadeOnUpdate();
            $table->foreignUuid('driver_id')->unique()->constrained('users')->cascadeOnUpdate();
            $table->string('origin_location_name');
            $table->string('destination_location_name');
            $table->foreignId('vehicle_id')->unique()->constrained('vehicles')->cascadeOnUpdate();
            $table->text('type_of_load');
            $table->integer('initial_tonnage');
            $table->integer('final_tonnage');
            $table->text('information');
            $table->enum('status', ['berlangsung', 'terkirim', 'selesai'])->default('berlangsung');
            $table->string('image_proof_of_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
};
