<?php namespace App;
use Illuminate\Database\Eloquent\Model;

use DB;

class Zrender extends Model {
	public static function stocklist($astocks,$date,$title='',$mode=0){
		$n=0;
		$html='<center>';
		$html.="<h1>$title</h1>";
		$html.='<table width=100%>';
		$html.="<tr>";
		$html.="<td>No.</td>";
		$html.="<td>ID</td>";
		$html.="<td width=150>Stock</td>";
		$html.="<td align=right>Last Date</td>";
		$html.="<td align=right>Last Open / High / Low</td>";
		$html.="<td align=right>Last Close</td>";
		$html.="<td align=right>Last Volume</td>";
		$html.="<td align=right>Date</td>";
		$html.="<td align=right>Open / High / Low</td>";
		$html.="<td align=right>Close</td>";
		$html.="<td align=right>Volume</td>";
		$html.="<td align=right>Change %</td>";
		$html.="<td align=right>Next Date</td>";
		$html.="<td align=right>Next Open</td>";
		$html.="<td align=right>Next Close</td>";
		$html.="<td align=right>Next Volume</td>";
		$html.="<td align=right>Next High</td>";
		$html.="<td align=right>Change %</td>";
		$html.="</tr>";
		foreach($astocks as $astock){
			$zstock_id=$astock->zstock_id;
			$n++;

			if($mode){
				$arow=$astock;
			}else{ 
				$ztrades=DB::select("select * from ztrade where zstock_id=$zstock_id and date='$date'");
				$arow=$ztrades[0];
			}
			$zstock=DB::table('zstock')->find($zstock_id);
			$html.='<tr>';
			$html.='<td>';
			$html.=$n;
			$html.='</td>';
			$html.='<td>';
			$html.=$arow->zstock_id;
			$html.='</td>';			
			$html.='<td>';
			$html.="<a href='http://finance.sina.com.cn/realstock/company/{$zstock->code}/nc.shtml' target=_blank>";
			$html.=$zstock->code;
			$html.='</a>';
			$html.=' '.$zstock->name;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->l_date;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->l_open;
			$html.=' / ';
			$html.='<span style="color:red;">';
			$html.=$arow->l_high;
			$html.='</span>';
			$html.=' / ';
			$html.='<span style="color:green;">';
			$html.=$arow->l_low;
			$html.='</span>';
			$html.='</td>';
			$html.='<td align=right>';
			$html.='<span style="color:blue;">';
			$html.=$arow->l_close;
			$html.='</span>';
			$html.=' / ';
			if($arow->h_close>0)$html.=number_format(100*($arow->l_close-$arow->h_close)/$arow->h_close,2).'%';
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->l_volume;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->date;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->open;
			$html.=' / ';
			$html.='<span style="color:red;">';
			$html.=$arow->high;
			$html.='</span>';
			$html.=' / ';
			$html.='<span style="color:green;">';
			$html.=$arow->low;
			$html.='</span>';
			$html.='</td>';
			$html.='<td align=right>';
			$html.='<span style="color:blue;">';
			$html.=$arow->close;
			$html.='</span>';
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->volume;
			$html.=' / ';
			if($arow->l_volume>0)$html.=number_format(100*($arow->volume-$arow->l_volume)/$arow->l_volume,2).'%';
			$html.='</td>';
			$html.='<td align=right>';
			if($arow->l_close>0){
				$changes=100*($arow->close-$arow->l_close)/$arow->l_close;
				if($changes<=9.5)$html.="<span style='font-weight:600;color:red;font-size:120%;'>";
				$html.=number_format($changes,2).'%';
				if($changes<=9.5)$html.="</span>";
			}
			$html.=' / ';
			$html.=$arow->pattern3;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->n_date;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->n_open;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->n_close;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->n_volume;
			$html.='</td>';
			$html.='<td align=right>';
			$html.=$arow->n_high;
			$html.='</td>';
			$html.='<td align=right>';
			$r=0;
			if($arow->close>0)$r=100*($arow->n_close-$arow->close)/$arow->close;
			if($r<0)$html.='<span style="color:green;">';
			else $html.='<span style="color:red;">';
			$html.=number_format($r,2).'%';
			$html.='</span>';
			$html.=' / ';
			$r=0;
			if($arow->close>0)$r=100*($arow->n_high-$arow->close)/$arow->close;
			if($r<0)$html.='<span style="color:green;">';
			else $html.='<span style="color:red;">';
			$html.=number_format($r,2).'%';
			$html.='</span>';
			$html.='</td>';
			$html.='</tr>';
		}
		$html.='</table></center>';
		return $html;		
	}
}
