<?php

namespace App\Exceptions;

use Exception;

class ShareExpiredException extends Exception
{
    protected $message = 'This share link has expired';
}
