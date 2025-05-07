<?php

namespace App\Services;

use App\Models\Mapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MappingService
{
    /**
     * Retrieve existing mapping or create a new one, with caching.
     *
     * @param string $keyword
     * @param string $src
     * @param string $creative
     * @return Mapping
     */
    public function getOrCreate(string $keyword, string $src, string $creative): Mapping
    {
        $cacheKey = "mapping:{$keyword}:{$src}:{$creative}";

        // Attempt to get from cache or store result of closure
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($keyword, $src, $creative) {
            return DB::transaction(function () use ($keyword, $src, $creative) {
                // Try to find existing mapping with the highest version
                $row = Mapping::where(compact('keyword', 'src', 'creative'))
                              ->orderByDesc('version')
                              ->first();

                if ($row) {
                    // Return the existing record
                    return $row;
                }

                // Create a new mapping record; version defaults to 1
                return Mapping::create([
                    'keyword'  => $keyword,
                    'src'      => $src,
                    'creative' => $creative,
                    'version'  => 1,
                ]);
            });
        });
    }

    /**
     * Create a fresh version of an existing mapping and invalidate its cache.
     *
     * @param Mapping $map
     * @return Mapping
     */
    public function refresh(Mapping $map): Mapping
    {
        $cacheKey = "mapping:{$map->keyword}:{$map->src}:{$map->creative}";
        Cache::forget($cacheKey);

        return DB::transaction(function () use ($map) {
            $version = $map->version + 1;

            while (true) {
                try {
                    return Mapping::create([
                        'keyword'     => $map->keyword,
                        'src'         => $map->src,
                        'creative'    => $map->creative,
                        'version'     => $version,
                        'refreshed_at'=> now(),
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $version++;
                    continue;
                }
            }
        });
    }

}
