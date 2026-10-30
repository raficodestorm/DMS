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
        Schema::create('daily_cash_closings', function (Blueprint $table) {

            $table->id();

            /*
             * ---------------------------------------------------------
             * Branch
             * ---------------------------------------------------------
             */
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
             * Business date of this cash closing.
             *
             * Only one closing should exist for a branch per date.
             */
            $table->date('closing_date');

            /*
             * ---------------------------------------------------------
             * Cash Reconciliation
             * ---------------------------------------------------------
             */

            // Cash available at the beginning of the business day.
            $table->decimal('opening_cash', 15, 2)
                ->default(0);

            // Total cash received during the day.
            $table->decimal('cash_in', 15, 2)
                ->default(0);

            // Total cash paid/withdrawn during the day.
            $table->decimal('cash_out', 15, 2)
                ->default(0);

            // System-calculated expected cash at closing.
            $table->decimal('expected_cash', 15, 2)
                ->default(0);

            // Physically counted cash at the time of closing.
            $table->decimal('actual_cash', 15, 2)
                ->default(0);

            /*
             * Difference:
             *
             * actual_cash - expected_cash
             *
             * Positive = excess
             * Negative = shortage
             * Zero    = balanced
             */
            $table->decimal('cash_variance', 15, 2)
                ->default(0);


            

            /*
             * ---------------------------------------------------------
             * Closing Information
             * ---------------------------------------------------------
             */

            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('closed_at')
                ->nullable();

            /*
             * ---------------------------------------------------------
             * Status
             * ---------------------------------------------------------
             *
             * open      = closing has not been finalized
             * closed    = finalized successfully
             * reopened  = previously closed but reopened for correction
             */
            $table->string('status', 20)
                ->default('open');

            /*
             * Optional explanation for:
             * - shortage
             * - excess
             * - manual adjustment
             * - reopening
             */
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
             * ---------------------------------------------------------
             * Constraints & Indexes
             * ---------------------------------------------------------
             */

            // One daily closing per branch per business date.
            $table->unique(
                ['branch_id', 'closing_date'],
                'daily_cash_closings_branch_date_unique'
            );

            // Useful for branch/date reporting.
            $table->index(
                ['branch_id', 'status'],
                'daily_cash_closings_branch_status_index'
            );

            // Useful for date-range reports.
            $table->index(
                'closing_date',
                'daily_cash_closings_date_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cash_closings');
    }
};