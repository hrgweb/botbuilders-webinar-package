<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;

class RegistrantData extends Data
{
    public function __construct(
        public string $affiliate,
        #[MapOutputName('first_name')]
        public string $firstName,
        #[Email] //, Unique(Registrant::class, 'email')]
        public string $email,
        public ?string $phone,
        #[MapOutputName('phone_calling_code')]
        public ?string $phoneCallingCode,
        #[MapOutputName('phone_country_code')]
        public ?string $phoneCountryCode,
        public ?string $ip,
        // Webinar
        #[MapOutputName('webinar_title')]
        public ?string $webinarTitle,
        #[MapOutputName('webinar_date'), Date]
        public ?string $webinarDate,
        #[MapOutputName('webinar_date_label')]
        public ?string $webinarDateLabel,
        #[MapOutputName('schedule_id')]
        public ?int $schedule,
        public ?string $timezone,
        public ?string $gmt,
        #[MapOutputName('is_jit')]
        public ?bool $isJit,
        // Page
        public ?string $page,
        public ?int $pageId,
        public ?string $pageName,
        public ?string $replay_link,
        public ?string $confirmation_link,
        public ?string $custom_live_url,
        public ?string $custom_thankyou_url,
        // Utms
        public ?string $utm_source,
        public ?string $utm_campaign,
        public ?string $utm_medium,
        public ?string $utm_content,
        public ?string $utm_term,
        // Misc
        public ?string $webinar_api,
        #[MapOutputName('webinar_id')]
        public int $webinarId,
        public array $eventNames,
        public ?int $epoch,
        public ?array $kartra
    ) {
    }

    public static function attributes(): array
    {
        return [
            // 'firstName' => 'first_name'
        ];
    }
}
