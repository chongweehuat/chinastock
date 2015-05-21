<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

use App\Gps;

class Ztest extends Model {

	public static function removetrade(){
		$astocks=DB::select("select id from zstock where status<0");
		foreach($astocks as $astock){
			DB::delete("delete from ztrade where zstock_id={$astock->id}");
		}
		return 'OK';
	}

	public static function updatepattern(){
		set_time_limit(900);
		$arows=DB::table('ztrade')->groupby('date')->get();
		$date='';
		$date1='';
		$html='';
		foreach($arows as $arow){
			if(empty($date))$date=$arow->date;
			elseif(!empty($date) and empty($date1))$date1=$arow->date;

			if(!empty($date) and !empty($date1)){
				if($date>='2015-02-25')self::checkpattern($date,$date1);
				$date=$date1;
				$date1='';
			}
		}
	}

	public static function checkpattern($date='',$date1=''){
		if(empty($date))$date=date('Y-m-d');
		if(empty($date1))$date1=date('Y-m-d',time()+(3600*24));
		$arows=DB::table('zanalytic')
				->where('date','=',$date)				
				->get();
		
		$acounts=array();
		$apatterns=array();

		$n=0;

		foreach($arows as $k=>$arow){
			$analytics=DB::table('zanalytic')
				->where('date','=',$date1)
				->where('zstock_id','=',$arow->zstock_id)
				->get();

			if($arow->vrate1==0 and $arow->vrate3==0 and $arow->vrate5==0)continue;			
			
			$n++;

			if($arow->vrate1>0)$m1='U';
			elseif($arow->vrate1<0) $m1='D';
			else $m1='N';

			if($arow->vrate3>0)$m2='U';
			elseif($arow->vrate3<0) $m2='D';
			else $m2='N';

			if($arow->vrate5>0)$m3='U';
			elseif($arow->vrate5<0) $m3='D';
			else $m3='N';

			if($arow->price0change>0)$m4='U';
			elseif($arow->price0change<0)$m4='D';
			else $m4='N';

			if($arow->price1change>0)$m5='U';
			elseif($arow->price1change<0)$m5='D';
			else $m5='N';

			if($arow->price1change>9.8)$m6='X';
			elseif($arow->price1change>9)$m6=9;
			elseif($arow->price1change<1)$m6=0;
			else $m6=floor($arow->price1change);
			
			$m=$m1.$m2.$m3.$m4.$m5.$m6;
			
			if(!isset($apatterns['count_all'][strlen($m)]))$apatterns['count_all'][strlen($m)]=0;
			$apatterns['count_all'][strlen($m)]++;
			if(!isset($acounts[$m]['count_all']))$acounts[$m]['count_all']=0;
			$acounts[$m]['count_all']++;

			if(!isset($acounts[$m]['count_up']))$acounts[$m]['count_up']=0;
			if(!isset($apatterns['count_up'][strlen($m)]))$apatterns['count_up'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->openchange>0){				
				$acounts[$m]['count_up']++;
				$apatterns['count_up'][strlen($m)]++;
			}

			if(!isset($acounts[$m]['count_down']))$acounts[$m]['count_down']=0;
			if(!isset($apatterns['count_down'][strlen($m)]))$apatterns['count_down'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->openchange<=0){				
				$acounts[$m]['count_down']++;
				$apatterns['count_down'][strlen($m)]++;
			}

			for($i=1;$i<=8;$i++){
				if(!isset($acounts[$m]['count'.$i]))$acounts[$m]['count'.$i]=0;
				if(!isset($apatterns['count'.$i][strlen($m)]))$apatterns['count'.$i][strlen($m)]=0;
				if(isset($analytics[0]) and $analytics[0]->openchange>=$i){					
					$acounts[$m]['count'.$i]++;
					$apatterns['count'.$i][strlen($m)]++;
				}

			}
			

			if(!isset($acounts[$m]['count_up0']))$acounts[$m]['count_up0']=0;
			if(!isset($apatterns['count_up0'][strlen($m)]))$apatterns['count_up0'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->price0change>0){				
				$acounts[$m]['count_up0']++;
				$apatterns['count_up0'][strlen($m)]++;
			}

			if(!isset($acounts[$m]['count_down0']))$acounts[$m]['count_down0']=0;
			if(!isset($apatterns['count_down0'][strlen($m)]))$apatterns['count_down0'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->price0change<=0){				
				$acounts[$m]['count_down0']++;
				$apatterns['count_down0'][strlen($m)]++;
			}

			for($i=1;$i<=8;$i++){
				if(!isset($acounts[$m]['count0'.$i]))$acounts[$m]['count0'.$i]=0;
				if(!isset($apatterns['count0'.$i][strlen($m)]))$apatterns['count0'.$i][strlen($m)]=0;
				if(isset($analytics[0]) and $analytics[0]->price0change>=$i){					
					$acounts[$m]['count0'.$i]++;
					$apatterns['count0'.$i][strlen($m)]++;
				}

			}

			if(!isset($acounts[$m]['count_up1']))$acounts[$m]['count_up1']=0;
			if(!isset($apatterns['count_up1'][strlen($m)]))$apatterns['count_up1'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->price1change>0){				
				$acounts[$m]['count_up1']++;
				$apatterns['count_up1'][strlen($m)]++;
			}

			if(!isset($acounts[$m]['count_down1']))$acounts[$m]['count_down1']=0;
			if(!isset($apatterns['count_down1'][strlen($m)]))$apatterns['count_down1'][strlen($m)]=0;
			if(isset($analytics[0]) and $analytics[0]->price1change<=0){				
				$acounts[$m]['count_down1']++;
				$apatterns['count_down1'][strlen($m)]++;
			}

			for($i=1;$i<=8;$i++){
				if(!isset($acounts[$m]['count1'.$i]))$acounts[$m]['count1'.$i]=0;
				if(!isset($apatterns['count1'.$i][strlen($m)]))$apatterns['count1'.$i][strlen($m)]=0;
				if(isset($analytics[0]) and $analytics[0]->price1change>=$i){					
					$acounts[$m]['count1'.$i]++;
					$apatterns['count1'.$i][strlen($m)]++;
				}

			}

		}
		foreach($acounts as $pattern=>$afield){
			$zpattern=DB::table('zpattern')->where('date','=',$date)->where('pattern','=',$pattern);			
			foreach($afield as $fn=>$v){
				if($apatterns[$fn][strlen($pattern)]){
					$afield[$fn.'_rt']=intval(100*$v/$apatterns[$fn][strlen($pattern)]);
				}
			}

			$afield['date']=$date;
			$afield['pattern']=$pattern;
			if(count($zpattern->get())){
				$zpattern->update($afield);
			}else{
				DB::table('zpattern')->insert($afield);
			}
		}

	}

	public static function datelist(){
		set_time_limit(300);
		$arows=DB::table('ztrade')->groupby('date')->get();
		$date='';
		$date1='';
		$html='';
		foreach($arows as $arow){
			if(empty($date))$date=$arow->date;
			elseif(!empty($date) and empty($date1))$date1=$arow->date;

			if(!empty($date) and !empty($date1)){
				$html.=$date;
				$html.=' / ';
				$html.=$date1;
				$html.=' - ';
				$html.=self::toppattern($date,$date1);

				$html.='<br>';
				$date=$date1;
				$date1='';
			}
		}
		file_put_contents('pattern_all.html',$html);
		return $html;
	}

	public static function toppattern($date,$date1){
		$arows=DB::table('zanalytic')
				->where('date','=',$date1)
				//->where('openchange','>=',3)
				//->orWhere('price1change','>=',3)
				//->where('price1change','>=',3)
				->get();
		
		$acounts=array();	

		$n=0;

		foreach($arows as $k=>$arow){
			$analytics=DB::table('zanalytic')
				->where('date','=',$date)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			if(!isset($analytics[0]))continue;

			if($analytics[0]->vrate1==0 and $analytics[0]->vrate3==0 and $analytics[0]->vrate5==0)continue;

			$zstock=DB::table('zstock')->find($arow->zstock_id);

			$atrades=DB::table('ztrade')
				->where('date','=',$date)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			$atrades1=DB::table('ztrade')
				->where('date','=',$date1)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			
			$n++;

			if($analytics[0]->vrate1>0)$m1='U';
			else $m1='D';
			if($analytics[0]->vrate3>0)$m2='U';
			else $m2='D';
			if($analytics[0]->vrate5>0)$m3='U';
			else $m3='D';

			if($analytics[0]->price0change>0)$m4='U';
			else $m4='D';
			if($analytics[0]->price1change>0)$m5='U';
			else $m5='D';

			$m=$m1.$m2.$m3.$m4.$m5;
			
			if(!isset($acounts['vrate'][$m]))$acounts['vrate'][$m]=0;
			$acounts['vrate'][$m]++;
			
		}
		if(isset($acounts['vrate'])){
			arsort($acounts['vrate']);

			foreach($acounts['vrate'] as $k=>$v){
				if($k<>'DUUUU')return $k.' => '.$v.'*';
				else return $k.' => '.$v;
			}
		}else return '';
	}

	public static function vibrate(){
		$date='2015-04-08';
		//$date='2015-05-04';

		$date1='2015-04-09';
		//$date1='2015-05-05';

		$arows=DB::table('zanalytic')
				->where('date','=',$date1)
				->where('openchange','>=',3)
				->get();
		
		$acounts=array();	

		$n=0;
		echo '<center>';
		echo $date.' / '.$date1;
		echo '<table width=100%>';
		echo "<tr>";
		echo "<td>No.</td>";
		echo "<td width=150>Stock</td>";
		echo "<td align=right>vrate1</td>";
		echo "<td align=right>vrate3</td>";
		echo "<td align=right>vrate5</td>";
		echo "<td align=right>vrate10</td>";
		echo "<td align=right>open</td>";
		echo "<td align=right>close</td>";
		echo "<td align=right>nextday open</td>";
		echo "<td align=right>nextday close</td>";
		echo "<td align=right>open change</td>";
		echo "<td align=right>price 0 change</td>";
		echo "<td align=right>price 1 change</td>";
		echo "<td align=right>price 2 change</td>";
		echo "<td align=right>price 5 change</td>";
		echo "<td align=right>price 10 change</td>";
		echo "</tr>";

		foreach($arows as $k=>$arow){
			$analytics=DB::table('zanalytic')
				->where('date','=',$date)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			if(!isset($analytics[0]))continue;

			if($analytics[0]->vrate1==0 and $analytics[0]->vrate3==0 and $analytics[0]->vrate5==0)continue;

			$zstock=DB::table('zstock')->find($arow->zstock_id);

			$atrades=DB::table('ztrade')
				->where('date','=',$date)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			$atrades1=DB::table('ztrade')
				->where('date','=',$date1)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			
			$n++;

/*
			echo '<tr>';
			echo '<td>';
			echo $n;
			echo '</td>';
			echo '<td>';
			echo $zstock->code.' '.$zstock->name;
			echo '</td>';
			echo '<td align=right>';
			echo $analytics[0]->vrate1;
			echo '</td>';
			echo '<td align=right>';
			echo $analytics[0]->vrate3;
			echo '</td>';
			echo '<td align=right>';
			echo $analytics[0]->vrate5;
			echo '</td>';
			echo '<td align=right>';
			echo $analytics[0]->vrate10;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades[0]->open;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades[0]->close;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades1[0]->open;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades1[0]->close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->openchange;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->price0change;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->price1change;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->price2change;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->price5change;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->price10change;
			echo '</td>';
			echo '</tr>';
*/
			if($analytics[0]->vrate1>0)$m1='U';
			else $m1='D';
			if($analytics[0]->vrate3>0)$m2='U';
			else $m2='D';
			if($analytics[0]->vrate5>0)$m3='U';
			else $m3='D';

			if($analytics[0]->price0change>0)$m4='U';
			else $m4='D';
			if($analytics[0]->price1change>0)$m5='U';
			else $m5='D';

			$m=$m1.$m2.$m3.$m4.$m5;
			
			if(!isset($acounts['vrate'][$m]))$acounts['vrate'][$m]=0;
			$acounts['vrate'][$m]++;
			
		}
		echo '</table></center>';

arsort($acounts['vrate']);
echo '<pre>';
echo var_export($acounts,1);
echo '</pre>';

		return count($arows);
	}
}
