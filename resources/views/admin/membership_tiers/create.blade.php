@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Tambah Membership Tier</h2>
        </div>
        
        <form action="{{ route('admin.membership_tiers.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tier</label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required placeholder="Contoh: Gold">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Min. Spent (Rp)</label>
                <input type="number" name="min_spent" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required placeholder="Contoh: 2000000" min="0">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Diskon Otomatis (%)</label>
                    <input type="number" step="0.01" name="discount_percent" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="0" min="0" max="100">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Point Multiplier (x)</label>
                    <input type="number" step="0.1" name="point_multiplier" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="1" min="1">
                </div>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Urutan (Sort Order)</label>
                    <input type="number" name="sort_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="0">
                    <p class="text-xs text-gray-500 mt-1">Makin besar = makin tinggi levelnya (Bronze=1, Silver=2, dll)</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Benefits (Deskripsi)</label>
                <textarea name="benefits" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Deskripsi benefit untuk tier ini..."></textarea>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.membership_tiers.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
