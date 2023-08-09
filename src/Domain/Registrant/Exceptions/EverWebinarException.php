<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Exceptions;

use Exception;

class EverWebinarException extends Exception
{
    protected $message = 'Everwebinar failed to register. Check your mobile number make sure it is valid';
}
