<?php

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->string('status')->default(OrderStatus::FINISHED);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Address::class, 'shipping_address_id')->nullable()->constrained('addresses')->cascadeOnDelete();
            $table->foreignIdFor(Address::class, 'invoice_address_id')->nullable()->constrained('addresses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
