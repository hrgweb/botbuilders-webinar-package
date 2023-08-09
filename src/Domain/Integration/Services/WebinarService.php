<?php

namespace Hrgweb\Botbuilders\Domain\Integration\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Domain\Registrant\Exceptions\EverWebinarException;

class WebinarService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function register(...$request)
    {
        return (new static(...$request))->handle();
    }

    protected function getDateOrTime(string $dt): array
    {
        // return explode(' ', $dt);
        return [
            'date' => Carbon::parse($dt)->toFormattedDateString(),
            'time' => Carbon::parse($dt)->isoFormat('hh:mm A')
        ];
    }

    protected function getDay(string $dt, string $tz): string
    {
        $now = Carbon::parse($dt, $tz);

        $result = explode('at', $now->calendar());

        return trim($result[0]);
    }

    protected function handle()
    {
        $url = config('everwebinar.url');
        $url .= '/webinar';

        $gmt = $this->request['gmt'] ?? '';

        $data = Http::post($url, [
            'api_key' => $this->request['webinar_api_key'] ??= '',
            'webinar_id' => $this->request['webinar_id'] ??= '',
            'timezone' => $gmt
        ])->throw()->json();

        if (!$data || count($data) <= 0) {
            throw new EverWebinarException('everwebinar encounter an error while fetching schedules.');
        }

        // $schedules = $this->nearestTime(30, false);

        // $current_date = Carbon::now()->tz($timezone);
        // $hour = $current_date->format('H A');

        // $jit = [
        //     'schedule' => $data['webinar']['schedules'][0]['schedule'],
        //     'date' =>  $data['webinar']['schedules'][0]['date'],
        //     'comment' => 'Today at ' . $hour . ' ' . $schedules[0] . ':' . $schedules[1],
        //     'minutes' => $schedules[0],
        //     'seconds' => $schedules[1]
        // ];

        $result = [];
        $result['status'] = $data['status'];

        foreach ($data['webinar'] as $key => $value) {
            $result['webinar'][$key] = $value;
        }

        $schedules = $result['webinar']['schedules'];
        $timezone = $data['webinar']['timezone'];

        // Loop through webinar
        foreach ($schedules as $key => $value) {
            $result['webinar']['schedules'][$key] = $value;

            $dt = $this->getDateOrTime($schedules[$key]['date']);

            if ($dt && count($dt)) {
                $result['webinar']['schedules'][$key]['human_date'] = $dt['date']; // $dt[0];
                $result['webinar']['schedules'][$key]['human_time'] = $dt['time']; // $dt[1];
                $result['webinar']['schedules'][$key]['day'] = $this->getDay($schedules[$key]['date'], $timezone);
            }
        }

        return $result;
    }

    // protected function getDayFormatter($key, $value, $tzname)
    // {
    //     $curr_date = Carbon::parse($value['date'] . $tzname);

    //     return [
    //         'date' => $value['date'],
    //         'label' => $curr_date->calendar(),
    //         'schedule' => $value['schedule'],
    //         'jit' => ($key == 0) ? true : false
    //     ];
    // }

    // protected function nearestTime($param, $toJson = true)
    // {
    //     $now = Carbon::now();
    //     $minute = $now->format('i');
    //     $second = $now->format('s');

    //     while ($minute > $param) {
    //         $minute = $minute - $param;
    //     }
    //     $minute = $param - $minute;
    //     $second = 60 - $second;

    //     if ($toJson) {
    //         return response()->json(['minutes' => $minute, 'seconds' => $second]);
    //     } else {
    //         return [$minute, $second];
    //     }
    // }
}
