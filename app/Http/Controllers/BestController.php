<?php namespace App\Http\Controllers;

use App\Ztrade;
use App\Zstock;
use App\Zstudy;
use App\Zdstudy;
use App\Zstudy1;

class BestController extends Controller {

	public function index($code='')
	{
		return Ztrade::plist($code);
	}

	public function updatelatest($section)
	{
		return Zstock::updatelatest($section);
	}

	public function study($page)
	{
		if(empty($page))return Zstudy::plist();
		elseif($page==1)return Zstudy1::plist();
		elseif($page==11)return Zstudy1::bestlist();
		elseif($page==12)return Zstudy1::winnerlist();
		elseif($page==13)return Zstudy1::winnerdate();
		elseif($page==2)return Zstudy1::maxwin();
		elseif($page==3)return Zdstudy::plist();
	}
}
