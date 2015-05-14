<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('test',function(){
	return file_get_contents("http://hq.sinajs.cn/list=sh601006");
	//return App\Zstock::geturlcontents("http://hq.sinajs.cn/list=sh601006");
	//return App\Zbest::vdownpup();

	//return date('Y-m-d',time()-(3600*24*100));
	//return App\Ztrade::plist();

	//return App\Zbest::bestpattern();

	//return App\Zupdate::updatecsv();
	//return App\Zupdate::importcsv();
	
	//return App\Zupdate::lastdata();
	//return App\Ztest::checkpattern();
	//
	//return App\Ztest::vibrate();
	//
	//return App\Ztest::toppattern('2015-04-08','2015-04-09');
	//return phpinfo();	
	//return App\Zstock::importhistory();
});

Route::get('/updatepattern',function(){	
	return App\Zupdate::lastdata();
	//return App\Ztest::updatepattern();
});

Route::get('/datepattern',function(){		
	return App\Zpattern::plist();
});

Route::get('/best/{code}', 'BestController@index');

Route::get('/bestpattern',function(){		
	return App\Zbest::bestpattern(Input::get('date'),Input::get('pattern'),Input::get('p'));
});

Route::get('/bestconclude',function(){			
	return App\Zbest::datelist();
});

Route::get('/analytic',function(){		
	return App\Zanalytic::analytic();
});

Route::get('/analytic1',function(){		
	return App\Zanalytic1::analytic();
});

Route::get('/topvol',function(){		
	return App\Zstock::topvol();
});

Route::get('/topp1',function(){		
	return App\Zstock::topp1();
});

Route::get('/topp00',function(){		
	return App\Zstock::topp1(-1);
});

Route::get('/topp0',function(){		
	return App\Zstock::topp1(0);
});

Route::get('/similar',function(){		
	return App\Zstock::similar(Input::get('c'));
});

Route::get('/updatelatest',function(){
	return App\Zstock::updatelatest();
});

Route::get('/updatechanges',function(){
	return App\Zstock::updatechanges();
});

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
