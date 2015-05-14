<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

use App\Gps;

class Zanalytic extends Model {

	protected $fillable = ['*'];

	public static function analytic(){
		$amax=array();
		$amin=array();
		$fdate='2015-04-01';
		//$date='2015-04-29';
		$date=date('Y-m-d');
		$p=2;
		
		$amax['p1']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price1change');
		$amin['p1']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price1change');	
		$amax['p2']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price2change');
		$amin['p2']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price2change');
		$amax['p3']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price3change');
		$amin['p3']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price3change');
		$amax['p5']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price5change');
		$amin['p5']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price5change');
		$amax['p10']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price10change');
		$amin['p10']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price10change');
		$amax['p20']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->max('price20change');
		$amin['p20']=DB::table('zanalytic')
			->where('price1change','>=',$p)
			->where('date','<=',$date)
			->where('date','>=',$fdate)
			->min('price20change');

		$acounts=array();
		$arows=DB::table('zanalytic')->where('price1change','>=',$p)->where('date','<=',$date)->get();
		foreach($arows as $arow){
			$p1=intval(100*($arow->price1change-$amin['p1'])/($amax['p1']-$amin['p1']));
			if(!isset($acounts['p1'][$p1]))$acounts['p1'][$p1]=0;
			$acounts['p1'][$p1]++;

			$p2=intval(100*($arow->price2change-$amin['p2'])/($amax['p2']-$amin['p2']));
			if(!isset($acounts['p2'][$p2]))$acounts['p2'][$p2]=0;
			$acounts['p2'][$p2]++;

			$p3=intval(100*($arow->price3change-$amin['p3'])/($amax['p3']-$amin['p3']));
			if(!isset($acounts['p3'][$p3]))$acounts['p3'][$p3]=0;
			$acounts['p3'][$p3]++;

			$p5=intval(100*($arow->price5change-$amin['p5'])/($amax['p5']-$amin['p5']));
			if(!isset($acounts['p5'][$p5]))$acounts['p5'][$p5]=0;
			$acounts['p5'][$p5]++;

			$p10=intval(100*($arow->price10change-$amin['p10'])/($amax['p10']-$amin['p10']));
			if(!isset($acounts['p10'][$p10]))$acounts['p10'][$p10]=0;
			$acounts['p10'][$p10]++;

			$p20=intval(100*($arow->price20change-$amin['p20'])/($amax['p20']-$amin['p20']));
			if(!isset($acounts['p20'][$p20]))$acounts['p20'][$p20]=0;
			$acounts['p20'][$p20]++;
		}

		arsort($acounts['p1']);
		arsort($acounts['p2']);
		arsort($acounts['p3']);
		arsort($acounts['p5']);
		arsort($acounts['p10']);
		arsort($acounts['p20']);

		$azstocks=array();
		foreach($arows as $arow){
			$p1=intval(100*($arow->price1change-$amin['p1'])/($amax['p1']-$amin['p1']));
			$n=0;
			foreach($acounts['p1'] as $k=>$v){
				if($n<6){
					if($p1==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
			$p2=intval(100*($arow->price2change-$amin['p2'])/($amax['p2']-$amin['p2']));
			$n=0;
			foreach($acounts['p2'] as $k=>$v){
				if($n<5){
					if($p2==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
			$p3=intval(100*($arow->price3change-$amin['p3'])/($amax['p3']-$amin['p3']));
			$n=0;
			foreach($acounts['p3'] as $k=>$v){
				if($n<4){
					if($p3==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
			$p5=intval(100*($arow->price5change-$amin['p5'])/($amax['p5']-$amin['p5']));
			$n=0;
			foreach($acounts['p5'] as $k=>$v){
				if($n<3){
					if($p5==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
			$p10=intval(100*($arow->price10change-$amin['p10'])/($amax['p10']-$amin['p10']));
			$n=0;
			foreach($acounts['p10'] as $k=>$v){
				if($n<2){
					if($p10==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
			$p20=intval(100*($arow->price20change-$amin['p20'])/($amax['p20']-$amin['p20']));
			$n=0;
			foreach($acounts['p20'] as $k=>$v){
				if($n<1){
					if($p20==$k){
						if(isset($azstocks[$arow->zstock_id]))$azstocks[$arow->zstock_id]++;
						else $azstocks[$arow->zstock_id]=0;
					}
				}else break;
				$n++;
			}
		
		}

		$zanalytics=DB::table('zanalytic')->where('date',$date)->get();
		foreach($zanalytics as $zanalytic){
			if($zanalytic->vrate10<40 and $zanalytic->vrate5<40 and isset($azstocks[$zanalytic->zstock_id])){
				$azstocks[$zanalytic->zstock_id]+=round(5*$zanalytic->vrate5/10)+round(3*$zanalytic->vrate10/10);
			}
		}

		arsort($azstocks);

		$html="<table>";

		$k=0;
		foreach($azstocks as $zstock_id=>$ncount){
			if($k>=30)break;
			
			$zanalytic=DB::table('zanalytic')->where('date',$date)->where('zstock_id',$zstock_id)->get();
			if(!isset($zanalytic[0]))continue;
			$k++;
			$arow=$zanalytic[0];
			$vrate=$arow->vrate5;
			$zstock=DB::table('zstock')->find($arow->zstock_id);
			$ztrade=DB::table('ztrade')->where('zstock_id',$arow->zstock_id)->where('date',$date)->get();
			$html.="<tr>";
			$html.="<td>";

			$html.="<table>";
			$html.="<tr>";
			$html.="<td>ID</td>";
			$html.="<td>".$k.'-'.$zstock->id.' ('.$ncount.")</td>";
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
/*
		return 'avalue<pre>'.var_export($avalue,1).'<br>ahigh'.var_export($ahigh,1).'<br>amax'.var_export($amax,1).'<br>amin'.var_export($amin,1).'<br>acounts'.var_export($acounts,1).'</pre><br>';
*/
	}
}
	