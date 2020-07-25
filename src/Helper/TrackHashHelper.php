<?php declare(strict_types=1);

namespace App\Helper;

final class TrackHashHelper
{
    public static function generateHash(\stdClass $track): string
    {
        $hashBase = $track->name;
        $hashBase.=self::getArtistNames($track);
        return md5(strtolower($hashBase));
    }

    public static function getArtistNames(\stdClass $track): string
    {
        $artistNames=[];
        foreach ($track->artists as $artist){
            $artistNames[] = $artist->name;
        }
        sort($artistNames);
        return implode(',',$artistNames);
    }
}
