<?php
// app/Models/Auth/User.php
namespace App\Models\Auth;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use App\Services\Contracts\NosqlServiceInterface;
use Illuminate\Support\Facades\Log;

class User implements AuthenticatableContract {
	private $conn;

	public $email;
	public $password;
	protected $rememberTokenName = 'email';

	public function __construct() {
		// $this->conn = $conn;
	}

	/**
	 * Fetch user by Credentials
	 *
	 * @param array $credentials
	 * @return Illuminate\Contracts\Auth\Authenticatable
	 */
	public function fetchUserByCredentials(Array $credentials) {
		Log::emergency('fetchUserByCredentials: ' . json_encode($credentials));

		$this->email = $credentials['email'];
		$this->password = $credentials['password'];

		/*
			    $arr_user = $this->conn->find('users', ['username' => $credentials['username']]);

			    if (! is_null($arr_user)) {
			      $this->username = $arr_user['username'];
			      $this->password = $arr_user['password'];
			    }
		*/

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifierName()
	 */
	public function getAuthIdentifierName() {
		return "email";
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifier()
	 */
	public function getAuthIdentifier() {
		return $this->{$this->getAuthIdentifierName()};
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthPassword()
	 */
	public function getAuthPassword() {
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberToken()
	 */
	public function getRememberToken() {
		if (!empty($this->getRememberTokenName())) {
			return $this->{$this->getRememberTokenName()};
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::setRememberToken()
	 */
	public function setRememberToken($value) {
		if (!empty($this->getRememberTokenName())) {
			$this->{$this->getRememberTokenName()} = $value;
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberTokenName()
	 */
	public function getRememberTokenName() {
		return $this->rememberTokenName;
	}
}