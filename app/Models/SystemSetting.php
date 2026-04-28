<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'data_type',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Cache settings in memory to avoid repeated DB queries
    private static array $cache = [];

    public static function getSetting(string $key, $default = null)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        $value = $setting->value;
        
        // Cast based on data_type
        if ($setting->data_type === 'boolean') {
            $value = in_array($value, ['true', '1', true, 1]);
        } elseif ($setting->data_type === 'integer') {
            $value = (int) $value;
        } elseif ($setting->data_type === 'json') {
            $value = json_decode($value, true);
        }

        self::$cache[$key] = $value;
        return $value;
    }

    public static function setSetting(string $key, $value, string $dataType = 'string', ?string $description = null): void
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $dataType = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
            $dataType = 'boolean';
        }

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'data_type' => $dataType,
                'description' => $description,
            ]
        );

        unset(self::$cache[$key]);
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
