<?php

namespace SoftOne\Http;

use SoftOne\Contracts\AuthInterface;
use SoftOne\Context;
use SoftOne\Exception\MissingApplicationBusinessObjectException;
use SoftOne\Exception\UninitializedContextException;

abstract class Request
{
    public function __construct()
    {

    }
}