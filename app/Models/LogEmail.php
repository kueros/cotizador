<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEmail extends Model
{
    use HasFactory;
	protected $table = 'logs_emails'; 
	protected $fillable = [
		'email',
		'detalle',
		'enviado',
		'ip_address',
		'user_agent'
	];
}
