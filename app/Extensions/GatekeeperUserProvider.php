<?php
namespace App\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Log;

class GatekeeperUserProvider implements UserProvider {
	/**
	 * The Mongo User Model
	 */
	private $model;

	/**
	 * Create a new mongo user provider.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 * @return void
	 */
	public function __construct(\App\Models\Auth\User $userModel) {
		$this->model = $userModel;
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials) {
		Log::emergency('MongoUserProvider retrieving by credentials');

		if (empty($credentials)) {
			return;
		}

		$user = $this->model->fetchUserByCredentials($credentials);

		return $user;
	}

	public function createModel() {
		return new \App\Models\Auth\User;
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
	 * @param  array  $credentials  Request credentials
	 * @return bool
	 */
	public function validateCredentials(Authenticatable $user, Array $credentials) {
		return ($credentials['email'] == $user->getAuthIdentifier() &&
			md5($credentials['password']) == $user->getAuthPassword());
	}

	public function retrieveById($identifier) {
		$model = $this->createModel();
		$this->model = $model;

		$this->model->email = $identifier;

		return $model;
	}

	public function retrieveByToken($identifier, $token) {
		$model = $this->createModel();

		$retrievedModel = $this->newModelQuery($model)->where(
			$model->getAuthIdentifierName(), $identifier
		)->first();

		if (!$retrievedModel) {
			return;
		}

		$rememberToken = $retrievedModel->getRememberToken();

		return $rememberToken && hash_equals($rememberToken, $token)
		? $retrievedModel : null;

	}

	public function updateRememberToken(Authenticatable $user, $token) {}
}