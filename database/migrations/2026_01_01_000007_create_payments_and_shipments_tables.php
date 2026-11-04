<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('transaction_reference', 100)->unique();
            $table->string('provider', 50)->default('cod');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');
            $table->enum('status', ['pending', 'authorized', 'captured', 'failed', 'refunded'])->default('pending')->index();
            $table->json('payload_json')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->enum('transaction_type', ['charge', 'refund', 'reversal', 'void']);
            $table->decimal('amount', 12, 2);
            $table->string('status', 30);
            $table->string('gateway_reference', 100)->nullable();
            $table->string('response_code', 50)->nullable();
            $table->text('response_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->index();
            $table->string('event_id', 100)->unique();
            $table->string('event_type', 100);
            $table->json('payload_json');
            $table->boolean('is_processed')->default(false)->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('carrier_name', 100)->nullable();
            $table->string('tracking_number', 100)->nullable()->index();
            $table->string('tracking_url', 255)->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('status', ['pending', 'booked', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('status', 50);
            $table->string('location', 150)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('event_time')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payment_webhook_events');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
    }
};
