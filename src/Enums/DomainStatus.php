<?php

namespace Spatie\Rdap\Enums;

enum DomainStatus: string
{
    case ClientDeleteProhibited = "client delete prohibited";
    case ClientTransferProhibited = "client transfer prohibited";
    case ClientUpdateProhibited = "client update prohibited";
    case ServerDeleteProhibited = "server delete prohibited";
    case ServerTransferProhibited = "server transfer prohibited";
    case ServerUpdateProhibited = "server update prohibited";
}
