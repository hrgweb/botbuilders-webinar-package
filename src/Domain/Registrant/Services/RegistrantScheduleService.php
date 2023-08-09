<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class RegistrantScheduleService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function schedules(...$request)
    {
        return (new static(...$request))->handle();
    }

    public function handle()
    {
        $url = config('botbuilders.everwebinar.url');
        $url .= '/webinar';

        $gmt = $this->request['gmt'];
        $tzname = $this->request['timezone'];
        $webinarId = $this->request['webinarId'];

        info('schedules fetched using webinar id: ' . $webinarId);

        $data = Http::retry(3, 100)->post($url, [
            'api_key' => config('botbuilders.everwebinar.api_key'),
            'webinar_id' => $webinarId,
            'timezone' => $gmt
        ])->throw()->json();

        if (!$data) {
            throw new Exception('everwebinar encountered an error.');
        }

        $schedules = $this->nearestTime(30, false);

        $current_date = Carbon::now()->tz($tzname);
        $hour = $current_date->format('H A');

        $jit = [
            'schedule' => $data['webinar']['schedules'][0]['schedule'],
            'date' =>  $data['webinar']['schedules'][0]['date'],
            'comment' => 'Today at ' . $hour . ' ' . $schedules[0] . ':' . $schedules[1],
            'minutes' => $schedules[0],
            'seconds' => $schedules[1]
        ];

        $new_value['name'] = $data['webinar']['name'];

        foreach ($data['webinar']['schedules'] as $key => $value) {
            $new_value['schedules'][] = $this->getDayFormatter($key, $value, $tzname);
        }

        return $new_value;
    }

    protected function getDayFormatter($key, $value, $tzname)
    {
        $curr_date = Carbon::parse($value['date'] . $tzname);

        return [
            'date' => $value['date'],
            'label' => $curr_date->calendar(),
            'schedule' => $value['schedule'],
            'jit' => ($key == 0) ? true : false
        ];
    }

    protected function nearestTime($param, $toJson = true)
    {
        $now = Carbon::now();
        $minute = $now->format('i');
        $second = $now->format('s');

        while ($minute > $param) {
            $minute = $minute - $param;
        }
        $minute = $param - $minute;
        $second = 60 - $second;

        if ($toJson) {
            return response()->json(['minutes' => $minute, 'seconds' => $second]);
        } else {
            return [$minute, $second];
        }
    }
}
