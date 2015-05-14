<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

use App\Zstock;

class Zupdate extends Model {

	public static function lastdata(){
		$time_start=time();
		set_time_limit(3000);
		$astocks=DB::table('zstock')->where('status','>=',0)->get();
		$gdate=date('Y-m-d',time()-(3600*24*10));


		$n=0;
		foreach($astocks as $j=>$astock){
			$arows=DB::table('ztrade')
				->where('zstock_id',$astock->id)
				->where('date','>',$gdate)
				->orderby('date')->get();
			foreach($arows as $k=>$arow){
				if($k>2){
					$aztrade=[
						'g_date'=>$garow->date,
						'g_volume'=>$garow->volume,
						'g_open'=>$garow->open,
						'g_high'=>$garow->high,
						'g_low'=>$garow->low,
						'g_close'=>$garow->close,
						];
					DB::table('ztrade')->where('id',$arow->id)->update($aztrade);
					$n++;
				}
				if(isset($harow))$garow=$harow;
				if(isset($larow))$harow=$larow;
				$larow=$arow;
				
			}
		}
		$time_lapse=time()-$time_start;
		file_put_contents('updatepattern','G: '.$time_lapse.' / '.date('Y-m-d H:i:s'));

		$n=0;
		foreach($astocks as $j=>$astock){
			$arows=DB::table('ztrade')
				->where('zstock_id',$astock->id)
				->where('date','>',$gdate)
				->orderby('date')->get();
			foreach($arows as $k=>$arow){
				if($k>1){
					$aztrade=[
						'h_date'=>$harow->date,
						'h_volume'=>$harow->volume,
						'h_open'=>$harow->open,
						'h_high'=>$harow->high,
						'h_low'=>$harow->low,
						'h_close'=>$harow->close,
						];
					DB::table('ztrade')->where('id',$arow->id)->update($aztrade);
					$n++;
				}
				if(isset($larow))$harow=$larow;
				$larow=$arow;
				
			}
		}
		$time_lapse=time()-$time_start;
		file_put_contents('updatepattern','H: '.$time_lapse.' / '.date('Y-m-d H:i:s'));


		$n=0;
		foreach($astocks as $j=>$astock){
			$arows=DB::table('ztrade')
				->where('zstock_id',$astock->id)
				->where('date','>',$gdate)
				->orderby('date')->get();
			foreach($arows as $k=>$arow){
				if($k>0){
					$aztrade=[
						'l_date'=>$larow->date,
						'l_volume'=>$larow->volume,
						'l_open'=>$larow->open,
						'l_high'=>$larow->high,
						'l_low'=>$larow->low,
						'l_close'=>$larow->close,
						];
					DB::table('ztrade')->where('id',$arow->id)->update($aztrade);
					$n++;
				}
				$larow=$arow;
			}
		}
		$time_lapse=time()-$time_start;
		file_put_contents('updatepattern','L: '.$time_lapse.' / '.date('Y-m-d H:i:s'));

		$n=0;
		foreach($astocks as $j=>$astock){
			$arows=DB::table('ztrade')
				->where('zstock_id',$astock->id)
				->where('date','>',$gdate)
				->orderby('date','desc')->get();
			foreach($arows as $k=>$arow){
				if($k>0){
					$aztrade=[
						'n_date'=>$larow->date,
						'n_volume'=>$larow->volume,
						'n_open'=>$larow->open,
						'n_high'=>$larow->high,
						'n_low'=>$larow->low,
						'n_close'=>$larow->close,
						];

					DB::table('ztrade')->where('id',$arow->id)->update($aztrade);
					$n++;
				}
				$larow=$arow;
			}
		}
		$time_lapse=time()-$time_start;
		file_put_contents('updatepattern','N: '.$time_lapse.' / '.date('Y-m-d H:i:s'));
		
		self::updatecsv();

		//$gdate=date('Y-m-d',time()-(3600*24*180));
		$n=0;
		$azsh=array();
		foreach($astocks as $j=>$astock){
			$arows=DB::table('ztrade')
				->where('zstock_id',$astock->id)
				->where('date','>',$gdate)
				->orderby('date')->get();
			foreach($arows as $k=>$ztrade){

				if($ztrade->h_volume>$ztrade->g_volume)$m1='U';
				elseif($ztrade->h_volume<$ztrade->g_volume) $m1='D';
				else $m1='N';

				if($ztrade->l_volume>$ztrade->h_volume)$m2='U';
				elseif($ztrade->l_volume<$ztrade->h_volume) $m2='D';
				else $m2='N';

				if($ztrade->volume>$ztrade->l_volume)$m3='U';
				elseif($ztrade->volume<$ztrade->l_volume) $m3='D';
				else $m3='N';

				if($ztrade->h_close>$ztrade->g_close)$m4='U';
				elseif($ztrade->h_close<$ztrade->g_close) $m4='D';
				else $m4='N';

				if($ztrade->l_close>$ztrade->h_close)$m5='U';
				elseif($ztrade->l_close<$ztrade->h_close) $m5='D';
				else $m5='N';

				if($ztrade->close>$ztrade->l_close)$m6='U';
				elseif($ztrade->close<$ztrade->l_close) $m6='D';
				else $m6='N';

				$r=0;
				if($ztrade->l_close>0){
					$nr=100*($ztrade->close-$ztrade->l_close)/$ztrade->l_close;
					$nr=abs(round($nr));
					if($nr>=10)$r='X';
					else $r=$nr;
				}

				$s=0;
				if($ztrade->l_volume>0){
					$ns=10*($ztrade->volume-$ztrade->l_volume)/$ztrade->l_volume;
					$ns=abs(round($ns));
					if($ns>=10)$s='X';
					else $s=$ns;
				}
				
				if(isset($azsh[$ztrade->date])){
					$m0=$azsh[$ztrade->date]['pattern'];
				}else{
					$zsh=DB::table('zsh')->where('date',$ztrade->date)->get();
					if(isset($zsh[0])){
						$m0=$zsh[0]->pattern;
						$azsh[$ztrade->date]['pattern']=$zsh[0]->pattern;
						$azsh[$ztrade->date]['pattern1']=$zsh[0]->pattern1;
						$azsh[$ztrade->date]['pattern2']=$zsh[0]->pattern2;
					}else continue;
				}

				$m0=$azsh[$ztrade->date]['pattern1'];
				$mp=$m0.$m3.$s.$m6.$r;

				$m0=$azsh[$ztrade->date]['pattern2'];
				$mp1=$m0.$m3.$s.$m6.$r;

				$m0=$azsh[$ztrade->date]['pattern1'];				
				$mp2=$m0.$m2.$m3.$m5.$m6;

				$mp3=$m0.$m1.$m2.$m3.$m4.$m5.$m6;

				//$mp2=$m0.$m1.$m2.$m3.$s.$m4.$m5.$m6.$r;
				//$m=$m0.$m3.$s.$m5.$m6;
				

				$nrate=0;
				if($ztrade->close>0){
					$nrate=100*($ztrade->n_high-$ztrade->close)/$ztrade->close;
				}
					
				DB::table('ztrade')
					->where('id',$ztrade->id)
					->update([
					'pattern'=>$mp,
					'pattern1'=>$mp1,
					'pattern2'=>$mp2,
					'pattern3'=>$mp3,
					'pricechange'=>$nrate
					]);
			}
		}
		$time_lapse=time()-$time_start;
		file_put_contents('updatepattern','P: '.$time_lapse.' / '.date('Y-m-d H:i:s'));


		return $n;
	}

	public static function updatecsv(){		

		$gdate=date('Y-m-d',time()-(3600*24*10));				

		$n=0;
		
		$arows=DB::table('zsh')
			->where('date','>',$gdate)
			->orderby('date')->get();
		foreach($arows as $k=>$arow){
			if($k>0){
				$aztrade=[
					'l_date'=>$larow->date,
					'l_volume'=>$larow->volume,
					'l_open'=>$larow->open,
					'l_high'=>$larow->high,
					'l_low'=>$larow->low,
					'l_close'=>$larow->close,
					];
				DB::table('zsh')->where('id',$arow->id)->update($aztrade);
				$n++;
			}
			if($k>1){
				$aztrade=[
					'h_date'=>$harow->date,
					'h_volume'=>$harow->volume,
					'h_open'=>$harow->open,
					'h_high'=>$harow->high,
					'h_low'=>$harow->low,
					'h_close'=>$harow->close,
					];
				DB::table('zsh')->where('id',$arow->id)->update($aztrade);
				$n++;
			}
			if($k>2){
				$aztrade=[
					'g_date'=>$garow->date,
					'g_volume'=>$garow->volume,
					'g_open'=>$garow->open,
					'g_high'=>$garow->high,
					'g_low'=>$garow->low,
					'g_close'=>$garow->close,
					];
				DB::table('zsh')->where('id',$arow->id)->update($aztrade);
				$n++;
			}
			if(isset($harow))$garow=$harow;
			if(isset($larow))$harow=$larow;
			$larow=$arow;
			
		}

		$arows=DB::table('zsh')
			->where('date','>',$gdate)
			->orderby('date','desc')->get();
		foreach($arows as $k=>$arow){
			if($k>0){

				$nrate=0;
				if($arow->close>0){
					$nrate=100*($larow->close-$arow->close)/$arow->close;
				}

				$aztrade=[
					'n_date'=>$larow->date,
					'n_volume'=>$larow->volume,
					'n_open'=>$larow->open,
					'n_high'=>$larow->high,
					'n_low'=>$larow->low,
					'n_close'=>$larow->close,
					'pricechange'=>$nrate,
					];				

				DB::table('zsh')->where('id',$arow->id)->update($aztrade);
				$n++;
			}
			$larow=$arow;
		}
		
		//$gdate=date('Y-m-d',time()-(3600*24*18000));				
		$arows=DB::table('zsh')
			->where('date','>',$gdate)
			->orderby('date')->get();
		foreach($arows as $k=>$ztrade){

			if($ztrade->h_volume>$ztrade->g_volume)$m1='U';
			elseif($ztrade->h_volume<$ztrade->g_volume) $m1='D';
			else $m1='N';

			if($ztrade->l_volume>$ztrade->h_volume)$m2='U';
			elseif($ztrade->l_volume<$ztrade->h_volume) $m2='D';
			else $m2='N';

			if($ztrade->volume>$ztrade->l_volume)$m3='U';
			elseif($ztrade->volume<$ztrade->l_volume) $m3='D';
			else $m3='N';

			if($ztrade->h_close>$ztrade->g_close)$m4='U';
			elseif($ztrade->h_close<$ztrade->g_close) $m4='D';
			else $m4='N';

			if($ztrade->close>$ztrade->l_close)$m5='U';
			elseif($ztrade->close<$ztrade->l_close) $m5='D';
			else $m5='N';

			if($ztrade->l_close>$ztrade->h_close)$m6='U';
			elseif($ztrade->l_close<$ztrade->h_close) $m6='D';
			else $m6='N';

			$r=0;
			$r1='L';
			if($ztrade->l_close>0){
				$nr=100*($ztrade->close-$ztrade->l_close)/$ztrade->l_close;
				$nr=abs(round($nr));
				if($nr>=10)$r='X';
				else $r=$nr;
				if($nr>=1)$r1='H';
			}

			$s=0;
			$s1='L';
			if($ztrade->l_volume>0){
				$ns=10*($ztrade->volume-$ztrade->l_volume)/$ztrade->l_volume;
				$ns=abs(round($ns));
				if($ns>=10)$s='X';
				else $s=$ns;
				if($ns>=3)$s1='H';
			}
			
			//$m=$m1.$m2.$m3.$m4.$m5.$m6;
			//$m=$m2.$m3.$m6.$m5;
			//$m=$m3.$m4;
			$mp=$m3.$s.$m5.$r;
			$mp1=$m2.$m3.$m6.$m5;		
			$mp2=$m3.$s1.$m5.$r1;		
				
			DB::table('zsh')->where('id',$ztrade->id)->update(['pattern'=>$mp,'pattern1'=>$mp1,'pattern2'=>$mp2]);

		}
	}

	public static function importcsv(){
			$content=file_get_contents('000001.csv');
			$acontents=explode(chr(10),$content);
			foreach($acontents as $k=>$v){
				$acontent=explode(',',$v);
				if($k){
					$afield=[
						'date'=>$acontent[0],
						'open' => $acontent[6],
						'high' => $acontent[4],
						'low' => $acontent[5],
						'close' => $acontent[3],
						'volume' => $acontent[10]
					];
					DB::table('zsh')->insert($afield);
				}
			}
	}

	public static function importsh(){
		$content=Zstock::geturlcontents("http://table.finance.yahoo.com/table.csv?s=000001.ss");
		if(strpos($content,'the page you requested was not found')=== false){
				$acontents=explode(chr(10),$content);
				foreach($acontents as $k=>$v){
					$acontent=explode(',',$v);
					if($k){
						DB::table('zsh')->insert([
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
	}
}