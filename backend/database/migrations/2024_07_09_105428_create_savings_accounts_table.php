<?php

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
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('account_name');
            $table->double('balance');
            $table->boolean('is_account_blocked');
            $table->string('account_number')->unique();
            $table->float('monthly_interest');
            $table->float('monthly_maintenance_fee');
            $table->float('transaction_fee');
            $table->timestamp('last_interest_paied_at')->nullable();
            $table->timestamp('last_monthly_fee_paid_at')->nullable();
            $table->double('minimum_balance');
            $table->smallInteger('max_amount_of_transactions_monthly');
            $table->timestamp('last_avaible_transaction_date')->nullable();
            $table->float('limit_exceeding_fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};
