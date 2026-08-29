<?php

namespace App\Services\Product;

use App\Models\AffiliateNetwork;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductImportService
{
    /**
     * Preview CSV contents.
     */
    public function previewCsv(string $filePath): array
    {
        $rows = $this->parseCsv($filePath);
        $preview = array_slice($rows, 0, 5);

        return [
            'total_rows' => count($rows),
            'preview' => $preview,
        ];
    }

    /**
     * Process CSV file and import products.
     */
    public function processImport(string $filePath, int $userId): array
    {
        $rows = $this->parseCsv($filePath);

        $imported = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];

        $defaultNetwork = AffiliateNetwork::where('slug', 'amazon-associates')->first() 
            ?? AffiliateNetwork::first();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Including header offset

            $name = trim($row['product_name'] ?? $row['name'] ?? '');
            $affiliateUrl = trim($row['affiliate_url'] ?? '');

            if (empty($name) || empty($affiliateUrl)) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Missing product name or affiliate URL.";
                continue;
            }

            if (!filter_var($affiliateUrl, FILTER_VALIDATE_URL)) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Invalid affiliate URL scheme.";
                continue;
            }

            // Duplicate detection check
            $existing = Product::where('affiliate_url', $affiliateUrl)
                ->orWhere('name', $name)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Resolve Network
            $networkSlug = Str::slug($row['network'] ?? '');
            $network = AffiliateNetwork::where('slug', $networkSlug)->first() ?? $defaultNetwork;

            try {
                Product::create([
                    'user_id' => $userId,
                    'affiliate_network_id' => $network->id,
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::random(5),
                    'category' => $row['category'] ?? 'General',
                    'brand' => $row['brand'] ?? null,
                    'description' => $row['description'] ?? null,
                    'product_url' => filter_var($row['product_url'] ?? '', FILTER_VALIDATE_URL) ? $row['product_url'] : null,
                    'affiliate_url' => $affiliateUrl,
                    'price' => is_numeric($row['price'] ?? null) ? (float) $row['price'] : null,
                    'currency' => strtoupper($row['currency'] ?? 'USD'),
                    'commission_type' => in_array(strtolower($row['commission_type'] ?? ''), ['fixed', 'percentage']) ? strtolower($row['commission_type']) : 'percentage',
                    'commission_value' => is_numeric($row['commission_value'] ?? null) ? (float) $row['commission_value'] : 0.0,
                    'source' => 'csv',
                    'status' => 'draft',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row #{$rowNum}: Database error - " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    protected function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $headers = [];
        $rows = [];

        if (($data = fgetcsv($handle, 2000, ',')) !== false) {
            $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $data);
        }

        while (($data = fgetcsv($handle, 2000, ',')) !== false) {
            if (count($data) === count($headers)) {
                $rows[] = array_combine($headers, $data);
            }
        }

        fclose($handle);
        return $rows;
    }
}
