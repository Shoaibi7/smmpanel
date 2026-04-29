<?php

use Livewire\Volt\Component;
use App\Models\Setting;

new class extends Component {
    public $settings = [];

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        
        // Initial defaults if not existing
        $defaults = [
            'site_name' => config('app.name'),
            'site_description' => 'The #1 SMM Panel in the world.',
            'contact_email' => 'support@smmpro.com',
            'meta_title' => 'SMM PRO - Social Media Marketing Panel',
            'meta_description' => 'Boost your social presence with SMM PRO. Fast, reliable, and cheap services.',
            'payfast_merchant_id' => '',
            'payfast_secured_key' => '',
            'currency_code' => 'USD',
            'usd_to_pkr_rate' => '280',
            'api_endpoint' => url('api/v1'),
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
    }

    public function save()
    {
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->dispatch('toast', message: 'Settings saved successfully!', type: 'success');
    }
}; ?>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold">System & SEO Settings</h3>
            </x-slot>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="site_name" value="Site Name" />
                        <x-text-input wire:model="settings.site_name" id="site_name" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="contact_email" value="Contact Email" />
                        <x-text-input wire:model="settings.contact_email" id="contact_email" type="email" class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <x-input-label for="site_description" value="Footer Description" />
                    <textarea wire:model="settings.site_description" id="site_description" rows="2" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                </div>

                <hr class="border-secondary-100 dark:border-secondary-800">

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-secondary-900 dark:text-white uppercase tracking-widest">Currency & Regional Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="currency_code" value="Display Currency" />
                            <select wire:model="settings.currency_code" id="currency_code" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200">
                                <option value="USD">USD - US Dollar</option>
                                <option value="PKR">PKR - Pakistani Rupee</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="usd_to_pkr_rate" value="USD to PKR Conversion Rate (1 USD = ? PKR)" />
                            <x-text-input wire:model="settings.usd_to_pkr_rate" id="usd_to_pkr_rate" type="number" step="0.01" class="mt-1 block w-full" placeholder="e.g. 280" />
                        </div>
                    </div>
                </div>

                <hr class="border-secondary-100 dark:border-secondary-800">

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-secondary-900 dark:text-white uppercase tracking-widest">Payment Gateway: PayFast</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="payfast_merchant_id" value="PayFast Merchant ID" />
                            <x-text-input wire:model="settings.payfast_merchant_id" id="payfast_merchant_id" type="text" class="mt-1 block w-full" placeholder="Enter Merchant ID" />
                        </div>
                        <div>
                            <x-input-label for="payfast_secured_key" value="PayFast Secured Key" />
                            <x-text-input wire:model="settings.payfast_secured_key" id="payfast_secured_key" type="password" class="mt-1 block w-full" placeholder="Enter Secured Key" />
                        </div>
                    </div>
                </div>

                <hr class="border-secondary-100 dark:border-secondary-800">

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-secondary-900 dark:text-white uppercase tracking-widest">Global SEO Metadata</h4>
                    
                    <div>
                        <x-input-label for="meta_title" value="Default Meta Title" />
                        <x-text-input wire:model="settings.meta_title" id="meta_title" type="text" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="meta_description" value="Default Meta Description" />
                        <textarea wire:model="settings.meta_description" id="meta_description" rows="3" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                    </div>
                </div>

                <hr class="border-secondary-100 dark:border-secondary-800">

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-secondary-900 dark:text-white uppercase tracking-widest">API Configuration</h4>
                    <div>
                        <x-input-label for="api_endpoint" value="API Endpoint URL" />
                        <x-text-input wire:model="settings.api_endpoint" id="api_endpoint" type="text" class="mt-1 block w-full font-mono" placeholder="https://yourdomain.com/api/v1" />
                        <p class="text-xs text-secondary-400 mt-1">Base URL shown to users for API access. Update if you use a custom domain.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <x-button type="submit" variant="primary" class="w-full py-3">Save All Settings</x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
