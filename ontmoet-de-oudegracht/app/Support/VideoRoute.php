<?php

namespace App\Support;

class VideoRoute
{
    public static function progress(float $latitude, float $longitude): float
    {
        $route = config('video.route');
        $segments = [];
        $totalLength = 0.0;

        for ($index = 1; $index < count($route); $index++) {
            $start = $route[$index - 1];
            $end = $route[$index];
            $length = self::distance($start[0], $start[1], $end[0], $end[1]);
            $segments[] = [$start, $end, $length, $totalLength];
            $totalLength += $length;
        }

        if ($totalLength === 0.0) {
            return 0.0;
        }

        $closestDistance = INF;
        $routeDistance = 0.0;

        foreach ($segments as [$start, $end, $length, $startDistance]) {
            $referenceLatitude = deg2rad(($start[0] + $end[0] + $latitude) / 3);
            $startX = deg2rad($start[1]) * cos($referenceLatitude);
            $startY = deg2rad($start[0]);
            $endX = deg2rad($end[1]) * cos($referenceLatitude);
            $endY = deg2rad($end[0]);
            $pointX = deg2rad($longitude) * cos($referenceLatitude);
            $pointY = deg2rad($latitude);
            $deltaX = $endX - $startX;
            $deltaY = $endY - $startY;
            $segmentLengthSquared = $deltaX ** 2 + $deltaY ** 2;
            $segmentProgress = $segmentLengthSquared > 0
                ? max(0.0, min(1.0, (($pointX - $startX) * $deltaX + ($pointY - $startY) * $deltaY) / $segmentLengthSquared))
                : 0.0;
            $projectedX = $startX + $deltaX * $segmentProgress;
            $projectedY = $startY + $deltaY * $segmentProgress;
            $distanceSquared = ($pointX - $projectedX) ** 2 + ($pointY - $projectedY) ** 2;

            if ($distanceSquared < $closestDistance) {
                $closestDistance = $distanceSquared;
                $routeDistance = $startDistance + $length * $segmentProgress;
            }
        }

        return max(0.0, min(1.0, $routeDistance / $totalLength));
    }

    public static function timestamp(float $latitude, float $longitude, float $duration): float
    {
        return round(self::progress($latitude, $longitude) * $duration, 3);
    }

    private static function distance(float $startLatitude, float $startLongitude, float $endLatitude, float $endLongitude): float
    {
        $earthRadius = 6371000;
        $latitudeDifference = deg2rad($endLatitude - $startLatitude);
        $longitudeDifference = deg2rad($endLongitude - $startLongitude);
        $a = sin($latitudeDifference / 2) ** 2
            + cos(deg2rad($startLatitude)) * cos(deg2rad($endLatitude))
            * sin($longitudeDifference / 2) ** 2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }
}
