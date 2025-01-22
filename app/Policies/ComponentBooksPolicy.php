<?php

namespace App\Policies;

class ComponentBooksPolicy extends SnipePermissionsPolicy
{
    protected function columnName()
    {
        return 'componentbooks';
    }
}