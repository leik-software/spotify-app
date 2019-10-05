<?php declare(strict_types=1);

namespace App\Helper;

final class TrackHashHelper
{
    public static function generateHash(\stdClass $track): string
    {
        $hashBase = $track->name;
        $artistNames=[];
        foreach ($track->artists as $artist){
            $artistNames[] = $artist->name;
        }
        sort($artistNames);
        $hashBase.=implode('-',$artistNames);
        return md5($hashBase);
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
