<?php

namespace Hrgweb\Botbuilders\Domain\Integration\Services;

use Illuminate\Support\Str;

class TimezoneService
{
    public function __construct(protected array $request)
    {
    }

    public static function info(...$request)
    {
        return (new static(...$request))->handle();
    }

    protected function handle()
    {
        $tz = strtoupper($this->request['timezone']);

        return [
            'timezone' => $tz,
            'gmt' => Str::replace('UTC', 'GMT', $tz)
        ];
    }
}
