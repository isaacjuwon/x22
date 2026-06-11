<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
<<<<<<< HEAD

abstract class TestCase extends BaseTestCase
{
    //
=======
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
>>>>>>> 39d8a93ad41414dfcb6cdcc58894db1308285e6a
}
