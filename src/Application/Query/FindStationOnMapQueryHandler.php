<?php


namespace App\Application\Query;


use App\Domain\ValueObject\Schedule;
use App\Infrastructure\Doctrine\Repository\StationRepository;
use App\Shared\Domain\Bus\Query\Query;

class FindStationOnMapQueryHandler extends Query
{
    private $repository;

    public function __construct(StationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindStationOnMapQuery $query)
    {
        $list = $this->repository->search($query->lat(), $query->lng(), $query->radius(), $query->gas());
        $opened = $query->opened();

        $tmp = ['gas95' => [], 'gas98' => [], 'diesel' => []];

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

        return [
            'metadata' => [
                'total' => count($data),
                'media' => $media
            ],
            'data' => $data,
        ];
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
