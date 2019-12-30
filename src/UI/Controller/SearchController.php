<?php

namespace App\UI\Controller;

use App\Application\Query\FindActiveFileQuery;
use App\Application\Query\FindStationOnMapQuery;
use App\Application\Query\GetLocationByIpQuery;
use App\Domain\Entity\File;
use App\Domain\ValueObject\Location;
use App\Domain\ValueObject\Schedule;
use App\Domain\Entity\Station;
use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController
{
    public function map(Request $request)
    {
        // Lat/Lng de Android
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');
        $iAmAndroid = isset($lat) && isset($lng);
        $iAmSecure = $request->isSecure();

        if (!isset($lat)) {
            try {
                $ip = $request->getClientIp();
                /** @var Location $location */
                $location = $this->ask(new GetLocationByIpQuery($ip));
                $lat = $location->lat();
                $lng = $location->lng();

            } catch (\Exception $e) {
                //dump($e);
            }
        }

        // Lat/Lng por defecto
        if (!$lat) $lat = $request->query->get('lat', $this->getParameter('google_maps_lat'));
        if (!$lng) $lng = $request->query->get('lng', $this->getParameter('google_maps_lng'));

        /** @var File $file */
        $file = $this->ask(new FindActiveFileQuery());
        $key = $this->getParameter('google_maps_key');
        $gasSelected = $request->cookies->get('gasSelected', 'gas98');

        return $this->render('map.html.twig', [
            'key' => $key,
            'lat' => $lat,
            'lng' => $lng,
            'gasSelected' => $gasSelected,
            'last_update' => $file->getName(),
            'iAmAndroid' => $iAmAndroid,
            'iAmSecure' => $iAmSecure
        ]);
    }

    public function search(Request $request)
    {
        $lat = $request->query->get('lat', $this->getParameter('google_maps_lat'));
        $lng = $request->query->get('lng', $this->getParameter('google_maps_lng'));
        $radius = $request->query->get('radius', $this->getParameter('google_maps_radius'));
        $gas = $request->query->get('gas');
        $opened = $request->query->getBoolean('opened', true);

        $response = $this->find($lat, $lng, $radius, $gas, $opened);
        $response->headers->setCookie(Cookie::create('gasSelected', $gas));
        return $response;
    }

    private function find($lat, $lng, $radius, $gas = null, $opened = true)
    {
        $list = $this->ask(new FindStationOnMapQuery($lat, $lng, $radius, $gas, $opened));

        return $this->json($list);
    }

}
