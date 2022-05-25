<?php

namespace Spatie\Rdap\Enums;

enum EventAction: string
{
    case Registration = 'registration';
    case Expiration = 'expiration';
    case LastChanged = 'last changed';
    case LastUpdateOfRdapDb = 'last update of RDAP database';
}
