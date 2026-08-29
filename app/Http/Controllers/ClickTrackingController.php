<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;

class ClickTrackingController extends Controller
{
    public function redirect(string $trackingCode, Request $request)
    {
        $click = AffiliateClick::where('tracking_code', $trackingCode)->first();

        if (!$click) {
            // Fallback lookup or generic product redirect
            $campaign = Campaign::where('slug', $trackingCode)->first();
            if ($campaign) {
                $product = $campaign->product;
                return redirect()->away($product->affiliate_url);
            }
            return redirect()->route('dashboard')->with('error', 'Tracking link invalid.');
        }

        // Record tracking data
        $click->update([
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'referrer' => substr($request->header('referer') ?? '', 0, 500),
            'clicked_at' => now(),
        ]);

        $product = $click->product;
        $targetUrl = $product->affiliate_url;

        // Build clean UTM query parameters
        $utmParams = array_filter([
            'utm_source' => $click->utm_source ?? 'social',
            'utm_medium' => $click->utm_medium ?? 'affiliate',
            'utm_campaign' => $click->utm_campaign ?? optional($click->campaign)->slug,
            'utm_content' => $click->utm_content ?? (string) $click->campaign_content_id,
        ]);

        if (!empty($utmParams)) {
            $separator = parse_url($targetUrl, PHP_URL_QUERY) ? '&' : '?';
            $targetUrl .= $separator . http_build_query($utmParams);
        }

        return redirect()->away($targetUrl);
    }
}
