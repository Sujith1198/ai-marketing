<?php

namespace App\Http\Controllers;

use App\Models\ApiCredential;
use App\Services\Security\SecureVaultService;
use Illuminate\Http\Request;

class CredentialVaultController extends Controller
{
    protected SecureVaultService $vault;

    public function __construct(SecureVaultService $vault)
    {
        $this->vault = $vault;
    }

    public function index()
    {
        $credentials = ApiCredential::orderBy('created_at', 'desc')->get();
        return view('vault.index', compact('credentials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_name' => ['required', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:150'],
            'secret_value' => ['required', 'string', 'min:3'],
        ]);

        $this->vault->storeCredential(
            $request->input('provider_name'),
            $request->input('label'),
            $request->input('secret_value')
        );

        return redirect()->route('vault.index')->with('success', 'API Credential encrypted and saved securely.');
    }

    public function replace(Request $request, ApiCredential $credential)
    {
        $request->validate([
            'new_secret_value' => ['required', 'string', 'min:3'],
            'label' => ['nullable', 'string', 'max:150'],
        ]);

        $this->vault->replaceCredential(
            $credential,
            $request->input('new_secret_value'),
            $request->input('label')
        );

        return redirect()->route('vault.index')->with('success', 'API Credential updated successfully.');
    }

    public function destroy(ApiCredential $credential)
    {
        $credential->delete();
        return redirect()->route('vault.index')->with('success', 'API Credential deleted.');
    }
}
