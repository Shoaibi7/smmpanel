<?php

use Livewire\Volt\Component;
use App\Models\Service;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $categoryId = '';
    public $name = '';
    public $price_per_k = 0;
    public $min_qty = 100;
    public $max_qty = 100000;
    public $description = '';
    public $is_active = true;
    
    public $editingServiceId = null;

    public function with()
    {
        return [
            'services' => Service::with('category')->latest()->paginate(10),
            'categories' => Category::all(),
        ];
    }

    public function save()
    {
        $this->validate([
            'categoryId' => 'required|exists:categories,id',
            'name' => 'required',
            'price_per_k' => 'required|numeric|min:0',
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'required|integer',
        ]);

        $data = [
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'price_per_k' => $this->price_per_k,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->editingServiceId) {
            Service::find($this->editingServiceId)->update($data);
            $this->dispatch('toast', message: 'Service updated!', type: 'success');
        } else {
            Service::create($data);
            $this->dispatch('toast', message: 'Service created!', type: 'success');
        }

        $this->reset(['categoryId', 'name', 'price_per_k', 'min_qty', 'max_qty', 'description', 'is_active', 'editingServiceId']);
    }

    public function edit($id)
    {
        $service = Service::find($id);
        $this->editingServiceId = $service->id;
        $this->categoryId = $service->category_id;
        $this->name = $service->name;
        $this->price_per_k = $service->price_per_k;
        $this->min_qty = $service->min_qty;
        $this->max_qty = $service->max_qty;
        $this->description = $service->description;
        $this->is_active = $service->is_active;
    }

    public function cancel()
    {
        $this->reset(['categoryId', 'name', 'price_per_k', 'min_qty', 'max_qty', 'description', 'is_active', 'editingServiceId']);
    }

    public function delete($id)
    {
        Service::find($id)->delete();
        $this->dispatch('toast', message: 'Service deleted!', type: 'info');
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form -->
            <div class="lg:col-span-1">
                <x-card>
                    <x-slot name="header">
                        <h3 class="font-bold">{{ $editingServiceId ? 'Edit Service' : 'Create Service' }}</h3>
                    </x-slot>

                    <form wire:submit.prevent="save" class="space-y-4">
                        <div>
                            <x-input-label for="scat" value="Category" />
                            <select wire:model="categoryId" id="scat" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="sname" value="Service Name" />
                            <x-text-input wire:model="name" id="sname" type="text" class="mt-1 block w-full" placeholder="e.g. Followers [HQ]" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="sprice" value="Price / 1k" />
                                <x-text-input wire:model="price_per_k" id="sprice" type="number" step="0.00001" class="mt-1 block w-full" />
                            </div>
                            <div class="flex items-center space-x-2 pt-6">
                                <input wire:model="is_active" type="checkbox" id="sisactive" class="rounded dark:bg-secondary-900 border-secondary-300 dark:border-secondary-700 text-primary-600 shadow-sm focus:ring-primary-500">
                                <x-input-label for="sisactive" value="Active" class="!mb-0" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="smin" value="Min Qty" />
                                <x-text-input wire:model="min_qty" id="smin" type="number" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="smax" value="Max Qty" />
                                <x-text-input wire:model="max_qty" id="smax" type="number" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="sdesc" value="Description" />
                            <textarea wire:model="description" id="sdesc" rows="3" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                        </div>

                        <div class="flex items-center space-x-2 pt-4">
                            <x-button type="submit" variant="primary" class="flex-1">{{ $editingServiceId ? 'Update' : 'Create' }}</x-button>
                            @if($editingServiceId)
                                <x-button type="button" variant="ghost" wire:click="cancel">Cancel</x-button>
                            @endif
                        </div>
                    </form>
                </x-card>
            </div>

            <!-- List -->
            <div class="lg:col-span-2">
                <x-card class="p-0 overflow-hidden">
                    <x-table :headers="['Service', 'Price / 1k', 'Category', 'Actions']">
                        @foreach($services as $svc)
                            <tr class="hover:bg-secondary-50 dark:hover:bg-secondary-800/30">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-secondary-900 dark:text-white">{{ $svc->name }}</div>
                                    <div class="text-[10px] text-secondary-500">{{ Str::limit($svc->description, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-primary-600">{{ format_currency($svc->sale_price ?? $svc->price_per_k) }}</td>
                                <td class="px-6 py-4 text-xs text-secondary-500 uppercase tracking-widest font-bold">{{ $svc->category->name }}</td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <button wire:click="edit({{ $svc->id }})" class="text-primary-600 font-bold">Edit</button>
                                    <button wire:click="delete({{ $svc->id }})" wire:confirm="Are you sure?" class="text-red-600 font-bold">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                    <div class="p-4 bg-secondary-50 dark:bg-secondary-950">
                        {{ $services->links() }}
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</div>
