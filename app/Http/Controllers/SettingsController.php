<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        
        $weights = SystemSetting::getSetting('scoring_weights', [
            'demand' => 15,
            'buyer_intent' => 15,
            'commission' => 10,
            'content_potential' => 15,
            'social_fit' => 10,
            'seo_potential' => 10,
            'conversion_potential' => 15,
            'trust' => 10,
        ]);

        $maxCompetitionPenalty = SystemSetting::getSetting('max_competition_penalty', 15);
        $maxRiskPenalty = SystemSetting::getSetting('max_risk_penalty', 20);

        return view('settings.index', compact('settings', 'weights', 'maxCompetitionPenalty', 'maxRiskPenalty'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            SystemSetting::set($key, (string) $value);
        }

        return redirect()->route('settings.index')->with('success', 'General settings saved successfully.');
    }

    public function updateScoringWeights(Request $request)
    {
        $weights = $request->input('weights', []);

        $sum = array_sum(array_map('intval', $weights));

        if ($sum !== 100) {
            return back()->withInput()->with('error_scoring', "Scoring weights total must equal exactly 100%. Current total: {$sum}%.");
        }

        SystemSetting::setSetting('scoring_weights', $weights);
        SystemSetting::setSetting('max_competition_penalty', (int) $request->input('max_competition_penalty', 15));
        SystemSetting::setSetting('max_risk_penalty', (int) $request->input('max_risk_penalty', 20));

        return redirect()->route('settings.index')->with('success', 'Product Scoring Weights & Penalty limits updated successfully.');
    }
}
