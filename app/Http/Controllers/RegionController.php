<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RegionController extends Controller
{
    private const BASE_URL = 'https://emsifa.github.io/api-wilayah-indonesia/api';
    private const CACHE_TTL = 86400 * 30; // 30 days

    public function provinces()
    {
        return Cache::remember('regions.provinces', self::CACHE_TTL, function () {
            $response = Http::get(self::BASE_URL . '/provinces.json');
            return $response->json() ?? [];
        });
    }

    public function cities($provinceId)
    {
        return Cache::remember("regions.cities.{$provinceId}", self::CACHE_TTL, function () use ($provinceId) {
            $response = Http::get(self::BASE_URL . "/regencies/{$provinceId}.json");
            return $response->json() ?? [];
        });
    }

    public function districts($cityId)
    {
        return Cache::remember("regions.districts.{$cityId}", self::CACHE_TTL, function () use ($cityId) {
            $response = Http::get(self::BASE_URL . "/districts/{$cityId}.json");
            return $response->json() ?? [];
        });
    }

    public function villages($districtId)
    {
        return Cache::remember("regions.villages.{$districtId}", self::CACHE_TTL, function () use ($districtId) {
            $response = Http::get(self::BASE_URL . "/villages/{$districtId}.json");
            return $response->json() ?? [];
        });
    }
}
