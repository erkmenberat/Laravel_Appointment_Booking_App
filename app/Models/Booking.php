<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 * 
 * @property int $booking_id
 * @property int $user_id
 * @property int $handling_id
 * @property Carbon|null $startsat
 * @property Carbon|null $endsat
 * @property string|null $usernote
 * @property string|null $staffnote
 * @property string $state
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property User $user
 * @property Handling $handling
 *
 * @package App\Models
 */
class Booking extends Model
{
	protected $table = 'booking';
	protected $primaryKey = 'booking_id';
	public $incrementing = false;

	protected $casts = [
		'booking_id' => 'int',
		'patient_id' => 'int',
		'handling_id' => 'int',
		'date' => 'date',
		'startsat' => 'time',
		'endsat' => 'time'
	];

	protected $fillable = [
		'patient_id',
		'handling_id',
		'startsat',
		'endsat',
		'usernote',
		'staffnote',
		'state'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function handling()
	{
		return $this->belongsTo(Handling::class);
	}
}
