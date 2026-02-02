<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int    $user_id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $passwordhash
 * @property string $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Booking[] $bookings
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	protected $table = 'users';
	protected $primaryKey = 'user_id';
	public $incrementing = true;
	public $keytype = 'int';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'surname',
		'email',
		'passwordhash',
		'role'
	];

	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}

	public function getAuthPasswordName()
	{
    	return 'passwordhash';
	}
}
