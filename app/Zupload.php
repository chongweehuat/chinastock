<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

class Zupload extends Model {

	public static function lastid(){
		return DB::table('ztrade')->max('id');
	}

	public static function data(){
		$lastid=file_get_contents('http://chinastock.app/upload/lastid');
		$maxid=DB::table('ztrade0')->max('id');
		if(empty($maxid))$maxid=0;
		$result=$lastid;
		if($lastid>$maxid){
			$ztrade=DB::select("select * from ztrade where id>$maxid limit 100");
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL,"http://chinastock.app/upload/post?_token={{csrf_token()}}");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,array('data'=>serialize($ztrade)));		

			$result=curl_exec ($ch);

			curl_close ($ch);
		}
		return $result;
	}

	public static function post(){
		$data=Input::get('data');
		$ztrade=unserialize($data);
		foreach($ztrade as $z){
			DB::table('ztrade0')->insert((array)$z);
		}
	}
}