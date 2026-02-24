<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function toggleOrders(Request $request)
    {
        $enabled = $request->boolean('enabled', false);
        Setting::set('orders_enabled', $enabled, 'boolean');
        
        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'message' => $enabled ? 'Pedidos habilitados' : 'Pedidos deshabilitados'
        ]);
    }

    public function getOrdersStatus()
    {
        $enabled = Setting::get('orders_enabled', true);
        
        return response()->json([
            'enabled' => $enabled
        ]);
    }

    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        
        $request->validate([
            'value' => 'required'
        ]);

        Setting::set($key, $request->value, $setting->type);
        
        return back()->with('success', 'Configuración actualizada');
    }
}
