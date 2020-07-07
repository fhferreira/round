<?php

namespace Fhferreira\Round\Exceptions;

class CommaException extends \Exception
{
    protected $message = 'Comma-formatted numbers are not allowed.';
}
