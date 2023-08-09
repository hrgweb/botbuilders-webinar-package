<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Models;

use Illuminate\Database\Eloquent\Model;

class Registrant extends Model
{
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}