<?php

namespace App\Services;

use App\Models\Deduction;
use App\Models\StockInItem;

/**
 * PurchasePriceCalculator
 *
 * Centralized, reusable service for computing a product's
 * purchase price based on the company deduction policy.
 *
 * Formula:
 *   Step 1 → base_price         = product.price
 *   Step 2 → after_customer_cut = base_price - (customer_deduction% of base_price)
 *   Step 3 → after_tree_cut     = after_customer_cut - tree_deduction (flat amount from stock item)
 *   Step 4 → purchase_price     = after_tree_cut - (my_deduction% of after_tree_cut)
 */
class PurchasePriceCalculator
{
    /**
     * Compute the final purchase price for a single StockInItem.
     *
     * All deductions are PERCENTAGES applied sequentially:
     *
     *   Step 1 → base_price         = product.price
     *   Step 2 → after_customer_cut = base_price × (1 - customer_deduction / 100)
     *   Step 3 → after_tree_cut     = after_customer_cut × (1 - tree_deduction / 100)
     *   Step 4 → purchase_price     = after_tree_cut × (1 - my_deduction / 100)
     *
     * @param  StockInItem  $item       Must have product relation eager-loaded.
     * @param  Deduction    $deduction  The active deduction policy (type = 'main').
     * @return float
     * @throws \RuntimeException If calculated purchase price would be negative.
     */
    public function calculate(StockInItem $item, Deduction $deduction): float
    {
        $basePrice = (float) $item->product->price;

        // Step 2: customer_deduction %
        $customerPct = max(0.0, (float) ($deduction->customer_deduction ?? 0));
        $afterCustomer = $basePrice * (1 - $customerPct / 100);

        // Step 3: tree_deduction % (stored per stock-in item, can vary per product/batch)
        $treePct = max(0.0, (float) ($item->tree_deduction ?? 0));
        $afterTree = $afterCustomer * (1 - $treePct / 100);

        // Step 4: my_deduction %
        $myPct = max(0.0, (float) ($deduction->my_deduction ?? 0));
        $purchasePrice = $afterTree * (1 - $myPct / 100);

        if ($purchasePrice < 0) {
            throw new \RuntimeException(
                "Purchase price for product [{$item->product->name}] cannot be negative. " .
                "Calculated: {$purchasePrice}. Check your deduction percentages."
            );
        }

        return round($purchasePrice, 2);
    }

}
