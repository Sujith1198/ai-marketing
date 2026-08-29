<?php

namespace App\Http\Controllers;

use App\Models\AffiliateNetwork;
use App\Models\ApiCredential;
use Illuminate\Http\Request;

class AffiliateNetworkController extends Controller
{
    public function index()
    {
        $networks = AffiliateNetwork::with('credential')->get();
        $credentials = ApiCredential::all();

        return view('affiliates.index', compact('networks', 'credentials'));
    }

    public function update(Request $request, AffiliateNetwork $network)
    {
        $request->validate([
            'tracking_id' => ['nullable', 'string', 'max:150'],
            'affiliate_username' => ['nullable', 'string', 'max:150'],
            'portal_url' => ['nullable', 'url', 'max:500'],
            'credential_id' => ['nullable', 'exists:api_credentials,id'],
            'is_active' => ['boolean'],
        ]);

        $network->update([
            'tracking_id' => $request->input('tracking_id'),
            'affiliate_username' => $request->input('affiliate_username'),
            'portal_url' => $request->input('portal_url'),
            'credential_id' => $request->input('credential_id'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('affiliates.index')->with('success', "Affiliate Network '{$network->name}' updated.");
    }
}
