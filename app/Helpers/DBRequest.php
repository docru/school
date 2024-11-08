<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DBRequest
{
    private static function getSql($code)
    {
        $path = database_path('sql_requests/' . str_replace('.', '/', trim($code, ' /')) . '.sql');
        throw_if(!is_file($path));
        return file_get_contents($path);
    }

    public static function statement($code, $bindings = [])
    {
        $sql = static::getSql($code);
        $res = DB::statement($sql, $bindings);
        return $res;
    }

    public static function select($code, $bindings = [])
    {
        $sql = static::getSql($code);
        $arNewVals = [];
        foreach ($bindings as $k => $binding) {
            if (is_array($binding)) {
                $arNewBindings = [];
                foreach ($binding as $i => $item) {
                    $key = $k . '_' . $i;
                    $arNewBindings[] = $key;
                    $arNewVals[$key] = $item;
                }
                unset($bindings[$k]);
                $sql = str_replace($k, implode(',', $arNewBindings), $sql);
            }
        }
        if (count($arNewVals) > 0) {
            $bindings = array_merge($bindings, $arNewVals);
        }

        $res = DB::select($sql, $bindings);
        return $res;
    }

    public static function update($code, $bindings = [])
    {
        $sql = static::getSql($code);
        $res = DB::update($sql, $bindings);
        return $res;
    }

}
