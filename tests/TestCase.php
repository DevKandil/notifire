<?php

namespace DevKandil\NotiFire\Tests;

use DevKandil\NotiFire\FcmServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            FcmServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Use real Firebase configurations from .env
        $app['config']->set('fcm.project_id', env('FIREBASE_PROJECT_ID'));
        $app['config']->set('fcm.credentials_path', storage_path('firebase.json'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}