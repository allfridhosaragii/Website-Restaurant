<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
class StatusController extends Controller
{
    public function index()
    {
        $maintenanceMode = CmsSetting::where('key', 'maintenance_mode')->first();
        $isMaintenanceMode = $maintenanceMode && $maintenanceMode->value === 'true';
        $maintenanceStartTime = null;
        if ($isMaintenanceMode) {
            $startSetting = CmsSetting::where('key', 'maintenance_start_time')->first();
            $maintenanceStartTime = $startSetting ? $startSetting->value : null;
        }
        return view('maintenance_control', compact('isMaintenanceMode', 'maintenanceStartTime'));
    }
    public function toggle(Request $request)
    {
        $setting = CmsSetting::firstOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => 'false', 'type' => 'boolean']
        );
        $newValue = $setting->value === 'true' ? 'false' : 'true';
        $setting->value = $newValue;
        $setting->save();
        $startTime = null;
        if ($newValue === 'true') {
            $startTime = now()->toISOString();
            CmsSetting::updateOrCreate(
                ['key' => 'maintenance_start_time'],
                ['value' => $startTime, 'type' => 'string']
            );
        } else {
            CmsSetting::where('key', 'maintenance_start_time')->delete();
            \App\Models\SiteVisitor::truncate();
            \App\Models\MaintenanceVisitor::truncate();
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'isMaintenanceMode' => $newValue === 'true',
                'startTime' => $startTime,
                'message' => 'Maintenance mode ' . ($newValue === 'true' ? 'activated' : 'deactivated')
            ]);
        }
        return redirect('/maintenance')->with('success', 'Maintenance mode ' . ($newValue === 'true' ? 'activated' : 'deactivated'));
    }
}