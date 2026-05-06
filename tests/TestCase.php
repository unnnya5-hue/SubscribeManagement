<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

abstract class TestCase extends BaseTestCase
{
    //
}
