<?php
namespace database;

class strPosArray
{
    /**
     * @param array $needle
     * @param string $haystack
     * @return bool
     */
    public static function strPos(array $needle, string $haystack){
        foreach ($needle as $search){
            if(str_contains($haystack, $search)){
                return true;
                break;
            }
        }
        return false;
    }
}