<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Zdstudy extends Model {

	public static function cleandb(){
		set_time_limit(900);
		$zdstudy=DB::select("select id from zdstudy order by id desc limit 1");
		$ncount=$zdstudy[0]->id;
		for($n=17131;$n<=$ncount;$n++){
			$zdstudy=DB::table('zdstudy')->find($n);
			$zdstudy1=DB::table('zdstudy1')
				->where('date',$zdstudy->date)
				->where('p1',$zdstudy->p1)
				->where('p2',$zdstudy->p2)
				->get();

			if(count($zdstudy1)==0){
				DB::table('zdstudy1')->insert([
					'date'=>$zdstudy->date,
					'p1'=>$zdstudy->p1,
					'p2'=>$zdstudy->p2,
					'ob'=>$zdstudy->ob,
					'hprofit'=>$zdstudy->hprofit,
					'cprofit'=>$zdstudy->cprofit,
					'nlimit'=>$zdstudy->nlimit,
					'mlimit'=>$zdstudy->mlimit,
					'plimit'=>$zdstudy->plimit,
					'volume'=>$zdstudy->volume,
					]);
			}
		}
	}

	public static function plist(){
		$mdate=date('Y-m-d',time()-(3600*24*20));
		$zdstudy=DB::select("select * from zdstudy where date>='$mdate' order by date desc");
		$amax=array();
		foreach($zdstudy as $v){
			if(!isset($amax[$v->date]))$amax[$v->date]=['cid'=>0,'cp1'=>0,'cp2'=>0,'c'=>0,'h'=>0];
			if($v->cprofit>$amax[$v->date]['c']){
				$amax[$v->date]['c']=$v->cprofit;
				$amax[$v->date]['cid']=$v->id;
				$amax[$v->date]['cp1']=$v->p1;
				$amax[$v->date]['cp2']=$v->p2;
			}
			if($v->hprofit>$amax[$v->date]['h']){
				$amax[$v->date]['h']=$v->hprofit;
				$amax[$v->date]['hid']=$v->id;
				$amax[$v->date]['hp1']=$v->p1;
				$amax[$v->date]['hp2']=$v->p2;
			}
		}

		$zdstudy=DB::select("select * from zdstudy order by id desc limit 1");

		//$html='<meta http-equiv="refresh" content="30">';
		$html='';
		$html.="<center>{$zdstudy[0]->p1} - {$zdstudy[0]->p2}<table width=50%>";
		$html.='<tr>';
		$html.='<td>';
		$html.='Date';
		$html.='</td>';	
		$html.='<td>';
		$html.='CP1';
		$html.='</td>';	
		$html.='<td>';
		$html.='CP2';
		$html.='</td>';
		$html.='<td>';
		$html.='cprofit';
		$html.='</td>';		
		$html.='<td>';
		$html.='hprofit';
		$html.='</td>';
		$html.='<td>';
		$html.='Count';
		$html.='</td>';
		$html.='<td>';
		$html.='Volume';
		$html.='</td>';
		$html.='<td>';
		$html.='Index';
		$html.='</td>';
		$html.='</tr>';
		foreach($amax as $date=>$v){
			$zsh=DB::select("select * from zsh where date='$date'");
			$volume=100*($zsh[0]->volume-$zsh[0]->l_volume)/$zsh[0]->l_volume;
			$index=100*($zsh[0]->close-$zsh[0]->l_close)/$zsh[0]->l_close;
			//$azdstudy=DB::select("select count(*) as ncount from ztrade where date='$date' and (100*(close-l_close)/l_close)>={$v['cp1']} and (100*(close-l_close)/l_close)<={$v['cp2']}");
			//$ncount=$azdstudy[0]->ncount;
			$ncount=0;
			$html.='<tr>';
			$html.='<td>';
			$html.=$date;
			$html.='</td>';	
			$html.='<td>';
			$html.=$v['cp1'];
			$html.='</td>';	
			$html.='<td>';
			$html.=$v['cp2'];
			$html.='</td>';	
			$html.='<td>';
			$html.=$v['c'];
			$html.='</td>';				
			$html.='<td>';
			$html.=$v['h'];
			$html.='</td>';
			$html.='<td>';
			$html.=$ncount;
			$html.='</td>';
			$html.='<td>';
			$html.=number_format($volume,2);
			$html.='</td>';
			$html.='<td>';
			$html.=number_format($index,2);
			$html.='</td>';
			$html.='</tr>';
			$zdstudy=DB::select("select * from zdstudy where date='$date' and cprofit>={$v['c']}-2 and id<>{$v['cid']} order by cprofit desc,p1 desc limit 60");
			$z1=false;
			$mcount=count($zdstudy);
			foreach($zdstudy as $k=>$z){
				if($z1 and $z1->cprofit<>$z->cprofit){
					$html.=self::xlist($k,$z1,$mcount,0);
				}

				$z1=$z;

				if($v['c']==$z->cprofit or $k<(count($zdstudy)-1))continue;
				//$azdstudy=DB::select("select count(*) as ncount from ztrade where date='$date' and (100*(close-l_close)/l_close)>={$z->p1} and (100*(close-l_close)/l_close)<={$z->p2}");
				//$ncount=$azdstudy[0]->ncount;
				$ncount=0;
								
				$html.=self::xlist($k,$z,$mcount,$ncount);

				//if($k==2)break;
			}
			
		}
		$html.='</table></center>';

		return $html;
	}

	public static function xlist($k,$z,$mcount,$ncount){
		$html='<tr>';
		$html.='<td>';
		$html.=$k+1;
		$html.=' / ';
		$html.=$mcount;	
		$html.='</td>';	
		$html.='<td>';
		$html.=$z->p1;
		$html.='</td>';	
		$html.='<td>';
		$html.=$z->p2;
		$html.='</td>';	
		$html.='<td>';
		$html.=$z->cprofit;
		$html.='</td>';	
		
		$html.='<td>';
		$html.=$z->hprofit;
		$html.='</td>';
		$html.='<td>';
		$html.=$ncount;
		$html.='</td>';
		$html.='<td>';

		$html.='</td>';
		$html.='<td>';

		$html.='</td>';
		$html.='</tr>';
		return $html;
	}
}