<?php

namespace JoggApp\MailViewer\Tests;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use JoggApp\MailViewer\MailViewerServiceProvider;
use JoggApp\MailViewer\Tests\Stubs\Mail\TestEmailForMailViewer;
use JoggApp\MailViewer\Tests\Stubs\Mail\TestEmailWithDependencies;
use JoggApp\MailViewer\Tests\Stubs\Mail\TestEmailWithState;
use JoggApp\MailViewer\Tests\Stubs\Models\Test;
use Orchestra\Testbench\TestCase;

class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [MailViewerServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.env', 'local');

        $app['config']->set(
            'mailviewer.mailables',
            [
                TestEmailForMailViewer::class => [],
                TestEmailWithDependencies::class => [
                    [],
                    \stdClass::class,
                    'Some name',
                    7
                ],
                TestEmailWithState::class => [
                    [
                        'class' => Test::class,
                        'states' => ['is-awesome']
                    ]
                ]
            ]
        );

        $app['config']->set('mailviewer.url', 'mails');
        $app['config']->set('mailviewer.allowed_environments', ['local', 'staging', 'testing']);
        $app['config']->set('mailviewer.middlewares', []);

        $app->singleton(EloquentFactory::class, function ($app) {
            $faker = $app->make(\Faker\Generator::class);
            $factories_path = __DIR__ . '/Factories';

            return EloquentFactory::construct($faker, $factories_path);
        });
    }
}
