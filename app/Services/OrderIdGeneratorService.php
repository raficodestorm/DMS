<?php

namespace App\Services;

use App\Models\Order;
use Exception;

/**
 * OrderIdGeneratorService
 *
 * Production-grade service for generating unique, readable Order IDs.
 * Example format: BRS1358701 (Prefix 'BRS' + 7-digit unique number).
 */
class OrderIdGeneratorService
{
    /**
     * Default order ID prefix
     */
    public const DEFAULT_PREFIX = 'BRS';

    /**
     * Default number of digits
     */
    public const DEFAULT_DIGITS = 7;

    /**
     * Maximum retry attempts in case of collision
     */
    protected const MAX_ATTEMPTS = 15;

    /**
     * Generate a unique Order ID.
     *
     * @param string|null $prefix
     * @param int|null $digits
     * @return string
     * @throws Exception
     */
    public function generate(?string $prefix = null, ?int $digits = null): string
    {
        $prefix = $prefix ?? self::DEFAULT_PREFIX;
        $digits = $digits ?? self::DEFAULT_DIGITS;

        $min = (int) pow(10, $digits - 1);
        $max = (int) pow(10, $digits) - 1;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $randomNumber = random_int($min, $max);
            $candidateId = $prefix . $randomNumber;

            if (!Order::where('order_id', $candidateId)->exists()) {
                return $candidateId;
            }
        }

        // High-entropy fallback if max collision attempts reached
        $microTimePart = substr((string) (int) (microtime(true) * 1000), -5);
        $randomPart    = random_int(10, 99);
        $fallbackId    = $prefix . $microTimePart . $randomPart;

        if (!Order::where('order_id', $fallbackId)->exists()) {
            return $fallbackId;
        }

        // Final deterministic fallback
        return $prefix . time() . random_int(100, 999);
    }
}
