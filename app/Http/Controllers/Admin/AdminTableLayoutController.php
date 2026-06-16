<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminTableLayoutController extends Controller
{
    public function index()
    {
        $layouts = \App\Models\TableLayout::withCount('tables')->get();
        return view('admin.table_layouts.index', compact('layouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grid_width' => 'required|integer|min:10|max:100',
            'grid_height' => 'required|integer|min:10|max:100',
        ]);

        \App\Models\TableLayout::create($validated);

        return back()->with('success', 'Layout berhasil ditambahkan.');
    }

    public function show($id)
    {
        $layout = \App\Models\TableLayout::with('tables')->findOrFail($id);
        return view('admin.table_layouts.show', compact('layout'));
    }

    public function update(Request $request, $id)
    {
        $layout = \App\Models\TableLayout::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grid_width' => 'required|integer|min:10|max:100',
            'grid_height' => 'required|integer|min:10|max:100',
            'is_active' => 'boolean',
        ]);

        $layout->update($validated);

        return back()->with('success', 'Layout berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $layout = \App\Models\TableLayout::findOrFail($id);
        $layout->delete();

        return redirect('/admin/table-layouts')->with('success', 'Layout berhasil dihapus.');
    }

    public function storeTable(Request $request, $layoutId)
    {
        $layout = \App\Models\TableLayout::findOrFail($layoutId);

        $validated = $request->validate([
            'number' => 'required|integer|unique:tables,number',
            'capacity' => 'required|integer|min:1',
            'shape' => 'required|in:round,square,rectangle',
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $layout->tables()->create([
            'number' => $validated['number'],
            'capacity' => $validated['capacity'],
            'shape' => $validated['shape'],
            'position_x' => $validated['position_x'],
            'position_y' => $validated['position_y'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'status' => 'available',
        ]);

        return response()->json(['success' => true]);
    }

    public function updateTable(Request $request, $layoutId, $tableId)
    {
        $table = \App\Models\Table::where('table_layout_id', $layoutId)->findOrFail($tableId);

        $validated = $request->validate([
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $table->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroyTable($layoutId, $tableId)
    {
        $table = \App\Models\Table::where('table_layout_id', $layoutId)->findOrFail($tableId);
        $table->delete();

        return response()->json(['success' => true]);
    }

    public function kasirMap(Request $request)
    {
        $layouts = \App\Models\TableLayout::where('is_active', true)->get();
        $selectedLayoutId = $request->get('layout_id');
        
        if (!$selectedLayoutId && $layouts->isNotEmpty()) {
            $selectedLayoutId = $layouts->first()->id;
        }

        $layout = null;
        if ($selectedLayoutId) {
            $layout = \App\Models\TableLayout::with('tables')->find($selectedLayoutId);
        }

        return view('admin.pos.table_map', compact('layouts', 'layout', 'selectedLayoutId'));
    }

    public function kasirMapData(Request $request)
    {
        $layoutId = $request->get('layout_id');
        if (!$layoutId) {
            return response()->json([]);
        }

        $tables = \App\Models\Table::where('table_layout_id', $layoutId)->get()->map(function($table) {
            // Cek status meja berdasarkan order aktif
            // order aktif = status pending atau processing
            $activeOrder = \App\Models\Order::where('table_id', $table->id)
                ->whereIn('status', ['pending', 'processing'])
                ->with('user')
                ->latest()
                ->first();

            $status = 'available';
            $customerName = null;
            $orderId = null;
            $tableGroupId = null;

            if ($activeOrder) {
                $status = 'occupied';
                $customerName = $activeOrder->user ? $activeOrder->user->name : 'Guest';
                $orderId = $activeOrder->id;
                $tableGroupId = $activeOrder->table_group_id;
            }

            return [
                'id' => $table->id,
                'number' => $table->number,
                'capacity' => $table->capacity,
                'shape' => $table->shape,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'width' => $table->width,
                'height' => $table->height,
                'status' => $status,
                'customer_name' => $customerName,
                'order_id' => $orderId,
                'table_group_id' => $tableGroupId
            ];
        });

        return response()->json($tables);
    }
}
