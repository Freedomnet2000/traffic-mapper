<?php

namespace App\Services;

use App\Models\Mapping;
use Illuminate\Support\Facades\DB;

class MappingService
{
    public function getOrCreate(string $keyword,string $src,string $creative): Mapping
    {
        return DB::transaction(function () use ($keyword,$src,$creative) {
            $row = Mapping::where(compact('keyword','src','creative'))
                          ->orderByDesc('version')->first();
            if ($row) return $row;

            $nextId = DB::table('mappings')->max('id') + 1;
            $ourParam = $this->encodeBase62($nextId);

            return Mapping::create([
                'keyword'=>$keyword,
                'src'=>$src,
                'creative'=>$creative,
                'our_param'=>$ourParam,
            ]);
        });
    }

    public function refresh(Mapping $map): Mapping
    {
        return DB::transaction(function () use ($map) {
            $nextId = DB::table('mappings')->max('id') + 1;
            $newParam = $this->encodeBase62($nextId);

            return Mapping::create([
                'keyword'=>$map->keyword,
                'src'=>$map->src,
                'creative'=>$map->creative,
                'version'=>$map->version + 1,
                'our_param'=>$newParam,
                'refreshed_at'=>now(),
            ]);
        });
    }

    private function encodeBase62(int $num): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $out = '';
        do { $out = $chars[$num % 62] . $out; $num = intdiv($num,62); }
        while ($num > 0);
        return str_pad($out,10,'0',STR_PAD_LEFT);
    }
}

