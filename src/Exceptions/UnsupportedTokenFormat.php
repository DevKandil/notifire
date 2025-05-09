<?php

namespace DevKandil\NotiFire\Exceptions;

use Exception;

class UnsupportedTokenFormat extends Exception
{
    public function __construct()
    {
        parent::__construct('Unsupported token format. Token must be a string or an array of strings.');
    }
}