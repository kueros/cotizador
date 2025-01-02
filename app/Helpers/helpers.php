<?php
use App\Http\Controllers\MyController;

function tiene_permiso($permiso)
{
	$myController = new MyController();
	return $myController->tiene_permiso($permiso);
}