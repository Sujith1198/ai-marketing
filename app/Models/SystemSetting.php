<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group_name',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'group_name' => $group, 'type' => $type]
        );
    }

    public static function getSetting(string $key, $default = null)
    {
        $val = static::get($key);
        if ($val === null) return $default;
        $decoded = json_decode($val, true);
        return (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_numeric($decoded))) ? $decoded : $val;
    }

    public static function setSetting(string $key, $value): void
    {
        $valToStore = is_array($value) ? json_encode($value) : (string)$value;
        static::set($key, $valToStore);
    }
}
