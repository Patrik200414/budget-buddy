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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactionable');
            $table->double('amount');
            $table->timestamp('transaction_time');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('transaction_subcategory_id');
            $table->foreign('transaction_subcategory_id')->references('id')->on('transaction_subcategories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
