<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Schedule;
use App\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function map(Request $request)
    {
        // Lat/Lng de Android
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');
        $iAmAndroid = isset($lat) && isset($lng);
        $iAmSecure = $request->isSecure();

        // Lat/Lng de geoplugin
        if (!isset($lat)) {
            try {
                $ip = $request->getClientIp();

                $curl = curl_init();
                $options = [];
                $options[CURLOPT_URL] = 'http://www.geoplugin.net/php.gp?ip='.$ip;
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
            } catch (\Exception $e) {
                //dump($e);
            }
        }

        // Lat/Lng por defecto
        if (!$lat) $lat = $request->query->get('lat', $this->getParameter('google_maps_lat'));
        if (!$lng) $lng = $request->query->get('lng', $this->getParameter('google_maps_lng'));

        $file = $this->getDoctrine()->getRepository(File::class)->findOneBy(['active' => 1]);
        $key = $this->getParameter('google_maps_key');

        return $this->render('map.html.twig', [
            'key' => $key,
            'lat' => $lat,
            'lng' => $lng,
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
        return $this->find($lat, $lng, $radius, $gas, $opened);
    }

    private function find($lat, $lng, $radius, $gas = null, $opened = true)
    {
        $tmp = ['gas95' => [], 'gas98' => [], 'diesel' => []];

        $list = $this->getDoctrine()->getRepository(Station::class)->findByLatLng($lat, $lng, $radius, $gas);

        $data = [];
        foreach ($list as $item) {
            if ($opened === true) {
                $schedule = new Schedule($item['schedule']);
                if ($schedule->isClose()) {
                    continue;
                }
            }

            $item['id'] = 'id-'.$item['id'];
            if ($item['gas95'] > 0) {
                $tmp['gas95'][] = $item['gas95'];
            }
            if ($item['gas98'] > 0) {
                $tmp['gas98'][] = $item['gas98'];
            }
            if ($item['diesel'] > 0) {
                $tmp['diesel'][] = $item['diesel'];
            }
            $data[] = $item;
        }

        $media = [];
        if (count($data) > 0) {
            $media['gas95'] = $this->calculateMedia($tmp['gas95']);
            $media['gas98'] = $this->calculateMedia($tmp['gas98']);
            $media['diesel'] = $this->calculateMedia($tmp['diesel']);
        }

        return $this->json([
            'metadata' => [
                'total' => count($data),
                'media' => $media
            ],
            'data' => $data,
        ]);
    }

    private function calculateMedia($data) {
        sort($data);
        $it_low = $it_med = $it_hig = intdiv(count($data), 3);
        if ((count($data) % 3) == 2) {
            $it_low++; $it_med++;
        } else if ((count($data) % 3) == 1) {
            $it_low++;
        }

        $media = [
            'low' => [
                'min' => $data[0], 'max' => $data[$it_low - 1]
            ],
            'medium' => [
                'min' => $data[$it_med], 'max' => $data[($it_low + $it_med) - 1]
            ],
            'high' => [
                'min' => $data[min(count($data) - 1,($it_low + $it_med))], 'max' => $data[count($data) - 1]
            ],
        ];

        return $media;
    }
}
