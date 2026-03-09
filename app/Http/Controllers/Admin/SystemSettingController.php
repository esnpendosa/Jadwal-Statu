<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settingsData = $request->input('settings', []);

        // Handle File Uploads
        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('settings', 'public');
            SystemSetting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => $path]
            );
        }

        // Fallback: if no settings[] array, treat all non-reserved fields as settings
        if (empty($settingsData)) {
            $settingsData = $request->except(['_token', '_method', 'app_logo']);
        }

        foreach ($settingsData as $key => $value) {
            if (!empty($key)) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? '']
                );
            }
        }

        return back()->with('success', __('admin.settings_saved'));
    }
}
