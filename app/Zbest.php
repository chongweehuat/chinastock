<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

class Zbest extends Model {

	public static function datelist(){
		$arows=DB::table('ztrade')->groupby('date')->get();
		$date='';
		$date1='';
		foreach($arows as $arow){
			if(empty($date))$date=$arow->date;
			elseif(!empty($date) and empty($date1))$date1=$arow->date;

			if(!empty($date) and !empty($date1)){
				self::account($date,$date1);

				$date=$date1;
				$date1='';
			}
		}
	
		return '';
	}

	public static function account($date='',$date1=''){
		//$date='2015-05-07';
		//$date1='2015-05-08';
		if(empty($date)){			
			$date=date('Y-m-d');
			$date1=date('Y-m-d',time()+(3600*24));
		}
//DUUUU		
		$arows=DB::table('zanalytic')
				->where('date','=',$date)
				->where('vrate1','<',0)
				->where('vrate3','>',0)
				->where('vrate5','>',0)
				//->where('vrate10','>',0)
				->where('price0change','>',0)
				->where('price1change','>',6)
				//->where('price2change','>',0)
				->orderby('vrate1','asc')
				->take(10)
				->get();
//
/*UUUUU
		$arows=DB::table('zanalytic')
				->where('date','=',$date)
				->where('vrate1','>',0)
				->where('vrate3','>',0)
				->where('vrate5','>',0)
				//->where('vrate10','>',0)
				->where('price0change','>',0)
				->where('price1change','>',5)
				//->where('price2change','>',0)
				->orderby('vrate1','asc')
				->take(10)
				->get();
*/
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
			$zstock=DB::table('zstock')->find($arow->zstock_id);

			$atrades=DB::table('ztrade')
				->where('date','=',$date)
				->where('zstock_id','=',$arow->zstock_id)
				->get();

			$atrades1=DB::table('ztrade')
				->where('date','=',$date1)
				->where('zstock_id','=',$arow->zstock_id)
				->get();

			$analytics=DB::table('zanalytic')
				->where('date','=',$date1)
				->where('zstock_id','=',$arow->zstock_id)
				->get();
			
			$n++;

			echo '<tr>';
			echo '<td>';
			echo $n;
			echo '</td>';
			echo '<td>';
			echo "<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			echo $zstock->code;
			echo '</a>';
			echo ' '.$zstock->name;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->vrate1;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->vrate3;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->vrate5;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->vrate10;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades[0]->open;
			echo '</td>';
			echo '<td align=right>';
			echo $atrades[0]->close;
			echo '</td>';
			echo '<td align=right>';
			if(isset($atrades1[0]))echo $atrades1[0]->open;
			echo '</td>';
			echo '<td align=right>';
			if(isset($atrades1[0]))echo $atrades1[0]->close;
			echo '</td>';
			echo '<td align=right>';
			if(isset($analytics[0])){
				if($analytics[0]->openchange<3)echo'<span style="color:green;font-weight:600;">';
				echo $analytics[0]->openchange;
				if($analytics[0]->openchange<3)echo'</span>';
			}
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
			
		}
		echo '</table></center>';

		return '';
	}

	public static function bestpattern($date='',$pattern='',$p=''){
		if($date){
			$arows=DB::table('ztrade')
				->select('*', DB::raw('100*(close-l_close)/l_close as nrate'))
				->where('date',$date)
				->where('pattern'.$p,$pattern)
				->orderby('close','asc')
				->get();
		}else{
			$arows=DB::table('ztrade')
				->select('*', DB::raw('100*(close-l_close)/l_close as nrate'))
				->where('pattern'.$p,$pattern)
				->orderby('close','asc')
				->get();
		}

		$n=0;
		echo '<center>';
		echo $date.' / '.$pattern;
		echo '<table width=100%>';
		echo "<tr>";
		echo "<td>No.</td>";
		echo "<td>ID</td>";
		echo "<td width=150>Stock</td>";
		echo "<td align=right>Last Date</td>";
		echo "<td align=right>Last Open</td>";
		echo "<td align=right>Last Close</td>";
		echo "<td align=right>Last Volume</td>";
		echo "<td align=right>Date</td>";
		echo "<td align=right>Open</td>";
		echo "<td align=right>Close</td>";
		echo "<td align=right>Volume</td>";
		echo "<td align=right>Change %</td>";
		echo "<td align=right>Next Date</td>";
		echo "<td align=right>Next Open</td>";
		echo "<td align=right>Next Close</td>";
		echo "<td align=right>Next Volume</td>";
		echo "<td align=right>Next High</td>";
		echo "<td align=right>Change %</td>";
		echo "</tr>";

		foreach($arows as $k=>$arow){
			$n++;

			$zstock=DB::table('zstock')->find($arow->zstock_id);
			echo '<tr>';
			echo '<td>';
			echo $n;
			echo '</td>';
			echo '<td>';
			echo $arow->zstock_id;
			echo '</td>';			
			echo '<td>';
			echo "<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			echo $zstock->code;
			echo '</a>';
			echo ' '.$zstock->name;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_volume;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->volume;
			echo '</td>';
			echo '<td align=right>';
			if($arow->l_close>0)echo number_format(100*($arow->close-$arow->l_close)/$arow->l_close,2);
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_volume;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_high;
			echo '</td>';
			echo '<td align=right>';
			if($arow->close>0)echo number_format(100*($arow->n_close-$arow->close)/$arow->close,2);
			echo ' / ';
			if($arow->close>0)echo number_format(100*($arow->n_high-$arow->close)/$arow->close,2);
			echo '</td>';
			echo '</tr>';
		}
		echo '</table></center>';
		return '';
	}


	public static function vdownpup(){
		$date=DB::table('ztrade')->max('date');
$date='2015-05-06';
		$arows=DB::table('ztrade')
			->select('*', DB::raw('100*(l_volume-volume)/l_volume as nrate'))
			->where('date',$date)
			->orderby('nrate','desc')			
			->get();

		$n=0;
		echo '<center>';
		echo $date;
		echo '<table width=100%>';
		echo "<tr>";
		echo "<td>No.</td>";
		echo "<td>ID</td>";
		echo "<td width=150>Stock</td>";
		echo "<td align=right>Last Date</td>";
		echo "<td align=right>Last Open</td>";
		echo "<td align=right>Last Close</td>";
		echo "<td align=right>Last Volume</td>";
		echo "<td align=right>Date</td>";
		echo "<td align=right>Open</td>";
		echo "<td align=right>Close</td>";
		echo "<td align=right>Volume</td>";
		echo "<td align=right>Change %</td>";
		echo "<td align=right>Next Date</td>";
		echo "<td align=right>Next Open</td>";
		echo "<td align=right>Next Close</td>";
		echo "<td align=right>Next Volume</td>";
		echo "<td align=right>Change %</td>";
		echo "</tr>";

		foreach($arows as $k=>$arow){
			$vrate=0;
			$prate=0;
			if($arow->l_volume>0)$vrate=($arow->l_volume-$arow->volume)/$arow->l_volume;
			if($arow->l_close>0)$prate=($arow->close-$arow->l_close)/$arow->l_close;

			if($vrate<0.2 or $prate<0.02 or $prate>0.097)continue;

			$n++;

			$zstock=DB::table('zstock')->find($arow->zstock_id);
			echo '<tr>';
			echo '<td>';
			echo $n;
			echo '</td>';
			echo '<td>';
			echo $arow->zstock_id;
			echo '</td>';			
			echo '<td>';
			echo "<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			echo $zstock->code;
			echo '</a>';
			echo ' '.$zstock->name;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->l_volume;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->volume;
			echo '</td>';
			echo '<td align=right>';
			if($arow->l_close>0)echo number_format(100*($arow->close-$arow->l_close)/$arow->l_close,2);
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_date;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_open;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_close;
			echo '</td>';
			echo '<td align=right>';
			echo $arow->n_volume;
			echo '</td>';
			echo '<td align=right>';
			if($arow->close>0){
				echo number_format(100*($arow->n_close-$arow->close)/$arow->close,2);
				echo '/'.number_format(100*$vrate,2);
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table></center>';
		return '';
	}
}
