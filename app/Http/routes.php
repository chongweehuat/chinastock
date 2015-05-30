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

Route::get('/imports',function(){
	return '<frameset cols="25%,*,25%">
			  <frame src="/import/1">
			  <frame src="/import/2">
			  <frame src="/import/3">
			  <frame src="/import/4">
			</frameset>';
});

Route::get('/import/{code}', 'ZtradeController@import');

Route::get('test',function(){
	return App\Import::data();
	//return App\Zstock::importhistory();
	//return App\Zupdate::lastdata();
	//return App\Ztest::removetrade();
	
	//return App\Zdstudy::plist();
	//return App\Zdstudy::cleandb();

	//return App\Zstudy1::maxwin();

	//return App\Zstock::updatename();	

	
});

Route::get('/upload/lastid',function(){
	return App\Zupload::lastid();
});

Route::get('/upload/data',function(){
	return App\Zupload::data();
});

Route::get('/updatelatest',function(){
	return App\Zstock::updatelatest(0);
});

Route::get('/study/{page}','BestController@study');

Route::get('/updatelatest/{section}','BestController@updatelatest');

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


Route::get('/updatechanges',function(){
	return App\Zstock::updatechanges();
});

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
