<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

class Ztrade extends Model {

	public static function plist($p){
		$ldate=DB::table('ztrade')->max('date');

//$ldate='2015-05-12';

		$azsh=DB::table('zsh')->where('date',$ldate)->first();
		if(empty($p)){
			$p='';
			$pt=$azsh->pattern1;
			$html='<center>VVPPV9P9';
		}elseif($p==1){
			$pt=$azsh->pattern2; 
			$html='<center>VHPHV9P9';
		}elseif($p==2){
			$pt=$azsh->pattern1;
			$html='<center>VVPPVVPP';
		}elseif($p==3){
			$pt=$azsh->pattern1;
			$html='<center>VVPPVVVPPP';
		}

		$date=date('Y-m-d',strtotime($ldate.' -60days'));
//dd($ldate,$date);
		
		$ztrades=DB::table('ztrade')
			->where('date','>=',$date)
			->where('date','<',$ldate)	
			->select('*', DB::raw('count(*) as total'))
			->groupby('pattern'.$p)
			->get();

		$zpts=DB::table('ztrade')
			->where('date','>=',$date)
			->where('date','<',$ldate)	
			->where('pricechange','>=',2)
			->select('*', DB::raw('count(*) as total'))
			->groupby('pattern'.$p)
			->get();
		$apts=array();
		foreach($zpts as $zpt){
			if(empty($p))$apts[$zpt->pattern]=$zpt->total;
			elseif($p==1)$apts[$zpt->pattern1]=$zpt->total;
			elseif($p==2)$apts[$zpt->pattern2]=$zpt->total;
			elseif($p==3)$apts[$zpt->pattern3]=$zpt->total;
		}

		$asort=array();
		$atrades=array();
		foreach($ztrades as $ztrade){
			if(empty($p))$k=$ztrade->pattern;
			elseif($p==1)$k=$ztrade->pattern1;
			elseif($p==2)$k=$ztrade->pattern2;
			elseif($p==3)$k=$ztrade->pattern3;

			$atrades[$k]=$ztrade->total;
			if(isset($apts[$k])){
				$asort[$k]=100*$apts[$k]/$ztrade->total;
			}
		}

		arsort($asort);

		$html.='<table>';
		foreach($asort as $pattern=>$rate){
			if($atrades[$pattern]<20)continue;
			if($pt<>substr($pattern,0,4))continue;
			$html.="<tr>";
			$html.="<td>*";
			$html.=$pattern;
			$html.="</td>";
			$html.="<td>";
			$html.="<a href='/bestpattern/?pattern=$pattern&p=$p' target=_blank>";
			$html.=$atrades[$pattern];
			$html.="</a>";
			$html.="</td>";
			$html.="<td style='color:red;font-weight:600;'>";
			if($pt==substr($pattern,0,4)){
				$npt=DB::table('ztrade')
					->where('date',$ldate)
					->where('pattern'.$p,$pattern)
					->count();
				$html.="<a href='/bestpattern/?date=$ldate&pattern=$pattern&p=$p' target=_blank>{$npt}</a>";
				if($npt>0){
					//$html.='#';
					if(substr($pattern,-1)<>'X')$html.='$';
				}
			}
			$html.="</td>";
			$html.="<td>";
			if(isset($apts[$pattern]))$html.=$apts[$pattern];
			$html.="</td>";
			$html.="<td>";
			if(isset($apts[$pattern]))$html.=number_format($rate,2);
			$html.="</td>";
			$html.="</tr>";
		}		
		$html.='</table></center>';

		return $html;
	}
}