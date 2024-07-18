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
            $table->float('monthly_interest');
            $table->float('monthly_maintenance_fee');
            $table->float('transaction_fee');
            $table->timestamp('last_interest_paied_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('last_monthly_fee_paid_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->double('minimum_balance');
            $table->smallInteger('max_amount_of_transactions_monthly');
            $table->timestamp('last_avaible_transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->float('limit_exceeding_fee');
            $table->timestamp('account_exceeded_min_blance_at')->nullable();
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
