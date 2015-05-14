<?php namespace App;
/*
SELECT date,pattern,count1,count2,count3,count_up,count_all,round(10*count_up/count_all) as rt FROM `zpattern` where substr(pattern,1,5)='DUDDD' order by rt desc,count_up desc
*/

use Illuminate\Database\Eloquent\Model;

use DB;

class Zpattern extends Model {

	public static function plist(){
		$date='2015-04-28';
		
		$apatterns=DB::table('zpattern')
			->select('pattern', DB::raw('sum(count_all) as total'))
			//->where('date','>=','2015-04-27')
			->groupby('pattern')
			->orderby('total','desc')
			->get();

		$alist=array();
		foreach($apatterns as $apattern){
			$alist[$apattern->pattern]['all']=$apattern->total;
		}

		$apatterns=DB::table('zpattern')
			->where('date','>=','2015-04-25')
			//->where('date','>=','2015-04-30')
			->get();

		foreach($apatterns as $apattern){
			$alist[$apattern->pattern][$apattern->date]['count_all']=$apattern->count_all;
			$alist[$apattern->pattern][$apattern->date]['count3']=$apattern->count3;
		}

		$html='<table width=100%>';
		foreach($alist as $pattern=>$list){
			$html.='<tr>';
			$html.="<td>Pattern</td>";
			foreach($list as $k=>$v){
				$html.="<td>{$k}</td>";				
			}
			$html.='</tr>';
			break;
		}
		foreach($alist as $pattern=>$list){
			$html.='<tr>';
			$html.="<td>{$pattern}</td>";
			foreach($list as $k=>$v){
				
				if($k=='all')$html.="<td>{$v}</td>";
				else{
					$html.="<td><table width=100><tr>";
					foreach($v as $j=>$w){
						$html.="<td>{$w}</td>";
					}
					if((100*$v['count3']/$v['count_all'])>=20){
						$html.="<td>".number_format(100*$v['count3']/$v['count_all'],1)."</td>";
					}
					$html.="</tr></table></td>";
				}
			}
			$html.='</tr>';
		}
		$html.='</table>';

		return $html;
	}
}