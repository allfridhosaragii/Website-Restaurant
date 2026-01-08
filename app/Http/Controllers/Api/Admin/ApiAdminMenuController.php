<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Menu;

class ApiAdminMenuController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menus = \DB::table('menus')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'menus' => $menus]);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $imageUrl = $request->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';

        $id = \DB::table('menus')->insertGetId([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image_url' => $imageUrl,
            'is_available' => $request->boolean('is_available', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Menu created', 'id' => $id]);
    }

    public function update(Request $request, $slug)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menu = \DB::table('menus')->where('slug', $slug)->first();
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $imageUrl = $request->image_url ?: $menu->image_url;

        \DB::table('menus')->where('id', $menu->id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image_url' => $imageUrl,
            'is_available' => $request->boolean('is_available', true),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Menu updated']);
    }

    public function destroy(Request $request, $slug)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        \DB::table('menus')->where('slug', $slug)->delete();
        return response()->json(['success' => true, 'message' => 'Menu deleted']);
    }
}
