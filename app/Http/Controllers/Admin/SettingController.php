<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $val) {
            StoreSetting::set($key, $val);
        }

        AuditLog::log('updated', 'StoreSetting', null, null, $inputs);

        return back()->with('success', 'Store configurations updated successfully.');
    }
}
