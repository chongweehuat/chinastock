<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Zrender;

class Zstudy1 extends Model {

	public static function bestlist(){
		$date=DB::table('ztrade')->max('date');
		$mdate=date('Y-m-d',time()-(3600*24*20));

		$astocks=array();
		$aid=array();

		$sql="select p1,p2,sum(cprofit) as n from zdstudy where date>='{$mdate}' and p2<=9.5 group by p1,p2 order by n desc limit 100";
		$zdstudy=DB::select($sql);
		//file_put_contents('cprofit',serialize($zdstudy));
		//$zdstudy=unserialize(file_get_contents('cprofit'));
		foreach($zdstudy as $z){

			$astock=DB::select("select zstock_id from ztrade where date='$date' and close>=5 and close<=50 and ((close-open)/close)>=({$z->p1}/100) and ((close-open)/close)<=({$z->p2}/100) order by (high-low)/close limit 2");
			if(count($astock)){
				if(!isset($aid[$astock[0]->zstock_id])){
					$astocks[]=$astock[0];
					$aid[$astock[0]->zstock_id]=1;
				}
				if(count($astock)>1 and !isset($aid[$astock[1]->zstock_id])){
					$astocks[]=$astock[1];
					$aid[$astock[1]->zstock_id]=1;
				}
			}
		}

		$zdstudy=DB::select("select p1,p2,sum(hprofit) as n from zdstudy where date>='{$mdate}' and p2<=9.5 group by p1,p2 order by n desc limit 100");
		//file_put_contents('hprofit',serialize($zdstudy));
		//$zdstudy=unserialize(file_get_contents('hprofit'));
		foreach($zdstudy as $z){

			$astock=DB::select("select zstock_id from ztrade where date='$date' and close>=5 and close<=50 and ((close-open)/close)>=({$z->p1}/100) and ((close-open)/close)<=({$z->p2}/100) order by (high-low)/close limit 2");
			
			if(count($astock)){
				if(!isset($aid[$astock[0]->zstock_id])){
					$astocks[]=$astock[0];
					$aid[$astock[0]->zstock_id]=1;
				}
				if(count($astock)>1 and !isset($aid[$astock[1]->zstock_id])){
					$astocks[]=$astock[1];
					$aid[$astock[1]->zstock_id]=1;
				}
			}
		}

		$html=Zrender::stocklist($astocks,$date,'Best - '.$date);
		
		return $html;
	}



	public static function winnerlist(){
		$date=date('Y-m-d',time()-(3600*24*20));
		$astocks=DB::select("select * from ztrade where date>='$date' and close>=10 and close<=30 and ((close-l_close)/l_close)>=0.055 and ((close-l_close)/l_close)<0.12 and l_volume>0 order by ((n_high-close)/close) desc");
		//$astocks=DB::select("select * from ztrade where date>='$date' and close>=5 and ((close-l_close)/l_close)>=0.09 and ((close-l_close)/l_close)<0.098 and l_volume>0 order by ((high-l_low)/l_low) desc");
		return Zrender::stocklist($astocks,$date,'5.5% - 12% - Winner',1);
	}

	public static function winnerdate(){
		$dates=DB::select("select date from ztrade group by date order by date desc limit 20");
		$html='';
		foreach($dates as $k=>$odate){
			$date=$odate->date;
			//$astocks=DB::select("select * from ztrade where date='$date' and close>=5 and close<=50 and ((close-l_close)/l_close)>=0.085 and ((close-l_close)/l_close)<0.095 and l_volume>0 order by (close-l_close)/l_close limit 4");
			$astock=DB::select("select * from ztrade where date='$date' and close>=5 and close<=50 and ((close-open)/close)>=0.045 and ((close-open)/close)<=0.054  order by ((high-low)/close)");

			$ncount=count($astock);
			$astocks=array();
			if($ncount>0)$astocks[]=$astock[0];
			if($ncount>1)$astocks[]=$astock[1];
			$html.=Zrender::stocklist($astocks,$date,'4.5% - 5.4% - Winner '.$date.' / '.$ncount,1);
		}
		return $html;
	}

	public static function maxwin(){
		set_time_limit(9000);
		$time_start=time();
		//$dates=DB::select("select date from ztrade where n_close>0 group by date order by date desc limit 20");
		//$dates=DB::select("select date from ztrade where date='2015-05-14' limit 1");
		$date=DB::table('zsh')->max('date');
		$date1=DB::table('zdstudy')->max('date');

		$zsh=DB::select("select date from zsh where date>'$date1' and date<'$date' order by date");
		if(count($zsh)==0)return 0;

		$date=$zsh[0]->date;

		$nlimit=2;
		$mlimit=5;
		$plimit=50;
		$volume=$date;
		$ob='(high-low)/close';
		$step=0.1;
		for($p1=1;$p1<=9;$p1=$p1+$step){
			for($p2=1;$p2<=9;$p2=$p2+$step){
				if(($p2-$p1)>=$step){					
					$thprofit=0;
					$tcprofit=0;
					//foreach($dates as $k=>$odate){
						//$date=$odate->date;							
						
						//$astocks=DB::select("select * from ztrade where date='$date' and close>=$mlimit and close<=$plimit and ((close-l_close)/l_close)>=($p1/100) and ((close-l_close)/l_close)<=($p2/100) and l_volume>0 order by $ob limit $nlimit");

						$astocks=DB::select("select * from ztrade where date='$date' and close>=$mlimit and close<=$plimit and ((close-open)/close)>=($p1/100) and ((close-open)/close)<=($p2/100) order by $ob limit $nlimit");

						$hprofit=0;
						$cprofit=0;
						if(count($astocks)>0){
							foreach($astocks as $astock){
								if($astock->n_close>0){
									$r=100*($astock->n_high-$astock->close)/$astock->close;
									if(abs($r)<11)$hprofit+=$r;
									$r=100*($astock->n_close-$astock->close)/$astock->close;					
									if(abs($r)<11)$cprofit+=$r;
								}
							}
							$thprofit+=$hprofit/count($astocks);
							$tcprofit+=$cprofit/count($astocks);
						

							DB::table("zdstudy")->insert([
							'date'=>$date,
							'p1'=>$p1,
							'p2'=>$p2,
							'ob'=>$ob,
							'hprofit'=>$hprofit/count($astocks),
							'cprofit'=>$cprofit/count($astocks),
							'nlimit'=>$nlimit,
							'mlimit'=>$mlimit,
							'plimit'=>$plimit,
							'volume'=>$volume,
							]);

						//}

					}

					DB::table("zstudy")->insert([
						'p1'=>$p1,
						'p2'=>$p2,
						'ob'=>$ob,
						'hprofit'=>$thprofit,
						'cprofit'=>$tcprofit,
						'nlimit'=>$nlimit,
						'mlimit'=>$mlimit,
						'plimit'=>$plimit,
						'volume'=>$volume,
						]);

				}
			}
		}
		$time_lapse=time()-$time_start;
		return $time_lapse;
	}

	public static function plist(){
		$date=date('Y-m-d',time()-(3600*24*60));
		$ztrades=DB::select("select * from ztrade where date>='$date' and close>=5 and close<=50 and n_close>0 and ((close-l_close)/l_close)>=0.09 and ((close-l_close)/l_close)<0.098 order by date desc");
		
		$afd=[
			'all'=>0,
			'>0'=>0,
			'>=1'=>0,
			'>=2'=>0,
			'>=3'=>0,
			'>=4'=>0,
			'>=5'=>0,
			'>=6'=>0,
			'>=7'=>0,
			'>=8'=>0,
			'>=9'=>0,
			];

		$adata=array();
		$adata['all']=$afd;
		$adata=self::compute($ztrades,$adata,$afd,'h');

		$avu=array();
		$avu['all']=$afd;
		$avu=self::compare($ztrades,$avu,$afd,'vu');

		$avd=array();
		$avd['all']=$afd;
		$avd=self::compare($ztrades,$avd,$afd,'vd');

		$avuvu=array();
		$avuvu['all']=$afd;
		$avuvu=self::compare($ztrades,$avuvu,$afd,'vuvu');

		$avuvd=array();
		$avuvd['all']=$afd;
		$avuvd=self::compare($ztrades,$avuvd,$afd,'vuvd');

		$avdvu=array();
		$avdvu['all']=$afd;
		$avdvu=self::compare($ztrades,$avdvu,$afd,'vdvu');

		$avdvd=array();
		$avdvd['all']=$afd;
		$avdvd=self::compare($ztrades,$avdvd,$afd,'vdvd');

		$ap0=array();
		$ap0['all']=$afd;
		$ap0=self::compare($ztrades,$ap0,$afd,'p0');

		$ap1=array();
		$ap1['all']=$afd;
		$ap1=self::compare($ztrades,$ap1,$afd,'p1');

		$ap2=array();
		$ap2['all']=$afd;
		$ap2=self::compare($ztrades,$ap2,$afd,'p2');

		$ap3=array();
		$ap3['all']=$afd;
		$ap3=self::compare($ztrades,$ap3,$afd,'p3');
		
		$ap4=array();
		$ap4['all']=$afd;
		$ap4=self::compare($ztrades,$ap4,$afd,'p4');

		$ap5=array();
		$ap5['all']=$afd;
		$ap5=self::compare($ztrades,$ap5,$afd,'p5');

		$ap7=array();
		$ap7['all']=$afd;
		$ap7=self::compare($ztrades,$ap7,$afd,'p7');

		$ap8=array();
		$ap8['all']=$afd;
		$ap8=self::compare($ztrades,$ap8,$afd,'p8');

		$ap85=array();
		$ap85['all']=$afd;
		$ap85=self::compare($ztrades,$ap85,$afd,'p85');

		$ap9=array();
		$ap9['all']=$afd;
		$ap9=self::compare($ztrades,$ap9,$afd,'p9');

		$ap95=array();
		$ap95['all']=$afd;
		$ap95=self::compare($ztrades,$ap95,$afd,'p95');

		$ap98=array();
		$ap98['all']=$afd;
		$ap98=self::compare($ztrades,$ap98,$afd,'p98');

		$html="<h2>Ratio Analysis 90% - 98%</h2>";
		$html.="<table width=100%>";
		$html.="<tr>";
		$html.="<td>No.</td>";
		$html.="<td>Date</td>";
		$html.="<td>All</td>";
		$html.="<td>>0</td>";
		$html.="<td>>=1</td>";
		$html.="<td>>=2</td>";
		$html.="<td>>=3</td>";
		$html.="<td>>=4</td>";
		$html.="<td>>=5</td>";
		$html.="<td>>=6</td>";
		$html.="<td>>=7</td>";
		$html.="<td>>=8</td>";
		$html.="<td>>=9</td>";
		$html.="</tr>";

		$n=1;
		foreach($adata as $k=>$v){
			if($k=='pt')continue;
			$html.="<tr>";
			$html.="<td>{$n}.</td>";
			$html.="<td>$k</td>";
			
			$html.="<td>{$v['all']}</td>";

			$nrate=round(100*$v['>0']/$v['all']);
			$html.="<td>{$v['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$v['>='.$i]/$v['all']);
				$html.="<td>{$v['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VU</td>";
			
			$html.="<td>{$avu[$k]['all']}</td>";

			$nrate=0;
			if($avu[$k]['all']>0)$nrate=round(100*$avu[$k]['>0']/$avu[$k]['all']);
			$html.="<td>{$avu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avu[$k]['all']>0)$nrate=round(100*$avu[$k]['>='.$i]/$avu[$k]['all']);
				$html.="<td>{$avu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VD</td>";
			
			$html.="<td>{$avd[$k]['all']}</td>";

			$nrate=0;
			if($avd[$k]['all']>0)$nrate=round(100*$avd[$k]['>0']/$avd[$k]['all']);
			$html.="<td>{$avd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avd[$k]['all']>0)$nrate=round(100*$avd[$k]['>='.$i]/$avd[$k]['all']);
				$html.="<td>{$avd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VUVU</td>";
			
			$html.="<td>{$avuvu[$k]['all']}</td>";

			$nrate=0;
			if($avuvu[$k]['all']>0)$nrate=round(100*$avuvu[$k]['>0']/$avuvu[$k]['all']);
			$html.="<td>{$avuvu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avuvu[$k]['all']>0)$nrate=round(100*$avuvu[$k]['>='.$i]/$avuvu[$k]['all']);
				$html.="<td>{$avuvu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VUVD</td>";
			
			$html.="<td>{$avuvd[$k]['all']}</td>";

			$nrate=0;
			if($avuvd[$k]['all']>0)$nrate=round(100*$avuvd[$k]['>0']/$avuvd[$k]['all']);
			$html.="<td>{$avuvd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avuvd[$k]['all']>0)$nrate=round(100*$avuvd[$k]['>='.$i]/$avuvd[$k]['all']);
				$html.="<td>{$avuvd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VDVU</td>";
			
			$html.="<td>{$avdvu[$k]['all']}</td>";

			$nrate=0;
			if($avdvu[$k]['all']>0)$nrate=round(100*$avdvu[$k]['>0']/$avdvu[$k]['all']);
			$html.="<td>{$avdvu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avdvu[$k]['all']>0)$nrate=round(100*$avdvu[$k]['>='.$i]/$avdvu[$k]['all']);
				$html.="<td>{$avdvu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VDVD</td>";
			
			$html.="<td>{$avdvd[$k]['all']}</td>";

			$nrate=0;
			if($avdvd[$k]['all']>0)$nrate=round(100*$avdvd[$k]['>0']/$avdvd[$k]['all']);
			$html.="<td>{$avdvd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($avdvd[$k]['all']>0)$nrate=round(100*$avdvd[$k]['>='.$i]/$avdvd[$k]['all']);
				$html.="<td>{$avdvd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P0</td>";
			
			$html.="<td>{$ap0[$k]['all']}</td>";

			$nrate=0;
			if($ap0[$k]['all']>0)$nrate=round(100*$ap0[$k]['>0']/$ap0[$k]['all']);
			$html.="<td>{$ap0[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap0[$k]['all']>0)$nrate=round(100*$ap0[$k]['>='.$i]/$ap0[$k]['all']);
				$html.="<td>{$ap0[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P1</td>";
			
			$html.="<td>{$ap1[$k]['all']}</td>";

			$nrate=0;
			if($ap1[$k]['all']>0)$nrate=round(100*$ap1[$k]['>0']/$ap1[$k]['all']);
			$html.="<td>{$ap1[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap1[$k]['all']>0)$nrate=round(100*$ap1[$k]['>='.$i]/$ap1[$k]['all']);
				$html.="<td>{$ap1[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P2</td>";
			
			$html.="<td>{$ap2[$k]['all']}</td>";

			$nrate=0;
			if($ap2[$k]['all']>0)$nrate=round(100*$ap2[$k]['>0']/$ap2[$k]['all']);
			$html.="<td>{$ap2[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap2[$k]['all']>0)$nrate=round(100*$ap2[$k]['>='.$i]/$ap2[$k]['all']);
				$html.="<td>{$ap2[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P3</td>";
			
			$html.="<td>{$ap3[$k]['all']}</td>";

			$nrate=0;
			if($ap3[$k]['all']>0)$nrate=round(100*$ap3[$k]['>0']/$ap3[$k]['all']);
			$html.="<td>{$ap3[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap3[$k]['all']>0)$nrate=round(100*$ap3[$k]['>='.$i]/$ap3[$k]['all']);
				$html.="<td>{$ap3[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P4</td>";
			
			$html.="<td>{$ap4[$k]['all']}</td>";

			$nrate=0;
			if($ap4[$k]['all']>0)$nrate=round(100*$ap4[$k]['>0']/$ap4[$k]['all']);
			$html.="<td>{$ap4[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap4[$k]['all']>0)$nrate=round(100*$ap4[$k]['>='.$i]/$ap4[$k]['all']);
				$html.="<td>{$ap4[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P5</td>";
			
			$html.="<td>{$ap5[$k]['all']}</td>";

			$nrate=0;
			if($ap5[$k]['all']>0)$nrate=round(100*$ap5[$k]['>0']/$ap5[$k]['all']);
			$html.="<td>{$ap5[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap5[$k]['all']>0)$nrate=round(100*$ap5[$k]['>='.$i]/$ap5[$k]['all']);
				$html.="<td>{$ap5[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P7</td>";
			
			$html.="<td>{$ap7[$k]['all']}</td>";

			$nrate=0;
			if($ap7[$k]['all']>0)$nrate=round(100*$ap7[$k]['>0']/$ap7[$k]['all']);
			$html.="<td>{$ap7[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap7[$k]['all']>0)$nrate=round(100*$ap7[$k]['>='.$i]/$ap7[$k]['all']);
				$html.="<td>{$ap7[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P8</td>";
			
			$html.="<td>{$ap8[$k]['all']}</td>";

			$nrate=0;
			if($ap8[$k]['all']>0)$nrate=round(100*$ap8[$k]['>0']/$ap8[$k]['all']);
			$html.="<td>{$ap8[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap8[$k]['all']>0)$nrate=round(100*$ap8[$k]['>='.$i]/$ap8[$k]['all']);
				$html.="<td>{$ap8[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P85</td>";
			
			$html.="<td>{$ap85[$k]['all']}</td>";

			$nrate=0;
			if($ap85[$k]['all']>0)$nrate=round(100*$ap85[$k]['>0']/$ap85[$k]['all']);
			$html.="<td>{$ap85[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap85[$k]['all']>0)$nrate=round(100*$ap85[$k]['>='.$i]/$ap85[$k]['all']);
				$html.="<td>{$ap85[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P9</td>";
			
			$html.="<td>{$ap9[$k]['all']}</td>";

			$nrate=0;
			if($ap9[$k]['all']>0)$nrate=round(100*$ap9[$k]['>0']/$ap9[$k]['all']);
			$html.="<td>{$ap9[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap9[$k]['all']>0)$nrate=round(100*$ap9[$k]['>='.$i]/$ap9[$k]['all']);
				$html.="<td>{$ap9[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P95</td>";
			
			$html.="<td>{$ap95[$k]['all']}</td>";

			$nrate=0;
			if($ap95[$k]['all']>0)$nrate=round(100*$ap95[$k]['>0']/$ap95[$k]['all']);
			$html.="<td>{$ap95[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap95[$k]['all']>0)$nrate=round(100*$ap95[$k]['>='.$i]/$ap95[$k]['all']);
				$html.="<td>{$ap95[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P98</td>";
			
			$html.="<td>{$ap98[$k]['all']}</td>";

			$nrate=0;
			if($ap98[$k]['all']>0)$nrate=round(100*$ap98[$k]['>0']/$ap98[$k]['all']);
			$html.="<td>{$ap98[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=0;
				if($ap98[$k]['all']>0)$nrate=round(100*$ap98[$k]['>='.$i]/$ap98[$k]['all']);
				$html.="<td>{$ap98[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr><td colspan=13><br></td></tr>";


			$n++;
		}

		$html.="</table>";

		$html.="<table>";
		foreach($adata['pt'] as $k=>$v){
			foreach($v as $pt=>$n){
				$html.="<tr>";
				$html.="<td>";
				$html.=$k;
				$html.="</td>";
				$html.="<td>";
				$html.=$pt;
				$html.="</td>";
				$html.="<td>";
				$html.=$n;
				$html.="</td>";
				$html.="</tr>";
			}
		}
		$html.="</table>";

		return $html;
	}

	public static function compute($ztrades,$adata,$afd,$mode){
		foreach($ztrades as $ztrade){
			$nprice0=$ztrade->close;
			if($mode=='h')$nprice=$ztrade->n_high;
			elseif($mode=='o') $nprice=$ztrade->n_open;
			elseif($mode=='c') $nprice=$ztrade->n_close;
			else{
				$nprice0=$ztrade->n_open;
				$nprice=$ztrade->n_high;
			}

			if(!isset($adata[$ztrade->date])){
				$adata[$ztrade->date]=$afd;
			}

			$adata['all']['all']++;
			$adata[$ztrade->date]['all']++;			

			if($nprice>$ztrade->close){
				$adata['all']['>0']++;
				$adata[$ztrade->date]['>0']++;
			}

			$nrate=0;
			if($nprice0>0)$nrate=round(100*($nprice-$nprice0)/$nprice0);

			if($nrate>=1){
				if($nrate>9)$nrate=9;
				
				for($r=$nrate;$r>0;$r--){
					$adata['all']['>='.$r]++;
					$adata[$ztrade->date]['>='.$r]++;
				}

				if($nrate>=3){
					if(!isset($adata['pt'][0][$ztrade->pattern]))$adata['pt'][0][$ztrade->pattern]=0;
					$adata['pt'][0][$ztrade->pattern]++;

					if(!isset($adata['pt'][1][$ztrade->pattern1]))$adata['pt'][1][$ztrade->pattern1]=0;
					$adata['pt'][1][$ztrade->pattern1]++;

					if(!isset($adata['pt'][2][$ztrade->pattern2]))$adata['pt'][2][$ztrade->pattern2]=0;
					$adata['pt'][2][$ztrade->pattern2]++;

					if(!isset($adata['pt'][3][$ztrade->pattern3]))$adata['pt'][3][$ztrade->pattern3]=0;
					$adata['pt'][3][$ztrade->pattern3]++;
				}

			}
		}
		return $adata;
	}

	public static function compare($ztrades,$adata,$afd,$mode){
		foreach($ztrades as $ztrade){
			$nprice0=$ztrade->close;
			$nprice=$ztrade->n_high;			

			if(!isset($adata[$ztrade->date])){
				$adata[$ztrade->date]=$afd;
			}

			$r=0;
			if($ztrade->h_close>0)$r=($ztrade->l_close-$ztrade->h_close)/$ztrade->h_close;

			$filter=false;
			if($mode=='vu'){
				if(	$ztrade->volume>$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='vd'){
				if(	$ztrade->volume<$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='vuvd'){
				if(	$ztrade->l_volume>$ztrade->h_volume and 
					$ztrade->volume<$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->h_volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='vdvd'){
				if(	$ztrade->l_volume<$ztrade->h_volume and 
					$ztrade->volume<$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->h_volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='vuvu'){
				if(	$ztrade->l_volume>$ztrade->h_volume and 
					$ztrade->volume>$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->h_volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='vdvu'){
				if(	$ztrade->l_volume<$ztrade->h_volume and 
					$ztrade->volume>$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->h_volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='p0'){
				if($ztrade->h_close>0 and $r>=0.001 and $r<0.01){
					$filter=true;
				}
			}elseif($mode=='p1'){
				if($ztrade->h_close>0 and $r>=0.01 and $r<0.02){
					$filter=true;
				}
			}elseif($mode=='p2'){
				if($ztrade->h_close>0 and $r>=0.02 and $r<0.03){
					$filter=true;
				}
			}elseif($mode=='p3'){
				if($ztrade->h_close>0 and $r>=0.03 and $r<0.04){
					$filter=true;
				}
			}elseif($mode=='p4'){
				if($ztrade->h_close>0 and $r>=0.04 and $r<0.05){
					$filter=true;
				}
			}elseif($mode=='p5'){
				if($ztrade->h_close>0 and $r>=0.05 and $r<0.07){
					$filter=true;
				}
			}elseif($mode=='p7'){
				if($ztrade->h_close>0 and $r>=0.07 and $r<0.08){
					$filter=true;
				}
			}elseif($mode=='p8'){
				if($ztrade->h_close>0 and $r>=0.08 and $r<0.085){
					$filter=true;
				}
			}elseif($mode=='p85'){
				if($ztrade->h_close>0 and $r>=0.085 and $r<0.09){
					$filter=true;
				}
			}elseif($mode=='p9'){
				if($ztrade->h_close>0 and $r>=0.09 and $r<0.095){
					$filter=true;
				}
			}elseif($mode=='p95'){
				if($ztrade->h_close>0 and $r>=0.095 and $r<0.098){
					$filter=true;
				}
			}elseif($mode=='p98'){
				if($ztrade->h_close>0 and $r>=0.098){
					$filter=true;
				}
			}

			if($filter){
				$adata['all']['all']++;
				$adata[$ztrade->date]['all']++;

				if($nprice>$ztrade->close){
					$adata['all']['>0']++;
					$adata[$ztrade->date]['>0']++;
				}

				$nrate=0;
				if($nprice0>0)$nrate=round(100*($nprice-$nprice0)/$nprice0);

				if($nrate>=1){
					if($nrate>9)$nrate=9;
					
					for($r=$nrate;$r>0;$r--){
						$adata['all']['>='.$r]++;
						$adata[$ztrade->date]['>='.$r]++;
					}
				}
			}
		}
		return $adata;
	}
}