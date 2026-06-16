@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Promo Buy X Get Y</h1>
        <a href="{{ route('admin.promos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah Promo
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Promo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Syarat Beli</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gratis/Diskon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($promos as $promo)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($promo->is_active && $promo->start_date <= now() && $promo->end_date >= now())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $promo->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">Beli {{ $promo->buy_quantity }}x {{ $promo->buyMenu->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        Dapat {{ $promo->get_quantity }}x {{ $promo->getMenu->name }}
                        @if($promo->get_type == 'free')
                            <span class="text-green-600 font-bold">(Gratis)</span>
                        @else
                            <span class="text-blue-600 font-bold">(Diskon {{ $promo->get_discount_percent }}%)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $promo->start_date->format('d M Y') }} - {{ $promo->end_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.promos.edit', $promo) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Hapus promo ini?');">
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
