<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class member extends Model
{
	protected $table = 'member';
	protected $primaryKey = 'tetsid';
	public $incrementing = false;
}
