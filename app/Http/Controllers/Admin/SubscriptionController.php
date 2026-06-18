<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function choose(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.theme_one.subscription.choose', ['plans' => $plans]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $company = Tenancy::company();

        Subscription::create([
            'company_id'    => $company->id,
            'plan_id'       => $plan->id,
            'status'        => $plan->trial_days > 0 ? 'trialing' : 'active',
            'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            'starts_at'     => now(),
        ]);

        return redirect()->route('admin.dashboard.index')
            ->with('status', "You're on the {$plan->name} plan.");
    }
}
