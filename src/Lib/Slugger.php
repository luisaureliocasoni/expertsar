<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16/01/17
 * Time: 14:34
 */

namespace Lib;


class Slugger
{
    private static $slugfier;

    private function __construct(){

    }

    public static function geraSlug($string){
        if (self::$slugfier == null){
            self::$slugfier = new \Slug\Slugifier();
            self::$slugfier->setTransliterate(true);
        }

        return self::$slugfier->slugify($string);
    }
}
