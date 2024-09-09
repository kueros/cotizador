<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use HasFactory;

	protected $table = 'variables';
	protected $primaryKey = 'id';
	protected $fillable = ['nombre', 'valor'];
}
