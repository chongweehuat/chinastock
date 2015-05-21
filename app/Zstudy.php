<?php namespace App;

/*
select date,zstock_id,(close-l_close)/l_close ,(n_high-close)/close as n,n_high,close, n_high-close,volume-l_volume from ztrade where  ((close-l_close)/l_close)>=0.09 and ((close-l_close)/l_close)<0.098 order by n desc
*/

use Illuminate\Database\Eloquent\Model;

use DB;

class Zstudy extends Model {

	public static function plist(){
		$date=date('Y-m-d',time()-(3600*24*20));

		$ztrades=DB::table('ztrade')
			->where('date','>=',$date)
			->where('n_close','>=',5)
			->where('close','>=',5)
			->where('close','<=',50)
			->orderby('date','desc')
			->get();

		$adata=array();
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
		$adata['all']=$afd;

		$adata=self::compute($ztrades,$adata,$afd,'h');

		$aopen=array();
		$aopen['all']=$afd;
		$aopen=self::compute($ztrades,$aopen,$afd,'o');

		$aclose=array();
		$aclose['all']=$afd;
		$aclose=self::compute($ztrades,$aclose,$afd,'c');

		$ajump=array();
		$ajump['all']=$afd;
		$ajump=self::compute($ztrades,$ajump,$afd,'j');

		$apuvu=array();
		$apuvu['all']=$afd;
		$apuvu=self::compare($ztrades,$apuvu,$afd,'puvu');

		$apuvd=array();
		$apuvd['all']=$afd;
		$apuvd=self::compare($ztrades,$apuvd,$afd,'puvd');

		$apdvu=array();
		$apdvu['all']=$afd;
		$apdvu=self::compare($ztrades,$apdvu,$afd,'pdvu');

		$apdvd=array();
		$apdvd['all']=$afd;
		$apdvd=self::compare($ztrades,$apdvd,$afd,'pdvd');

		$apu=array();
		$apu['all']=$afd;
		$apu=self::compare($ztrades,$apu,$afd,'pu');

		$apd=array();
		$apd['all']=$afd;
		$apd=self::compare($ztrades,$apd,$afd,'pd');

		$avu=array();
		$avu['all']=$afd;
		$avu=self::compare($ztrades,$avu,$afd,'vu');

		$avd=array();
		$avd['all']=$afd;
		$avd=self::compare($ztrades,$avd,$afd,'vd');

		$ap1=array();
		$ap1['all']=$afd;
		$ap1=self::compare($ztrades,$ap1,$afd,'p1');

		$ap3=array();
		$ap3['all']=$afd;
		$ap3=self::compare($ztrades,$ap3,$afd,'p3');	

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

		$html="<h2>Ratio Analysis</h2>";
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
			$html.="<td>Open</td>";
			
			$html.="<td>{$aopen[$k]['all']}</td>";

			$nrate=round(100*$aopen[$k]['>0']/$aopen[$k]['all']);
			$html.="<td>{$aopen[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$aopen[$k]['>='.$i]/$aopen[$k]['all']);
				$html.="<td>{$aopen[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>Close</td>";
			
			$html.="<td>{$aclose[$k]['all']}</td>";

			$nrate=round(100*$aclose[$k]['>0']/$aclose[$k]['all']);
			$html.="<td>{$aclose[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$aclose[$k]['>='.$i]/$aclose[$k]['all']);
				$html.="<td>{$aclose[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>Jump</td>";
			
			$html.="<td>{$ajump[$k]['all']}</td>";

			$nrate=round(100*$ajump[$k]['>0']/$ajump[$k]['all']);
			$html.="<td>{$ajump[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ajump[$k]['>='.$i]/$ajump[$k]['all']);
				$html.="<td>{$ajump[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PUVU</td>";
			
			$html.="<td>{$apuvu[$k]['all']}</td>";

			$nrate=round(100*$apuvu[$k]['>0']/$apuvu[$k]['all']);
			$html.="<td>{$apuvu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apuvu[$k]['>='.$i]/$apuvu[$k]['all']);
				$html.="<td>{$apuvu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PUVD</td>";
			
			$html.="<td>{$apuvd[$k]['all']}</td>";

			$nrate=round(100*$apuvd[$k]['>0']/$apuvd[$k]['all']);
			$html.="<td>{$apuvd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apuvd[$k]['>='.$i]/$apuvd[$k]['all']);
				$html.="<td>{$apuvd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PDVU</td>";
			
			$html.="<td>{$apdvu[$k]['all']}</td>";

			$nrate=round(100*$apdvu[$k]['>0']/$apdvu[$k]['all']);
			$html.="<td>{$apdvu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apdvu[$k]['>='.$i]/$apdvu[$k]['all']);
				$html.="<td>{$apdvu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PDVD</td>";
			
			$html.="<td>{$apdvd[$k]['all']}</td>";

			$nrate=round(100*$apdvd[$k]['>0']/$apdvd[$k]['all']);
			$html.="<td>{$apdvd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apdvd[$k]['>='.$i]/$apdvd[$k]['all']);
				$html.="<td>{$apdvd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";


			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PU</td>";
			
			$html.="<td>{$apu[$k]['all']}</td>";

			$nrate=round(100*$apu[$k]['>0']/$apu[$k]['all']);
			$html.="<td>{$apu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apu[$k]['>='.$i]/$apu[$k]['all']);
				$html.="<td>{$apu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>PD</td>";
			
			$html.="<td>{$apd[$k]['all']}</td>";

			$nrate=round(100*$apd[$k]['>0']/$apd[$k]['all']);
			$html.="<td>{$apd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$apd[$k]['>='.$i]/$apd[$k]['all']);
				$html.="<td>{$apd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VU</td>";
			
			$html.="<td>{$avu[$k]['all']}</td>";

			$nrate=round(100*$avu[$k]['>0']/$avu[$k]['all']);
			$html.="<td>{$avu[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$avu[$k]['>='.$i]/$avu[$k]['all']);
				$html.="<td>{$avu[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>VD</td>";
			
			$html.="<td>{$avd[$k]['all']}</td>";

			$nrate=round(100*$avd[$k]['>0']/$avd[$k]['all']);
			$html.="<td>{$avd[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$avd[$k]['>='.$i]/$avd[$k]['all']);
				$html.="<td>{$avd[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P1</td>";
			
			$html.="<td>{$ap1[$k]['all']}</td>";

			$nrate=round(100*$ap1[$k]['>0']/$ap1[$k]['all']);
			$html.="<td>{$ap1[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap1[$k]['>='.$i]/$ap1[$k]['all']);
				$html.="<td>{$ap1[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P3</td>";
			
			$html.="<td>{$ap3[$k]['all']}</td>";

			$nrate=round(100*$ap3[$k]['>0']/$ap3[$k]['all']);
			$html.="<td>{$ap3[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap3[$k]['>='.$i]/$ap3[$k]['all']);
				$html.="<td>{$ap3[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P5</td>";
			
			$html.="<td>{$ap5[$k]['all']}</td>";

			$nrate=round(100*$ap5[$k]['>0']/$ap5[$k]['all']);
			$html.="<td>{$ap5[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap5[$k]['>='.$i]/$ap5[$k]['all']);
				$html.="<td>{$ap5[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr>";
			$html.="<td></td>";
			$html.="<td>P7</td>";
			
			$html.="<td>{$ap7[$k]['all']}</td>";

			$nrate=round(100*$ap7[$k]['>0']/$ap7[$k]['all']);
			$html.="<td>{$ap7[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap7[$k]['>='.$i]/$ap7[$k]['all']);
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

			$nrate=round(100*$ap9[$k]['>0']/$ap9[$k]['all']);
			$html.="<td>{$ap9[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap9[$k]['>='.$i]/$ap9[$k]['all']);
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

			$nrate=round(100*$ap98[$k]['>0']/$ap98[$k]['all']);
			$html.="<td>{$ap98[$k]['>0']} {$nrate}%</td>";

			for($i=1;$i<=9;$i++){
				$nrate=round(100*$ap98[$k]['>='.$i]/$ap98[$k]['all']);
				$html.="<td>{$ap98[$k]['>='.$i]} {$nrate}%</td>";
			}

			$html.="</tr>";

			$html.="<tr><td colspan=13><br></td></tr>";


			$n++;
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
			if($ztrade->l_close>0)$r=($ztrade->close-$ztrade->l_close)/$ztrade->l_close;

			$filter=false;
			if($mode=='puvu'){
				if($ztrade->close>$ztrade->l_close and 
					$ztrade->volume>$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='puvd'){
				if($ztrade->close>$ztrade->l_close and 
					$ztrade->volume<$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='pdvu'){
				if($ztrade->close<$ztrade->l_close and 
					$ztrade->volume>$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='pdvd'){
				if($ztrade->close<$ztrade->l_close and 
					$ztrade->volume<$ztrade->l_volume and 
					$ztrade->volume>0 and 
					$ztrade->l_volume>0){
					$filter=true;
				}
			}elseif($mode=='pu'){
				if($ztrade->close>$ztrade->l_close and $ztrade->l_close>0){
					$filter=true;
				}
			}elseif($mode=='pd'){
				if($ztrade->close<$ztrade->l_close and $ztrade->close>0){
					$filter=true;
				}
			}elseif($mode=='vu'){
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
			}elseif($mode=='p1'){
				if($ztrade->l_close>0 and $r>=0.01 and $r<0.03){
					$filter=true;
				}
			}elseif($mode=='p3'){
				if($ztrade->l_close>0 and $r>=0.03 and $r<0.05){
					$filter=true;
				}
			}elseif($mode=='p5'){
				if($ztrade->l_close>0 and $r>=0.05 and $r<0.07){
					$filter=true;
				}
			}elseif($mode=='p7'){
				if($ztrade->l_close>0 and $r>=0.07 and $r<0.08){
					$filter=true;
				}
			}elseif($mode=='p8'){
				if($ztrade->l_close>0 and $r>=0.08 and $r<0.085){
					$filter=true;
				}
			}elseif($mode=='p85'){
				if($ztrade->l_close>0 and $r>=0.085 and $r<0.09){
					$filter=true;
				}
			}elseif($mode=='p9'){
				if($ztrade->l_close>0 and $r>=0.09 and $r<0.095){
					$filter=true;
				}
			}elseif($mode=='p95'){
				if($ztrade->l_close>0 and $r>=0.095 and $r<0.098){
					$filter=true;
				}
			}elseif($mode=='p98'){
				if($ztrade->l_close>0 and $r>=0.098){
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
