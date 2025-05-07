<?php

namespace App\Services;

use App\Models\Mapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;


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
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($keyword, $src, $creative, $cacheKey) {
            Log::channel('mapping')->debug('Cache miss, querying DB', [
                'cache_key' => $cacheKey,
                'keyword'   => $keyword,
                'src'       => $src,
                'creative'  => $creative,
            ]);

            return DB::transaction(function () use ($keyword, $src, $creative) {
                $row = Mapping::where(compact('keyword','src','creative'))
                            ->orderByDesc('version')
                            ->first();

                if ($row) {
                    Log::channel('mapping')->debug('Found existing mapping', [
                        'id'        => $row->id,
                        'our_param' => $row->our_param,
                        'version'   => $row->version,
                    ]);
                    return $row;
                }

                $new = Mapping::create([
                    'keyword'  => $keyword,
                    'src'      => $src,
                    'creative' => $creative,
                    'version'  => 1,
                ]);

                Log::channel('mapping')->debug('Created new mapping', [
                    'id'        => $new->id,
                    'our_param' => $new->our_param,
                    'version'   => $new->version,
                ]);

                return $new;
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

        $new = DB::transaction(function () use ($map) {
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
                } catch (UniqueConstraintViolationException $e) {
                    $version++;
                    continue;
                }
            }
        });

        Cache::put($cacheKey, $new, now()->addMinutes(60));
        return $new;
    }
}
