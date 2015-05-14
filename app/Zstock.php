<?php namespace App;
/*
http://www.bizeway.net/read.php?317

http://www.21andy.com/new/20090530/1313.html

http://hq.sinajs.cn/list=sh601006

http://image.sinajs.cn/newchart/daily/n/sh601006.gif
http://image.sinajs.cn/newchart/min/n/sh000001.gif

深市数据链接：http://table.finance.yahoo.com/table.csv?s=000001.sz
上市数据链接：http://table.finance.yahoo.com/table.csv?s=600000.ss
上证综指代码：000001.ss，深证成指代码：399001.SZ，沪深300代码：000300.ss

SELECT * FROM `zstock` where `price20change`<20 and `price20change`>15 and price10change>12 and price5change>10 and price3change>2 and price1change<10 

*/

use Illuminate\Database\Eloquent\Model;

use DB;

class Zstock extends Model {

	protected $fillable = ['*'];

	public static function updatevrate(){
		$time_start=time();
		DB::statement("update zanalytic set vrate=round(100*((2*volume10/volume20)-1),2) where volume10<>volume20");
		$time_lapse=time()-$time_start;
		file_put_contents('milestone.txt','updatevrate: '.$time_lapse.' / '.date('Y-m-d H:i:s'));
	}

	public static function topp1($up=1){

		$zstock=DB::table('zanalytic')
			->orderby('date','desc')
			->first();

		$date=$zstock->date;		

		if($up>0){
			$arows=DB::table('zanalytic')
				->where('date','=',$date)
				->orderby('price1change','desc')
				->take(30)
				->get();
		}elseif($up<0){
			$arows=DB::table('zanalytic')
				->where('date','=',$date)
				->where('price1change','>',-11)
				->orderby('price1change','asc')
				->take(30)
				->get();
		}else{
			$arows=DB::table('zanalytic')
				->where('date','=',$date)
				->where('price0change','<',0)
				->where('vrate','<',40)
				->where('vrate','>',10)
				->orderby('price0change','asc')
				->take(30)
				->get();
		}

		$html="<table>";
		
		foreach($arows as $k=>$arow){
			$vrate=$arow->vrate;
			$zstock=DB::table('zstock')->find($arow->zstock_id);
			$ztrade=DB::table('ztrade')->where('zstock_id',$arow->zstock_id)->where('date',$date)->get();
			$html.="<tr>";
			$html.="<td>";

			$html.="<table>";
			$html.="<tr>";
			$html.="<td>ID</td>";
			$html.="<td>".$zstock->id."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Code</td>";
			$html.="<td>";
			$html.="<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			$html.=$zstock->code;
			$html.="</a></td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Name</td>";
			$html.="<td>";
			$html.="<a href='/similar/?c={$zstock->code}' href=_blank>";
			$html.=$zstock->name;
			$html.="</a>";
			$html.="</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price</td>";
			$html.="<td>".$ztrade[0]->close."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price Change</td>";
			$html.="<td>".$arow->price1change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Vibration</td>";
			$html.="<td>".$arow->price0change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Volume Change</td>";
			$html.="<td>".$vrate."</td>";
			$html.="</tr>";
			$html.="</table>";
			
			$html.="</td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/daily/n/{$zstock->code}.gif'></td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/min/n/{$zstock->code}.gif'></td>";
			$html.="</tr>";
		}
		$html.="</table>";
//return count($arows);
		return $html;

	}
	
	public static function topvol(){

		$zstock=DB::table('zanalytic')
			->orderby('date','desc')
			->first();

		$date=$zstock->date;		

		$arows=DB::table('zanalytic')
			->where('date','=',$date)
			->where('vrate','<=',40)
			->where('vrate1','>',0)
			->where('price1change','>',0)
			->orderby('vrate','desc')
			->orderby('vrate1','desc')
			->take(30)
			->get();

		$html="<table>";
		
		foreach($arows as $k=>$arow){
			$vrate=$arow->vrate;
			$zstock=DB::table('zstock')->find($arow->zstock_id);
			$ztrade=DB::table('ztrade')->where('zstock_id',$arow->zstock_id)->where('date',$date)->get();
			$html.="<tr>";
			$html.="<td>";

			$html.="<table>";
			$html.="<tr>";
			$html.="<td>ID</td>";
			$html.="<td>".$zstock->id."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Code</td>";
			$html.="<td>";
			$html.="<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			$html.=$zstock->code;
			$html.="</a></td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Name</td>";
			$html.="<td>";
			$html.="<a href='/similar/?c={$zstock->code}' href=_blank>";
			$html.=$zstock->name;
			$html.="</a>";
			$html.="</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price</td>";
			$html.="<td>".$ztrade[0]->close."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price Change</td>";
			$html.="<td>".$arow->price1change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Vibration</td>";
			$html.="<td>".$arow->price0change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Volume Change</td>";
			$html.="<td>".$vrate."</td>";
			$html.="</tr>";
			$html.="</table>";
			
			$html.="</td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/daily/n/{$zstock->code}.gif'></td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/min/n/{$zstock->code}.gif'></td>";
			$html.="</tr>";
		}
		$html.="</table>";
//return count($arows);
		return $html;

	}

	public static function similar($code){
		$zstock=DB::table('zstock')->where('code','=',$code)->first();
		if(!isset($zstock->id))return 'code not found';
		$zstock_id=$zstock->id;

		$zstock=DB::table('zanalytic')
			->where('zstock_id',$zstock_id)
			->orderby('date','desc')
			->first();

		$date=$zstock->date;

		$zstock=DB::table('zanalytic')
			->where('zstock_id',$zstock_id)
			->where('date',$date)
			->get();

		if(count($zstock)==0)return 'analytic not found';

		$avalue=array();	
		$avalue['p1'][0]=$zstock[0]->price2change-2;
		$avalue['p1'][1]=$zstock[0]->price2change+2;
		$avalue['p2'][0]=$zstock[0]->price2change-4;
		$avalue['p2'][1]=$zstock[0]->price2change+4;
		$avalue['p3'][0]=$zstock[0]->price3change-6;
		$avalue['p3'][1]=$zstock[0]->price3change+6;
		$avalue['p5'][0]=$zstock[0]->price5change-10;
		$avalue['p5'][1]=$zstock[0]->price5change+10;
		$avalue['p10'][0]=$zstock[0]->price10change-20;
		$avalue['p10'][1]=$zstock[0]->price10change+20;
		$avalue['p20'][0]=$zstock[0]->price20change-40;
		$avalue['p20'][1]=$zstock[0]->price20change+40;

		$arows=DB::table('zanalytic')
			->where('date','=',$date)
			->where('price1change','>=',$avalue['p1'][0])
			->where('price1change','<=',$avalue['p1'][1])
			->where('price2change','>=',$avalue['p2'][0])
			->where('price2change','<=',$avalue['p2'][1])
			->where('price3change','>=',$avalue['p3'][0])
			->where('price3change','<=',$avalue['p3'][1])
			->where('price5change','>=',$avalue['p5'][0])
			->where('price5change','<=',$avalue['p5'][1])
			->where('price10change','>=',$avalue['p10'][0])
			->where('price10change','<=',$avalue['p10'][1])
			->where('price20change','>=',$avalue['p20'][0])
			->where('price20change','<=',$avalue['p20'][1])
			->where('vrate','<=',40)
			->where('vrate1','<=',40)
			->orderby('vrate','desc')
			->take(30)
			->get();

		$html="<table>";

		foreach($arows as $k=>$arow){
			$vrate=$arow->vrate;
			$zstock=DB::table('zstock')->find($arow->zstock_id);
			$ztrade=DB::table('ztrade')->where('zstock_id',$arow->zstock_id)->where('date',$date)->get();
			$html.="<tr>";
			$html.="<td>";

			$html.="<table>";
			$html.="<tr>";
			$html.="<td>ID</td>";
			$html.="<td>".$zstock->id."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Code</td>";
			$html.="<td>";
			$html.="<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			$html.=$zstock->code;
			$html.="</a></td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Name</td>";
			$html.="<td>";
			$html.="<a href='/similar/?c={$zstock->code}' href=_blank>";
			$html.=$zstock->name;
			$html.="</a>";
			$html.="</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price</td>";
			$html.="<td>".$ztrade[0]->close."</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Price Change</td>";
			$html.="<td>".$arow->price1change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Vibration</td>";
			$html.="<td>".$arow->price0change."%</td>";
			$html.="</tr>";
			$html.="<tr>";
			$html.="<td>Volume Change</td>";
			$html.="<td>".$vrate."</td>";
			$html.="</tr>";
			$html.="</table>";
			
			$html.="</td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/daily/n/{$zstock->code}.gif'></td>";
			$html.="<td><img src='http://image.sinajs.cn/newchart/min/n/{$zstock->code}.gif'></td>";
			$html.="</tr>";
		}
		$html.="</table>";
//return count($arows);
		return $html;

	}

	public static function updatechanges(){
		$time_start=time();
		set_time_limit(1500);
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$zstocks=DB::table('zstock')->where('status','>=',0)->get();
		foreach($zstocks as $j=>$zstock){
			$ztrades=DB::table('ztrade')
				->where('zstock_id','=',$zstock->id)
				->where('volume','>',0)
				->where('close','>',0)
				->orderby('date','desc')
				->get();
			if(count($ztrades)){
				$apricechanges=array();
				$n=0;
				foreach($ztrades as $k=>$ztrade){

					$apricechanges[$n]=[
						'date'=>$ztrade->date,
						'open'=>$ztrade->open,
						'close'=>$ztrade->close,
						'openchange'=>0,
						1=>0,
						2=>0,
						3=>0,
						5=>0,
						10=>0,
						20=>0,
						'v1'=>$ztrade->volume,
						'v3'=>$ztrade->volume,
						'v5'=>$ztrade->volume,
						'v10'=>$ztrade->volume,
						'v20'=>$ztrade->volume
					];
					
					$open=$ztrade->open;
					$close=$ztrade->close;

					$apricechanges[$n][0]=100*($close-$ztrade->open)/$close;
				
					if($n>=1){
						$apricechanges[$n-1]['openchange']=100*($apricechanges[$n-1]['open']-$close)/$close;
						$apricechanges[$n-1][1]=100*($apricechanges[$n-1]['close']-$close)/$close;
					}
					if($n>=2){
						$apricechanges[$n-2][2]=100*($apricechanges[$n-2]['close']-$close)/$close;
					}
					if($n>=3){
						$apricechanges[$n-3][3]=100*($apricechanges[$n-3]['close']-$close)/$close;
					}
					if($n>=5){
						$apricechanges[$n-5][5]=100*($apricechanges[$n-5]['close']-$close)/$close;
					}
					if($n>=10){
						$apricechanges[$n-10][10]=100*($apricechanges[$n-10]['close']-$close)/$close;
					}
					if($n>=20){
						$apricechanges[$n-20][20]=100*($apricechanges[$n-20]['close']-$close)/$close;
					}

					if(count($ztrades)>=20){
						for($r=1;$r<=3;$r++){
							if($n>=$r)$apricechanges[$n-$r]['v3']+=$ztrade->volume;
						}
						for($r=1;$r<=5;$r++){
							if($n>=$r)$apricechanges[$n-$r]['v5']+=$ztrade->volume;
						}
						for($r=1;$r<=10;$r++){
							if($n>=$r)$apricechanges[$n-$r]['v10']+=$ztrade->volume;
						}
						for($r=1;$r<=20;$r++){
							if($n>=$r)$apricechanges[$n-$r]['v20']+=$ztrade->volume;
						}
					}
					$n++;
				
					
					if($n>100)break;
				}

				foreach($apricechanges as $n=>$apricechange){
					if($n>0)break;
					
					$afield=[
						'zstock_id'=>$zstock->id,
						'date'=>$apricechange['date'],
						'openchange'=>$apricechange['openchange'],
						'price20change'=>$apricechange[20],
						'price10change'=>$apricechange[10],
						'price5change'=>$apricechange[5],
						'price3change'=>$apricechange[3],
						'price2change'=>$apricechange[2],
						'price1change'=>$apricechange[1],
						'price0change'=>$apricechange[0],
						'volume1'=>$apricechange['v1'],
						'volume3'=>$apricechange['v3'],
						'volume5'=>$apricechange['v5'],
						'volume10'=>$apricechange['v10'],
						'volume20'=>$apricechange['v20'],
						];

					$zanalytic=DB::table('zanalytic')->where('zstock_id','=',$zstock->id)->where('date','=',$apricechange['date']);
					if(count($zanalytic->get())){
						$zanalytic->update($afield);
					}else{
						DB::table('zanalytic')->insert($afield);
					}

				}				
			}
			if(!($j%100)){
				$time_lapse=time()-$time_start;
				file_put_contents('milestone.txt','updatechanges-inprogress: '.$j.' / '.$time_lapse.' / '.date('Y-m-d H:i:s'));
			}
		}
		
		DB::statement("update zanalytic set vrate1=round(100*((3*volume1/volume3)-1),2), vrate3=round(100*(((5/3)*volume3/volume5)-1),2), vrate5=round(100*((2*volume5/volume10)-1),2), vrate10=round(100*((2*volume10/volume20)-1),2) where volume10<>volume20");
		$time_lapse=time()-$time_start;
		file_put_contents('milestone.txt','updatechanges: '.$time_lapse.' / '.date('Y-m-d H:i:s'));
	}

	public static function updatelatest($n=0){
		date_default_timezone_set('Asia/Kuala_Lumpur');
		//if(date('H:i')<'09:30')return '';
		//if(date('H:i')>'15:15')return '';
		//if(date('w')==0 or date('w')==6)return '';
		$last5minutes=date('Y-m-d H:i:s',time()-300);
		$time_start=time();
		set_time_limit(600);
		if($n){
			$id1=$n;
			$id2=$n+500;
			$zstocks=DB::table('zstock')
				->where('id','>=',$id1)
				->where('id','<',$id2)
				->where('status','>=',0)
				->where('lread','<',$last5minutes)
				->get();
		}else{
			$zstocks=DB::table('zstock')->where('status','>=',0)->where('lread','<',$last5minutes)->get();
		}
		//$date=date('Y-m-d');
		//$date=date('2015-05-07');
		foreach($zstocks as $zstock){
			$content=self::geturlcontents("http://hq.sinajs.cn/list={$zstock->code}");
			$a=explode('"',$content);
			$a1=explode('"',$a[1]);
			$aresult=explode(',',$a1[0]);

			if(count($aresult)>30){
				if($aresult[3]>0){
					$zstock_id=$zstock->id;
					$date=$aresult[30];
					
					$afield=[
					'zstock_id'=>$zstock_id,
					'date'=>$date,
					'open'=>$aresult[1],
					'high'=>$aresult[4],
					'low'=>$aresult[5],
					'close'=>$aresult[3],
					'volume'=>$aresult[8]
					];

					$ztrade=DB::table('ztrade')->where('zstock_id','=',$zstock->id)->where('date','=',$date);
					if(count($ztrade->get())){
						$ztrade->update($afield);
					}else{
						DB::table('ztrade')->insert($afield);
					}
				}

				DB::table('zstock')->where('id',$zstock->id)->update(['lread'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table('zstock')->where('id',$zstock->id)->update(['status'=>-3]);
				echo $zstock->id;
				echo ' - ';
				echo $zstock->code;
				echo '<br>';
			}
		}

		if($n<=1){
			$content=self::geturlcontents("http://hq.sinajs.cn/list=sh000001");
			$a=explode('"',$content);
			$a1=explode('"',$a[1]);
			$aresult=explode(',',$a1[0]);

			if(count($aresult)>30){
				if($aresult[3]>0){
					$date=$aresult[30];
					
					$afield=[
					'date'=>$date,
					'open'=>$aresult[1],
					'high'=>$aresult[4],
					'low'=>$aresult[5],
					'close'=>$aresult[3],
					'volume'=>$aresult[8]
					];
					
					$zsh=DB::table('zsh')->where('date',$date);
					if(count($zsh->get())){
						$zsh->update($afield);
					}else{
						DB::table('zsh')->insert($afield);
					}
				}
			}
		}

		$time_lapse=time()-$time_start;
		file_put_contents('stockupdate.txt','Time Lapse:'.$time_lapse.' - '.date('Y-m-d H:i:s'));
		return $time_lapse;
	}

	public static function updateldate(){
		$zstocks=DB::table('zstock')->where('status','>=',0)->get();

		foreach($zstocks as $zstock){
			$ztrade=DB::table('ztrade')->where('zstock_id','=',$zstock->id)->orderBy('date', 'desc')->get();
			DB::table('zstock')->where('id',$zstock->id)->update(['ldate'=>$ztrade[0]->date]);
		}

	}

	public static function importhistory(){
		set_time_limit(0);
		$last_month=date('Y-m-d',time()-3600*24*90);
		$zstocks=DB::table('zstock')->where('fdate','=','0000-00-00')->where('status','>=',0)->get();
		foreach($zstocks as $zstock){
			$code=substr($zstock->code,2);
			$postfix='sz';
			if(substr($zstock->code,0,2)=='sh')$postfix='ss';						
			$content=Gps::geturlcontents("http://table.finance.yahoo.com/table.csv?s={$code}.{$postfix}");
			if(strpos($content,'the page you requested was not found')=== false){
				$acontents=explode(chr(10),$content);
				foreach($acontents as $k=>$v){
					$acontent=explode(',',$v);
					if($k){
						if($acontent[0]<$last_month){
							if($k==1){
								DB::table('zstock')->where('id',$zstock->id)->update(['status'=>-2]);
							}
							break;
						}else{
							//if($acontent[0]<$zstock->ldate)break;
						}

						DB::table('ztrade')->insert([
							'zstock_id' => $zstock->id,
							'date' => $acontent[0],
							'open' => $acontent[1],
							'high' => $acontent[2],
							'low' => $acontent[3],
							'close' => $acontent[4],
							'volume' => $acontent[5]
							]
						);

						if($zstock->fdate=='0000-00-00')$fdate=$acontent[0];
						else $fdate=min($zstock->fdate,$acontent[0]);

						if($zstock->ldate=='0000-00-00')$ldate=$acontent[0];
						else $ldate=max($zstock->ldate,$acontent[0]);

						$zstock->fdate=$fdate;
						$zstock->ldate=$ldate;

						DB::table('zstock')
							->where('id',$zstock->id)
							->update(['fdate'=>$fdate,'ldate'=>$ldate]);						
					}
				}
				//break;
			}else{
				DB::table('zstock')->where('id',$zstock->id)->update(['status'=>-1]);
				$acontent=array();
			}			
		}
		return '<pre>'.$code.'</pre>';
	}

	public static function importlist(){
		$content=file_get_contents('zstock.html');
		$acontent=explode('http://quote.eastmoney.com/',$content);
		$aresult=array();
		$ainsert=array();
		foreach($acontent as $k=>$v){
			if($k and substr($v,0,1)=='s'){
				$a=explode('"',$v);
				$a0=explode('.',$a[0]);
				$code=$a0[0];
				$a1=explode('(',$a[1]);
				$name=substr($a1[0],1);
				$aresult[]=[$code,$name];
				
				$found=DB::table('zstock')->where('code','=',$code)->get();
				if(count($found)==0){
					DB::table('zstock')->insert(
						['code' => $code, 'name' => $name]
					);
					$ainsert[]=[$code,$name];
				}
			}
		}
		return '<pre>'.var_export($ainsert,1).'</pre>';
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