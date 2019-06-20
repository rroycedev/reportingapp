<?php

namespace App\Providers;

use App\Extensions\GatekeeperUserProvider;
use App\Services\Auth\GatekeeperGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		// 'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();

		Passport::routes();

		Passport::tokensExpireIn(now()->addDays(15));
		Passport::refreshTokensExpireIn(now()->addDays(30));

		// add custom guard provider
		Auth::provider('gatekeeper', function ($app, array $config) {
			return new GatekeeperUserProvider($app->make('App\Models\Auth\User'));
		});

		// add custom guard
		Auth::extend('gatekeeper', function ($app, $name, array $config) {
			return new GatekeeperGuard($name, Auth::createUserProvider($config['provider']), $this->app['session.store'], $app->make('request'));
		});

	}
}
