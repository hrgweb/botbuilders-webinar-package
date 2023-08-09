<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Services;

use App\Jobs\IntegrationJob;

class RegistrantService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function make(...$request)
    {
        return new static(...$request);
    }

    public function schedules()
    {
        return RegistrantScheduleService::schedules($this->request);
    }

    public function save()
    {
        // Everwebinar and persist
        $data = EverWebinarService::make($this->request)->register();

        $data['eventNames'] = $this->request['eventNames'];

        // Integrations
        IntegrationJob::dispatch($data ?? $this->request);

        return [
            'uuid' => $data['uuid'],
            'affiliate' => $data['affiliate']
        ];
    }

    public function saveViaIntegration()
    {
        // Everwebinar and persist
        $data = EverWebinarService::make($this->request)->register();

        $data['eventNames'] = $this->request['eventNames'];

        // Integrations
        IntegrationJob::dispatch($data ?? $this->request);

        return $data;
    }
}
