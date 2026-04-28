<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $settings = SystemSetting::all();
        return response()->json($settings);
    }

    public function show(string $key)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $setting = SystemSetting::where('key', $key)->first();
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        return response()->json($setting);
    }

    public function update(Request $request, string $key)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'value' => 'required',
            'data_type' => 'in:string,integer,boolean,json',
            'description' => 'nullable|string',
        ]);

        $setting = SystemSetting::where('key', $key)->first();
        $oldValue = $setting?->value;

        SystemSetting::setSetting(
            $key,
            $validated['value'],
            $validated['data_type'] ?? 'string',
            $validated['description']
        );

        ActivityLog::log(
            auth()->id(),
            'update_setting',
            'SystemSetting',
            SystemSetting::where('key', $key)->first()->id,
            ['key' => $key, 'value' => $oldValue],
            ['key' => $key, 'value' => $validated['value']],
            "Updated setting: {$key}"
        );

        return response()->json(['message' => 'Setting updated', 'setting' => SystemSetting::where('key', $key)->first()]);
    }

    public function bulk(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.data_type' => 'in:string,integer,boolean,json',
        ]);

        foreach ($validated['settings'] as $setting) {
            $oldSetting = SystemSetting::where('key', $setting['key'])->first();
            
            SystemSetting::setSetting(
                $setting['key'],
                $setting['value'],
                $setting['data_type'] ?? 'string'
            );

            ActivityLog::log(
                auth()->id(),
                'update_setting',
                'SystemSetting',
                $oldSetting?->id ?? 0,
                ['key' => $setting['key'], 'value' => $oldSetting?->value],
                ['key' => $setting['key'], 'value' => $setting['value']],
                "Updated settings (bulk): {$setting['key']}"
            );
        }

        return response()->json(['message' => 'Settings updated', 'count' => count($validated['settings'])]);
    }

    // Common clinic settings
    public function getClinicSettings()
    {
        $settings = [
            'clinic_name' => SystemSetting::getSetting('clinic_name', 'Pet Care Clinic'),
            'clinic_phone' => SystemSetting::getSetting('clinic_phone', ''),
            'clinic_email' => SystemSetting::getSetting('clinic_email', ''),
            'clinic_address' => SystemSetting::getSetting('clinic_address', ''),
            'working_hours_start' => SystemSetting::getSetting('working_hours_start', '08:00'),
            'working_hours_end' => SystemSetting::getSetting('working_hours_end', '18:00'),
            'appointment_slot_duration' => SystemSetting::getSetting('appointment_slot_duration', 30),
            'appointment_advance_booking_days' => SystemSetting::getSetting('appointment_advance_booking_days', 30),
            'minimum_appointment_notice_hours' => SystemSetting::getSetting('minimum_appointment_notice_hours', 2),
            'enable_online_payment' => SystemSetting::getSetting('enable_online_payment', true),
            'deposit_policy_percentage' => SystemSetting::getSetting('deposit_policy_percentage', 20),
            'enable_appointment_reminder' => SystemSetting::getSetting('enable_appointment_reminder', true),
            'reminder_hours_before' => SystemSetting::getSetting('reminder_hours_before', 24),
        ];

        return response()->json($settings);
    }

    public function updateClinicSettings(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'clinic_name' => 'nullable|string',
            'clinic_phone' => 'nullable|string',
            'clinic_email' => 'nullable|email',
            'clinic_address' => 'nullable|string',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'appointment_slot_duration' => 'nullable|integer|min:15',
            'appointment_advance_booking_days' => 'nullable|integer|min:1',
            'minimum_appointment_notice_hours' => 'nullable|integer|min:0',
            'enable_online_payment' => 'nullable|boolean',
            'deposit_policy_percentage' => 'nullable|integer|min:0|max:100',
            'enable_appointment_reminder' => 'nullable|boolean',
            'reminder_hours_before' => 'nullable|integer|min:1',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                SystemSetting::setSetting($key, $value);
            }
        }

        ActivityLog::log(
            auth()->id(),
            'update_clinic_settings',
            'SystemSetting',
            0,
            [],
            $validated,
            'Updated clinic settings'
        );

        return response()->json(['message' => 'Clinic settings updated', 'settings' => $this->getClinicSettings()->getData(true)]);
    }
}
