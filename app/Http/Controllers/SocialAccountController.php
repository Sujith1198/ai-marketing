<?php

namespace App\Http\Controllers;

use App\Models\SocialPlatform;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class SocialAccountController extends Controller
{
    public function index()
    {
        $platforms = SocialPlatform::where('is_active', true)->get();
        $accounts = SocialAccount::with(['platform', 'credential'])->get();

        return view('social_accounts.index', compact('platforms', 'accounts'));
    }

    public function connect(SocialPlatform $platform)
    {
        return view('social_accounts.connect', compact('platform'));
    }

    public function disconnect(SocialAccount $account)
    {
        $account->update(['status' => 'disconnected']);
        return back()->with('success', "Social account '{$account->account_name}' disconnected.");
    }
}
