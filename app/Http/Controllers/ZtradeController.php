<?php namespace App\Http\Controllers;

use App\Import;
use App\LNupdate;
use App\Study;
use App\Best;
use App\Invest;
use App\Render;

use Input;

class ZtradeController extends Controller {
	
	public function import($n)
	{
		return Import::data($n);
	}

	public function lnupdate()
	{
		return LNupdate::data();
	}

	public function study()
	{
		return Study::data();
	}

	public function best()
	{
		return Best::data();
	}

	public function invest()
	{
		return Invest::data();
	}

	public function render($mode)
	{
		if($mode=='invest')return Render::invest();
		elseif($mode=='pcrange')return Render::pcrange(Input::get('d'),Input::get('r'),Input::get('n'),Input::get('t'));
		elseif($mode=='study')return Render::study();
	}

}
