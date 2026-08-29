<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AffiliateAccount;
use App\Models\AffiliateNetwork;
use App\Models\ApiCredential;
use App\Services\Affiliate\AffiliateProviderManager;
use Illuminate\Http\Request;

class AffiliateAccountController extends Controller
{
    public function index()
    {
        $accounts = AffiliateAccount::with(['network', 'credential'])
            ->where('user_id', auth()->id())
            ->get();

        $networks = AffiliateNetwork::where('is_active', true)->get();
        $credentials = ApiCredential::all();

        return view('affiliates.accounts', compact('accounts', 'networks', 'credentials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'affiliate_network_id' => ['required', 'exists:affiliate_networks,id'],
            'name' => ['required', 'string', 'max:150'],
            'tracking_id' => ['nullable', 'string', 'max:150'],
            'credential_id' => ['nullable', 'exists:api_credentials,id'],
        ]);

        $account = AffiliateAccount::create([
            'user_id' => auth()->id(),
            'affiliate_network_id' => $request->input('affiliate_network_id'),
            'name' => $request->input('name'),
            'tracking_id' => $request->input('tracking_id'),
            'status' => $request->input('credential_id') ? 'connected' : 'manual',
            'credential_id' => $request->input('credential_id'),
            'last_tested_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'affiliate_account_created',
            'entity_type' => AffiliateAccount::class,
            'entity_id' => $account->id,
            'metadata' => ['account_name' => $account->name],
        ]);

        return redirect()->route('affiliate-accounts.index')->with('success', "Affiliate account '{$account->name}' created successfully.");
    }

    public function testConnection(AffiliateAccount $account, AffiliateProviderManager $providerManager)
    {
        $provider = $providerManager->resolve($account->network->driver);
        $credentials = $account->credential ? ['api_key' => 'configured'] : [];
        $result = $provider->testConnection($credentials);

        $account->update(['last_tested_at' => now()]);

        return back()->with('success', "Connection Test for {$account->name}: " . $result['message']);
    }

    public function destroy(AffiliateAccount $account)
    {
        $name = $account->name;
        $account->delete();

        return back()->with('success', "Affiliate account '{$name}' removed.");
    }
}
