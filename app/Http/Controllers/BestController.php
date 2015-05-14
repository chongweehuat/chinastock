<?php namespace App\Http\Controllers;

use App\Ztrade;
use App\Zstock;

class BestController extends Controller {

	public function index($code='')
	{
		return Ztrade::plist($code);
	}

	public function updatelatest($section)
	{
		return Zstock::updatelatest($section);
	}
}
