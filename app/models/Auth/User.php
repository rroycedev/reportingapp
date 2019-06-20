<?php
// app/Models/Auth/User.php
namespace App\Models\Auth;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use App\Services\Contracts\NosqlServiceInterface;

class User implements AuthenticatableContract {
	private $conn;

	public $email;
	public $password;
	public $name;
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
		$this->email = $credentials['email'];
		$this->password = $credentials['password'];
		$this->name = "Ron Royce";

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