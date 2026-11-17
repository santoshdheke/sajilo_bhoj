<?php

namespace Modules\School\Exceptions;

use RuntimeException;

class CannotDeleteOwnAccountException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('You cannot delete your own account.');
    }
}
