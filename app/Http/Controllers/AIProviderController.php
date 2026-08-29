<?php

namespace App\Http\Controllers;

use App\Models\AIProvider;
use App\Models\ApiCredential;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;

class AIProviderController extends Controller
{
    protected AIProviderManager $manager;

    public function __construct(AIProviderManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        $providers = AIProvider::with(['credential', 'fallbackProvider'])->get();
        $credentials = ApiCredential::all();

        return view('providers.index', compact('providers', 'credentials'));
    }

    public function update(Request $request, AIProvider $provider)
    {
        $request->validate([
            'default_model' => ['required', 'string'],
            'credential_id' => ['nullable', 'exists:api_credentials,id'],
            'fallback_provider_id' => ['nullable', 'exists:ai_providers,id'],
            'api_endpoint' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'is_primary' => ['boolean'],
        ]);

        if ($request->boolean('is_primary')) {
            AIProvider::where('id', '!=', $provider->id)->update(['is_primary' => false]);
        }

        $provider->update([
            'default_model' => $request->input('default_model'),
            'credential_id' => $request->input('credential_id'),
            'fallback_provider_id' => $request->input('fallback_provider_id'),
            'api_endpoint' => $request->input('api_endpoint'),
            'is_active' => $request->boolean('is_active', true),
            'is_primary' => $request->boolean('is_primary', false),
        ]);

        return redirect()->route('providers.index')->with('success', 'AI Provider settings updated.');
    }

    public function testConnection(AIProvider $provider)
    {
        $instance = $this->manager->resolve($provider);
        $success = $instance->testConnection();

        if ($success) {
            return back()->with('success', "Connection to {$provider->name} verified successfully!");
        }

        return back()->with('error', "Failed to connect to {$provider->name}. Check API key and credentials.");
    }
}
