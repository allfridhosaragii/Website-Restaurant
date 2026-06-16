@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Promo Buy X Get Y</h2>
        </div>
        
        <form action="{{ route('admin.promos.update', $promo) }}" method="POST" class="p-6" x-data="{ getType: '{{ $promo->get_type }}' }">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="buy_x_get_y">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Promo</label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('name', $promo->name) }}">
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6 p-4 border rounded bg-blue-50">
                <!-- BUY SECTION -->
                <div>
                    <h3 class="font-bold text-blue-800 mb-2 border-b border-blue-200 pb-1">Syarat Beli (BUY)</h3>
                    
                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs font-bold mb-1">Menu yang dibeli</label>
                        <select name="buy_menu_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ $promo->buy_menu_id == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Minimal Beli (Qty)</label>
                        <input type="number" name="buy_quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('buy_quantity', $promo->buy_quantity) }}" min="1">
                    </div>
                </div>

                <!-- GET SECTION -->
                <div>
                    <h3 class="font-bold text-green-800 mb-2 border-b border-green-200 pb-1">Reward (GET)</h3>
                    
                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs font-bold mb-1">Menu Reward</label>
                        <select name="get_menu_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">-- Sama dengan menu yang dibeli --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ $promo->get_menu_id == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs font-bold mb-1">Dapat Berapa (Qty)</label>
                        <input type="number" name="get_quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('get_quantity', $promo->get_quantity) }}" min="1">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs font-bold mb-1">Tipe Reward</label>
                        <select name="get_type" x-model="getType" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="free">Gratis 100%</option>
                            <option value="discount">Diskon x%</option>
                        </select>
                    </div>

                    <div x-show="getType == 'discount'" class="mt-2">
                        <label class="block text-gray-700 text-xs font-bold mb-1">Diskon (%)</label>
                        <input type="number" name="get_discount_percent" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('get_discount_percent', $promo->get_discount_percent) }}" min="1" max="99">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                    <input type="datetime-local" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('start_date', $promo->start_date->format('Y-m-d\TH:i')) }}">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai</label>
                    <input type="datetime-local" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('end_date', $promo->end_date->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ $promo->is_active ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700 font-bold">Promo Aktif</span>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.promos.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Promo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
