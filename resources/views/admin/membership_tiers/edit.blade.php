@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Membership Tier</h2>
        </div>
        
        <form action="{{ route('admin.membership_tiers.update', $membershipTier) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tier</label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('name', $membershipTier->name) }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Min. Spent (Rp)</label>
                <input type="number" name="min_spent" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('min_spent', $membershipTier->min_spent) }}" min="0">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Diskon Otomatis (%)</label>
                    <input type="number" step="0.01" name="discount_percent" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('discount_percent', $membershipTier->discount_percent) }}" min="0" max="100">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Point Multiplier (x)</label>
                    <input type="number" step="0.1" name="point_multiplier" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('point_multiplier', $membershipTier->point_multiplier) }}" min="1">
                </div>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Urutan (Sort Order)</label>
                    <input type="number" name="sort_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('sort_order', $membershipTier->sort_order) }}">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Benefits (Deskripsi)</label>
                <textarea name="benefits" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('benefits', $membershipTier->benefits) }}</textarea>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.membership_tiers.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
