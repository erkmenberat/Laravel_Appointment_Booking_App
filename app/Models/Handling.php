<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Handling
 * 
 * @property int $handling_id
 * @property string $name
 * 
 * @property Collection|Booking[] $bookings
 *
 * @package App\Models
 */
class Handling extends Model
{
	protected $table = 'handling';
	protected $primaryKey = 'handling_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'handling_id' => 'int'
	];

	protected $fillable = [
		'name'
	];

	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}
}
