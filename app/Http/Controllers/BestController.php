<?php namespace App\Http\Controllers;

use App\Ztrade;

class BestController extends Controller {

	public function index($code='')
	{
		return Ztrade::plist($code);
	}
}
