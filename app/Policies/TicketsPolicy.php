<?php

namespace App\Policies;

class TicketsPolicy extends SnipePermissionsPolicy
{
    protected function columnName()
    {
        return 'tickets';
    }
}
