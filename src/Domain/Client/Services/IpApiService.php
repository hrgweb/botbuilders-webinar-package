<?php

namespace Hrgweb\Botbuilders\Domain\Client\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class IpApiService
{
    public function __construct(protected array $params = [])
    {
    }

    public static function data(...$params)
    {
        return (new static(...$params))->execute();
    }

    protected function ip()
    {
        // return request()->ip();
        return '49.145.233.38';
    }

    protected function info(): array
    {
        $url = config('botbuilders.ipapi.url');
        // $url .= '/' . $this->ip();
        $url .= '?key=' . config('botbuilders.ipapi.api_key');
        $url .= '&fields=query,status,message,timezone,countryCode,callingCode';

        return Http::retry(3, 100)->get($url)->throw()->json();
    }

    public function execute(): array
    {
        if (!$this->info()) {
            throw new Exception('ipapi service encountered an error.');
        }

        $data = $this->info();

        if (!$data || $data['status'] === 'fail') {
            throw new Exception('ipapi service no data retreived.');
        }

        date_default_timezone_set($data['timezone']);

        $data['gmt'] = 'GMT' . date('P');
        $data['gmt_raw'] = date('P');

        return $data;
    }
}
