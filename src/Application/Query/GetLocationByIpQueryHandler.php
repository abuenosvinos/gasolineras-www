<?php


namespace App\Application\Query;


use App\Domain\ValueObject\Location;
use App\Shared\Domain\Bus\Query\QueryHandler;

class GetLocationByIpQueryHandler implements QueryHandler
{
    public function __invoke(GetLocationByIpQuery $query)
    {
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_URL] = 'http://www.geoplugin.net/php.gp?ip='.$query->ip();
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_FOLLOWLOCATION] = false;
        $options[CURLOPT_FAILONERROR] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT_MS] = 1000;
        curl_setopt_array($curl, $options);
        $data = curl_exec($curl);
        curl_close($curl);
        $data = unserialize($data);

        $lat = $data['geoplugin_latitude'];
        $lng = $data['geoplugin_longitude'];

        return new Location($lat, $lng);
    }
}