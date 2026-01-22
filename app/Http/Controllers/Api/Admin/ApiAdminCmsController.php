<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class ApiAdminCmsController extends Controller
{
    public function toggleMaintenance(Request $request)
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $setting = \App\Models\CmsSetting::where('key', 'maintenance_mode')->first();
        if (!$setting) {
            $setting = new \App\Models\CmsSetting(['key' => 'maintenance_mode']);
        }
        $newValue = $request->input('value') === true ? 'true' : 'false';
        $setting->value = $newValue;
        $setting->save();
        return response()->json(['success' => true, 'maintenance_mode' => $newValue === 'true']);
    }
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $maintenance = \App\Models\CmsSetting::where('key', 'maintenance_mode')->first();
        return response()->json([
            'success' => true,
            'settings' => [
                'maintenance_mode' => $maintenance ? $maintenance->value === 'true' : false,
            ]
        ]);
    }
}