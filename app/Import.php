<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

class Import extends Model {

	public static function data(){
		date_default_timezone_set('Asia/Kuala_Lumpur');		
		$time_start=time();
		set_time_limit(0);

		DB::table('ztradedata')->truncate();
		
		$zstocks=DB::table('zstock')->where('status','>=',0)->get();
		
		foreach($zstocks as $zstock){
			$content=self::geturlcontents("http://hq.sinajs.cn/list={$zstock->code}");
			$a=explode('"',$content);
			$a1=explode('"',$a[1]);
			$aresult=explode(',',$a1[0]);

			if(count($aresult)>30){
				if($aresult[3]>0 and $aresult[8]>0){
					
					$date=$aresult[30];
					
					$afield=[
					'code'=>$zstock->code,
					'date'=>$date,
					'open'=>$aresult[1],
					'high'=>$aresult[4],
					'low'=>$aresult[5],
					'close'=>$aresult[3],
					'volume'=>$aresult[8]
					];

					DB::table('ztradedata')->insert($afield);
				}

			}
		}


		$time_lapse=time()-$time_start;
		file_put_contents('stockupdate.txt','Time Lapse:'.$time_lapse.' - '.date('Y-m-d H:i:s'));
		return $time_lapse;
	}

	public static function ydata(){
		set_time_limit(0);
		$zstocks=DB::table('zstock')->where('status','>=',0)->get();
		foreach($zstocks as $zstock){
			$code=substr($zstock->code,2);
			$postfix='sz';
			if(substr($zstock->code,0,2)=='sh')$postfix='ss';						
			$content=self::geturlcontents("http://table.finance.yahoo.com/table.csv?s={$code}.{$postfix}");
			if(strpos($content,'the page you requested was not found')=== false){
				$acontents=explode(chr(10),$content);
				$ztrade=DB::select("select max(date) as d from ztrade2015 where code='{$zstock->code}'");
				$mdate=$ztrade[0]->d;

				foreach($acontents as $k=>$v){
					$acontent=explode(',',$v);
					
					if($k){
						
						if($acontent[0]<=$mdate)break;
						if($acontent[0]<'2015-04-01')break;

						if($acontent[5]>0){

							DB::table('ztrade2015')->insert([
								'code'=>$zstock->code,
								'date' => $acontent[0],
								'open' => $acontent[1],
								'high' => $acontent[2],
								'low' => $acontent[3],
								'close' => $acontent[4],
								'volume' => $acontent[5]
								]
							);
						}
											
					}
				}
				//break;
			}else{
				//DB::table('zstock')->where('id',$zstock->id)->update(['status'=>-6]);
				$acontent=array();
			}			
		}
		return '<pre>'.$code.'</pre>';
	}

	public static function cdata(){
		set_time_limit(0);
		$time_start=time();

		$data=file_get_contents('data/NASDAQ_20150527.txt');
		$adata=explode(chr(10),$data);

		foreach($adata as $l=>$d){
			$a=explode(',',$d);

			if(count($a)>1 and trim($a[6])>0){

				DB::table('ztrade2015')->insert([
				'code'=>$a[0],
				'date'=>substr($a[1],0,4).'-'.substr($a[1],4,2).'-'.substr($a[1],6,2),
				'open'=>$a[2],
				'high'=>$a[3],
				'low'=>$a[4],
				'close'=>$a[5],
				'volume'=>trim($a[6]),
				]);

			}
		}				

		$time_lapse=time()-$time_start;
		return $time_lapse;	
	}

	public static function hdata(){
		set_time_limit(0);
		$time_start=time();
		$adir=scandir('data');
		foreach($adir as $k=>$fn){
			if(substr($fn,-4)=='.txt'){
				if($fn<'NASDAQ_20150101.txt')continue;
				$data=file_get_contents('data/'.$fn);
				$adata=explode(chr(10),$data);

				foreach($adata as $l=>$d){
					$a=explode(',',$d);

					if(count($a)>1 and trim($a[6])>0){

						DB::table('ztrade2015')->insert([
						'code'=>$a[0],
						'date'=>substr($a[1],0,4).'-'.substr($a[1],4,2).'-'.substr($a[1],6,2),
						'open'=>$a[2],
						'high'=>$a[3],
						'low'=>$a[4],
						'close'=>$a[5],
						'volume'=>trim($a[6]),
						]);

					}
				}				

			}

		}
		$time_lapse=time()-$time_start;
		return $time_lapse;	
	}

	public static function geturlcontents($url,$local_ip=''){

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($local_ip)curl_setopt($ch, CURLOPT_INTERFACE, $local_ip);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT,self::useragent());
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		$result=curl_exec($ch);
		curl_close($ch);			
		return $result;
	}

	public static function useragent(){
		$auseragent=array(
			'Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)',
			'Mozilla/5.0 (compatible; Konqueror/3.92; Microsoft Windows) KHTML/3.92.0 (like Gecko)',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0; .NET CLR 1.1.4322; Windows-Media-Player/10.00.00.3990; InfoPath.2',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; Dealio Deskball 3.0)',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; NeosBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
			'Opera/9.64(Windows NT 5.1; U; en) Presto/2.1.1',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)',
			'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.2 (KHTML, like Gecko) Chrome/4.0.221.7 Safari/532.2',
			'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.14) Gecko/2009082707 Firefox/3.0.14 (.NET CLR 3.5.30729)',
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)',
			'Mozilla/5.0 (X11; U; Linux i686; it-IT; rv:1.9.0.2) Gecko/2008092313 Ubuntu/9.25 (jaunty) Firefox/3.8',
		);
		$n=rand(0,count($auseragent)-1);
		return $auseragent[$n];
	}
}