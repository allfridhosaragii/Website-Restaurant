@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Membership Tiers</h1>
        <a href="{{ route('admin.membership_tiers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah Tier
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Spent (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon (%)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Point Multiplier</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($tiers as $tier)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $tier->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $tier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($tier->min_spent, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $tier->discount_percent }}%</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $tier->point_multiplier }}x</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.membership_tiers.edit', $tier) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.membership_tiers.destroy', $tier) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tier ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
