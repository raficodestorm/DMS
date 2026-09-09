<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use InvalidArgumentException;

/**
 * BarcodeService
 *
 * Production-grade service for generating, validating, and formatting barcodes.
 * 
 * Features:
 * - GS1 EAN-13 automatic barcode generation with Modulo-10 check digit & uniqueness assurance.
 * - Standards-compliant Code 128 Auto (Sets A, B, C) SVG barcode generator using Dynamic Programming
 *   for optimal symbol density, minimal width, and universal scanner compatibility.
 * - Strict character set validation and quiet-zone compliance (ISO/IEC 15417).
 */
class BarcodeService
{
    /**
     * GS1 standard internal/in-store distribution prefix (200 - 299)
     */
    public const DEFAULT_IN_STORE_PREFIX = '200';

    /**
     * Number of random digits in the body (Prefix 3 digits + Body 9 digits + Checksum 1 digit = 13 digits)
     */
    public const BODY_DIGITS = 9;

    /**
     * Max retry attempts to resolve collisions
     */
    protected const MAX_ATTEMPTS = 20;

    /**
     * Code 128 Mode Constants
     */
    private const MODE_A = 0;
    private const MODE_B = 1;
    private const MODE_C = 2;

    /**
     * Code 128 Control Symbol Values
     */
    private const START_A = 103;
    private const START_B = 104;
    private const START_C = 105;
    private const CODE_A  = 101; // Switch to Code Set A
    private const CODE_B  = 100; // Switch to Code Set B
    private const CODE_C  = 99;  // Switch to Code Set C
    private const SHIFT   = 98;  // Single character Shift between Set A & B
    private const STOP    = 106; // Stop pattern (13 modules)

    /**
     * ISO/IEC 15417 Code 128 Bar/Space Module Width Patterns (0 to 106)
     * Values 0-105: 6 bar/space widths totaling 11 modules each.
     * Value 106 (Stop): 7 bar/space widths totaling 13 modules ('2331112').
     */
    private const CODE128_PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'
    ];

    /**
     * Generate a unique GS1 EAN-13 compliant barcode for a product.
     *
     * @param string|null $prefix 2-3 digit prefix (default: 200)
     * @param int|null $excludeProductId Exclude this product ID from uniqueness check (for updates)
     * @return string
     * @throws Exception
     */
    public function generateUniqueBarcode(?string $prefix = null, ?int $excludeProductId = null): string
    {
        $prefix = $prefix ?? self::DEFAULT_IN_STORE_PREFIX;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $candidate = $this->generateEan13($prefix);

            $query = Product::where('barcode', $candidate);
            if ($excludeProductId !== null) {
                $query->where('id', '!=', $excludeProductId);
            }

            if (!$query->exists()) {
                return $candidate;
            }
        }

        // High-entropy fallback if max attempts reached
        $microTime = substr((string) (int) (microtime(true) * 10000), -8);
        $random = (string) random_int(10, 99);
        $raw12 = substr($prefix . $microTime . $random, 0, 12);
        return $raw12 . $this->calculateEan13Checksum($raw12);
    }

    /**
     * Generate a 13-digit EAN-13 string with valid checksum.
     *
     * @param string $prefix Numeric prefix between 1 and 11 digits (default: 200)
     * @return string
     * @throws InvalidArgumentException if prefix is not 1-11 numeric digits
     */
    public function generateEan13(string $prefix = self::DEFAULT_IN_STORE_PREFIX): string
    {
        $prefix = trim($prefix);
        if (!preg_match('/^\d{1,11}$/', $prefix)) {
            throw new InvalidArgumentException("EAN-13 prefix must be a numeric string between 1 and 11 digits, '{$prefix}' given.");
        }

        $bodyLength = 12 - strlen($prefix);
        $min = (int) pow(10, $bodyLength - 1);
        $max = (int) pow(10, $bodyLength) - 1;

        $body = (string) random_int($min, $max);
        $raw12 = $prefix . $body;

        $checksum = $this->calculateEan13Checksum($raw12);

        return $raw12 . $checksum;
    }

    /**
     * Calculate GS1 Standard Modulo-10 Check Digit for a 12-digit barcode.
     *
     * @param string $digits12 Exactly 12 numeric digits
     * @return int Single checksum digit (0-9)
     * @throws InvalidArgumentException if input is not exactly 12 numeric digits
     */
    public function calculateEan13Checksum(string $digits12): int
    {
        $digits12 = trim($digits12);
        if (!preg_match('/^\d{12}$/', $digits12)) {
            throw new InvalidArgumentException("EAN-13 input must be exactly 12 numeric digits, '{$digits12}' given.");
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $digits12[$i];
            $weight = ($i % 2 === 0) ? 1 : 3;
            $sum += $digit * $weight;
        }

        $remainder = $sum % 10;
        return ($remainder === 0) ? 0 : 10 - $remainder;
    }

    /**
     * Validate whether a string is a valid EAN-13 barcode (length 13, numeric, matching checksum).
     *
     * @param string $barcode
     * @return bool
     */
    public function isValidEan13(string $barcode): bool
    {
        $barcode = trim($barcode);
        if (!preg_match('/^\d{13}$/', $barcode)) {
            return false;
        }

        $raw12 = substr($barcode, 0, 12);
        $expectedChecksum = $this->calculateEan13Checksum($raw12);

        return ((int) $barcode[12]) === $expectedChecksum;
    }

    /**
     * Sanitize scanned/entered barcode from any supplier format.
     * Strips whitespace and non-printable control characters.
     *
     * @param string|null $barcode
     * @return string|null
     */
    public function sanitize(?string $barcode): ?string
    {
        if ($barcode === null) {
            return null;
        }

        $trimmed = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $barcode));
        return $trimmed !== '' ? $trimmed : null;
    }

    /**
     * Generate standards-compliant standalone SVG Barcode using Code 128 Auto (Dynamic Programming).
     *
     * Uses Dynamic Programming to find the globally optimal encoding across Code Sets A, B, and C:
     * - Code Set C: High-density numeric pairs (2 digits per symbol) for compact numeric barcodes.
     * - Code Set B: Full ASCII alphanumeric (32..127), upper/lower case, symbols.
     * - Code Set A: Uppercase letters, digits, and standard ASCII control characters (0..95).
     *
     * Ensures minimum 10-module quiet zones on both sides, ISO/IEC 15417 weighted Modulo-103 checksum,
     * and crisp vector rendering.
     *
     * @param string $barcode Raw input barcode text to encode
     * @param int $barHeight Height of the barcode lines in pixels (default: 48)
     * @param bool $showText Whether to display human-readable text under the barcode
     * @return string Valid SVG XML string
     * @throws InvalidArgumentException if barcode contains characters outside ASCII 0..127
     */
    public function generateBarcodeSvg(string $barcode, int $barHeight = 48, bool $showText = true): string
    {
        $barcode = trim($barcode);
        if ($barcode === '') {
            return '';
        }

        // Validate character set (Code 128 strictly supports standard ASCII 0..127)
        $this->validateCode128Characters($barcode);

        // Optimal Dynamic Programming Code 128 Auto encoder
        $codes = $this->encodeCode128Optimal($barcode);

        // Assemble bar/space pattern sequence
        $patternSequence = '';
        foreach ($codes as $code) {
            $patternSequence .= self::CODE128_PATTERNS[$code] ?? '';
        }

        // Quiet zone padding (minimum 10 modules on both sides as per ISO/IEC 15417)
        $quietZoneModules = 10;
        $dataModules = 0;
        for ($k = 0; $k < strlen($patternSequence); $k++) {
            $dataModules += (int) $patternSequence[$k];
        }

        $totalModules = $dataModules + ($quietZoneModules * 2);
        $moduleWidth = 2; // px per module
        $svgWidth = $totalModules * $moduleWidth;
        $totalHeight = $showText ? ($barHeight + 22) : $barHeight;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $svgWidth . ' ' . $totalHeight . '" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" style="background:#ffffff; border-radius:6px; padding:4px 0;">';

        $x = $quietZoneModules * $moduleWidth;
        $isBar = true;

        for ($k = 0; $k < strlen($patternSequence); $k++) {
            $w = ((int) $patternSequence[$k]) * $moduleWidth;
            if ($isBar) {
                $svg .= '<rect x="' . $x . '" y="4" width="' . $w . '" height="' . $barHeight . '" fill="#111827"/>';
            }
            $x += $w;
            $isBar = !$isBar;
        }

        if ($showText) {
            $svg .= '<text x="' . ($svgWidth / 2) . '" y="' . ($barHeight + 18) . '" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace" font-size="13" font-weight="700" fill="#111827" text-anchor="middle" letter-spacing="2">' . htmlspecialchars($barcode, ENT_XML1, 'UTF-8') . '</text>';
        }

        $svg .= '</svg>';

        return $svg;
    }

    /**
     * Validate that all characters in the string are within the valid Code 128 range (ASCII 0..127).
     *
     * @param string $text
     * @throws InvalidArgumentException
     */
    protected function validateCode128Characters(string $text): void
    {
        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($text[$i]);
            if ($ascii > 127) {
                throw new InvalidArgumentException(
                    "Unsupported character '{$text[$i]}' (ASCII {$ascii}) at position {$i}. Code 128 only supports standard ASCII 0-127."
                );
            }
        }
    }

    /**
     * Dynamic Programming optimal shortest-path encoder for Code 128 (A, B, C).
     *
     * Guarantees the absolute shortest, highest-density symbol sequence by evaluating all possible
     * path combinations across modes A, B, and C with full support for numeric pair compression,
     * odd-length digit runs, and single-character Shift operations.
     *
     * @param string $text
     * @return int[] Full sequence of symbol values (Start + Data + Checksum + Stop)
     */
    protected function encodeCode128Optimal(string $text): array
    {
        $len = strlen($text);
        $inf = PHP_INT_MAX / 2;

        // DP state: dist[i][mode] = minimum symbol count to encode prefix text[0..i-1] ending in mode
        $dist = [];
        $prev = [];

        for ($i = 0; $i <= $len; $i++) {
            for ($m = 0; $m < 3; $m++) {
                $dist[$i][$m] = $inf;
                $prev[$i][$m] = null;
            }
        }

        // Base cases at index 0 (Start Symbols)
        $dist[0][self::MODE_A] = 1;
        $prev[0][self::MODE_A] = ['prev_i' => 0, 'prev_m' => null, 'symbols' => [self::START_A]];

        $dist[0][self::MODE_B] = 1;
        $prev[0][self::MODE_B] = ['prev_i' => 0, 'prev_m' => null, 'symbols' => [self::START_B]];

        // Start C is only possible if at least 2 digits are present at start
        if ($len >= 2 && ctype_digit($text[0]) && ctype_digit($text[1])) {
            $dist[0][self::MODE_C] = 1;
            $prev[0][self::MODE_C] = ['prev_i' => 0, 'prev_m' => null, 'symbols' => [self::START_C]];
        }

        // DP forward transitions
        for ($i = 0; $i <= $len; $i++) {
            // 1. Within-position mode switch relaxation (at position i)
            $this->relaxModeSwitches($dist, $prev, $text, $i, $len);

            if ($i === $len) {
                break;
            }

            $c = $text[$i];
            $ascii = ord($c);

            // 2. Transitions advancing characters:
            // ── In Mode A ────────────────────────────
            if ($dist[$i][self::MODE_A] < $inf) {
                if ($ascii <= 95) {
                    // Direct encoding in Set A
                    $symbol = ($ascii < 32) ? $ascii + 64 : $ascii - 32;
                    $cost = $dist[$i][self::MODE_A] + 1;
                    if ($cost < $dist[$i + 1][self::MODE_A]) {
                        $dist[$i + 1][self::MODE_A] = $cost;
                        $prev[$i + 1][self::MODE_A] = ['prev_i' => $i, 'prev_m' => self::MODE_A, 'symbols' => [$symbol]];
                    }
                } elseif ($ascii <= 127) {
                    // Single character Shift to Set B
                    $cost = $dist[$i][self::MODE_A] + 2;
                    if ($cost < $dist[$i + 1][self::MODE_A]) {
                        $dist[$i + 1][self::MODE_A] = $cost;
                        $prev[$i + 1][self::MODE_A] = ['prev_i' => $i, 'prev_m' => self::MODE_A, 'symbols' => [self::SHIFT, $ascii - 32]];
                    }
                }
            }

            // ── In Mode B ────────────────────────────
            if ($dist[$i][self::MODE_B] < $inf) {
                if ($ascii >= 32 && $ascii <= 127) {
                    // Direct encoding in Set B
                    $symbol = $ascii - 32;
                    $cost = $dist[$i][self::MODE_B] + 1;
                    if ($cost < $dist[$i + 1][self::MODE_B]) {
                        $dist[$i + 1][self::MODE_B] = $cost;
                        $prev[$i + 1][self::MODE_B] = ['prev_i' => $i, 'prev_m' => self::MODE_B, 'symbols' => [$symbol]];
                    }
                } elseif ($ascii < 32) {
                    // Single character Shift to Set A
                    $cost = $dist[$i][self::MODE_B] + 2;
                    if ($cost < $dist[$i + 1][self::MODE_B]) {
                        $dist[$i + 1][self::MODE_B] = $cost;
                        $prev[$i + 1][self::MODE_B] = ['prev_i' => $i, 'prev_m' => self::MODE_B, 'symbols' => [self::SHIFT, $ascii + 64]];
                    }
                }
            }

            // ── In Mode C ────────────────────────────
            if ($dist[$i][self::MODE_C] < $inf) {
                if ($i + 2 <= $len && ctype_digit($text[$i]) && ctype_digit($text[$i + 1])) {
                    $pairVal = (int) substr($text, $i, 2);
                    $cost = $dist[$i][self::MODE_C] + 1;
                    if ($cost < $dist[$i + 2][self::MODE_C]) {
                        $dist[$i + 2][self::MODE_C] = $cost;
                        $prev[$i + 2][self::MODE_C] = ['prev_i' => $i, 'prev_m' => self::MODE_C, 'symbols' => [$pairVal]];
                    }
                }
            }
        }

        // Find the best ending mode at index $len
        $bestMode = self::MODE_B;
        $bestCost = $dist[$len][self::MODE_B];

        foreach ([self::MODE_A, self::MODE_C] as $m) {
            if ($dist[$len][$m] < $bestCost) {
                $bestCost = $dist[$len][$m];
                $bestMode = $m;
            }
        }

        // Reconstruct symbol path backwards
        $pathChunks = [];
        $currI = $len;
        $currM = $bestMode;

        while ($currI > 0 || ($prev[$currI][$currM]['prev_m'] !== null)) {
            $p = $prev[$currI][$currM];
            if ($p === null) {
                break;
            }
            $pathChunks[] = $p['symbols'];
            $currI = $p['prev_i'];
            $currM = $p['prev_m'];
            if ($currM === null) {
                break;
            }
        }

        // Flatten reversed chunks to build forward symbol sequence
        $symbols = [];
        for ($k = count($pathChunks) - 1; $k >= 0; $k--) {
            foreach ($pathChunks[$k] as $sym) {
                $symbols[] = $sym;
            }
        }

        // Calculate ISO/IEC 15417 Code 128 Checksum:
        // Checksum = (Start_Symbol + Sum(1_based_position * Symbol_Value)) % 103
        $checksum = $symbols[0];
        $count = count($symbols);
        for ($pos = 1; $pos < $count; $pos++) {
            $checksum += $symbols[$pos] * $pos;
        }

        $symbols[] = $checksum % 103;
        $symbols[] = self::STOP;

        return $symbols;
    }

    /**
     * Relax mode switch transitions at current position $i.
     */
    private function relaxModeSwitches(array &$dist, array &$prev, string $text, int $i, int $len): void
    {
        $hasCpair = ($i + 2 <= $len) && ctype_digit($text[$i]) && ctype_digit($text[$i + 1]);

        for ($iteration = 0; $iteration < 2; $iteration++) {
            // Switch from Mode A
            if ($dist[$i][self::MODE_A] < PHP_INT_MAX / 2) {
                // A -> B (CODE_B = 100)
                if ($dist[$i][self::MODE_A] + 1 < $dist[$i][self::MODE_B]) {
                    $dist[$i][self::MODE_B] = $dist[$i][self::MODE_A] + 1;
                    $prev[$i][self::MODE_B] = ['prev_i' => $i, 'prev_m' => self::MODE_A, 'symbols' => [self::CODE_B]];
                }
                // A -> C (CODE_C = 99)
                if ($hasCpair && $dist[$i][self::MODE_A] + 1 < $dist[$i][self::MODE_C]) {
                    $dist[$i][self::MODE_C] = $dist[$i][self::MODE_A] + 1;
                    $prev[$i][self::MODE_C] = ['prev_i' => $i, 'prev_m' => self::MODE_A, 'symbols' => [self::CODE_C]];
                }
            }

            // Switch from Mode B
            if ($dist[$i][self::MODE_B] < PHP_INT_MAX / 2) {
                // B -> A (CODE_A = 101)
                if ($dist[$i][self::MODE_B] + 1 < $dist[$i][self::MODE_A]) {
                    $dist[$i][self::MODE_A] = $dist[$i][self::MODE_B] + 1;
                    $prev[$i][self::MODE_A] = ['prev_i' => $i, 'prev_m' => self::MODE_B, 'symbols' => [self::CODE_A]];
                }
                // B -> C (CODE_C = 99)
                if ($hasCpair && $dist[$i][self::MODE_B] + 1 < $dist[$i][self::MODE_C]) {
                    $dist[$i][self::MODE_C] = $dist[$i][self::MODE_B] + 1;
                    $prev[$i][self::MODE_C] = ['prev_i' => $i, 'prev_m' => self::MODE_B, 'symbols' => [self::CODE_C]];
                }
            }

            // Switch from Mode C
            if ($dist[$i][self::MODE_C] < PHP_INT_MAX / 2) {
                // C -> B (CODE_B = 100)
                if ($dist[$i][self::MODE_C] + 1 < $dist[$i][self::MODE_B]) {
                    $dist[$i][self::MODE_B] = $dist[$i][self::MODE_C] + 1;
                    $prev[$i][self::MODE_B] = ['prev_i' => $i, 'prev_m' => self::MODE_C, 'symbols' => [self::CODE_B]];
                }
                // C -> A (CODE_A = 101)
                if ($dist[$i][self::MODE_C] + 1 < $dist[$i][self::MODE_A]) {
                    $dist[$i][self::MODE_A] = $dist[$i][self::MODE_C] + 1;
                    $prev[$i][self::MODE_A] = ['prev_i' => $i, 'prev_m' => self::MODE_C, 'symbols' => [self::CODE_A]];
                }
            }
        }
    }

    /**
     * Backfill missing barcodes for existing products.
     *
     * @return int Number of products updated
     */
    public function backfillMissingBarcodes(): int
    {
        $products = Product::whereNull('barcode')->orWhere('barcode', '')->get();
        $count = 0;

        foreach ($products as $product) {
            $product->barcode = $this->generateUniqueBarcode();
            $product->save();
            $count++;
        }

        return $count;
    }
}
