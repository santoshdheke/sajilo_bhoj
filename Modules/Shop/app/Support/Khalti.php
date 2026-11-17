<?php

namespace Modules\Shop\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Modules\Shop\Models\ShopSetting;

/**
 * Khalti ePayment (KPG-2) with the shop's own merchant secret key (Settings → Payments).
 * "test" uses dev.khalti.com, "live" takes real money. Amounts are in paisa.
 */
class Khalti
{
    public function __construct(private ShopSetting $settings)
    {
    }

    public function enabled(): bool
    {
        return filled($this->settings->khalti_secret_key);
    }

    private function url(string $path): string
    {
        $base = $this->settings->khalti_env === 'live' ? 'https://khalti.com/api/v2/' : 'https://dev.khalti.com/api/v2/';

        return $base.$path;
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders(['Authorization' => 'Key '.$this->settings->khalti_secret_key])->acceptJson()->timeout(20);
    }

    /** Start a payment; the response has payment_url (send the customer there) and pidx. */
    public function initiate(array $payload): Response
    {
        return $this->request()->post($this->url('epayment/initiate/'), $payload);
    }

    /** Payment status by pidx: status ("Completed"...), total_amount (paisa), transaction_id. */
    public function lookup(string $pidx): Response
    {
        return $this->request()->post($this->url('epayment/lookup/'), ['pidx' => $pidx]);
    }
}
