<?php

namespace App\Services;

use App\Models\Mapping;
use Illuminate\Support\Facades\DB;

class MappingService
{
    /**
     * Retrieve existing mapping or create a new one.
     *
     * @param string $keyword
     * @param string $src
     * @param string $creative
     * @return Mapping
     */
    public function getOrCreate(string $keyword, string $src, string $creative): Mapping
    {
        return DB::transaction(function () use ($keyword, $src, $creative) {
            // Try to find existing mapping with the highest version
            $row = Mapping::where(compact('keyword', 'src', 'creative'))
                          ->orderByDesc('version')
                          ->first();

            if ($row) {
                // Return the existing record
                return $row;
            }

            // Create a new mapping record;
            return Mapping::create([
                'keyword'  => $keyword,
                'src'      => $src,
                'creative' => $creative,
                'version'  => 1, 
            ]);
        });
    }

    /**
     * Create a fresh version of an existing mapping.
     *
     * @param Mapping $map
     * @return Mapping
     */
    public function refresh(Mapping $map): Mapping
    {
        return DB::transaction(function () use ($map) {
            // Insert a new version with version incremented by 1
            return Mapping::create([
                'keyword'     => $map->keyword,
                'src'         => $map->src,
                'creative'    => $map->creative,
                'version'     => $map->version + 1,
                'refreshed_at'=> now(),
            ]);
        });
    }
}
