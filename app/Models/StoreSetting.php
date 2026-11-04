<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("store_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            if ($setting->type === 'json' || $setting->type === 'array') {
                return json_decode($setting->value, true) ?: $default;
            }

            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }

            if ($setting->type === 'integer') {
                return (int) $setting->value;
            }

            if ($setting->type === 'float') {
                return (float) $setting->value;
            }

            return $setting->value;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): self
    {
        $valToStore = is_array($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $valToStore, 'group' => $group, 'type' => $type]
        );

        Cache::forget("store_setting_{$key}");

        return $setting;
    }
}
