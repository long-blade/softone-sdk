<?php

namespace SoftOne\Contracts;

interface AuthInterface
{
    public function isAuthenticated(): bool;

    public function authorize();
}