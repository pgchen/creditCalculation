<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class calculation 
{
	private $calcWay;
	public $calcName;
	function __construct()
	{
		$this->calcWay = array('dengebenxi', 'danli', 'daoqihuanben');
		$this->calcName = array('等额本息计算', '单利', '到期还本');
	}

	public function getCalcData($calc = 2, $benjin = 0, $yue = 0, $yuelilv = 0)
	{	
		$calcWay = $this->calcWay;
		if($calc > count($this->calcWay)){
			return array();
		}else{
			$fun = $this->calcWay[$calc - 1];
			return $this->$fun($benjin, $yue, $yuelilv);
		}
	}

	/*
	 * 等额本息计算 
	 */
	private function dengebenxi($benjin,$yue,$yuelilv){
		$yuelilv = $yuelilv * 0.01;
		$fen = floatval(pow((1 + $yuelilv),$yue) -1);
		$benxi = floatval($benjin * $yuelilv * pow((1+$yuelilv),$yue)/$fen); 
		$daishou = floatval($benxi *$yue);
				
		$moneys = array();
		$money = array();
		$money['benjin'] = $benjin;
		$money['zonglixi'] = round($daishou-$benjin,2);
		$money['daishou'] = round($daishou,2);
		$moneys[0] = $money;
		$yuebenjinAdd = $yuelixiAdd = 0;
		for ($i=0;$i<$yue;$i++) {
			$money = array();
			$money['yuebenjin'] = round($benxi / pow((1 + $yuelilv),$yue - $i),2);
			$money['yuelixi'] = abs(round($benxi - $money['yuebenjin'],2));
			$daishou -= round($benxi,2);
			if(($i+1) == $yue){
				$money['yuebenjin'] = $moneys[0]['benjin'] - $yuebenjinAdd;
				$money['yuelixi'] = $moneys[0]['zonglixi'] - $yuelixiAdd;
				$daishou = 0;
			}
			$yuebenjinAdd += $money['yuebenjin'];
			$yuelixiAdd += $money['yuelixi'];
			$money['daishoubenxi'] =round($daishou,2);
			$moneys[$i+1] = $money;
		}
		return $moneys;
	}

	/*
	 * 单利 
	 */
	private function danli($benjin,$yue,$yuelilv){
		$moneys = array();
		$money = array();
		$yuelilv = $yuelilv *0.01;
		$money['benjin'] = $benjin;
		$money['zonglixi'] = round($benjin*$yuelilv*$yue,2);
		$money['daishou'] = $benjin+$money['zonglixi'];	
		$moneys[0] = $money;
		$money = array();
		$money['yuebenjin'] = round($benjin/$yue,2);
		$money['yuelixi'] = round($benjin*$yuelilv,2);
		$daishou = $benjin+$moneys[0]['zonglixi'];
		for ($i=0;$i<$yue;$i++) {
			$daishou -= round($money['yuebenjin']+$money['yuelixi'],2);
			if(($i+1) == $yue){
				$money['yuebenjin'] = $benjin-$money['yuebenjin']*($yue-1);
				$money['yuelixi'] = $moneys[0]['zonglixi'] - $money['yuelixi'] * $i;
				$daishou = 0;
			}
			$money['daishoubenxi'] =round($daishou,2);
			$moneys[$i+1] = $money;
		}
		return $moneys;
	}

	private function daoqihuanben($benjin,$yue,$yuelilv){

		$moneys = array();
		$money = array();
		$yuelilv = $yuelilv *0.01;

		$money['benjin'] = $benjin;
		$money['zonglixi'] = round($benjin * $yuelilv * $yue,2);
		$daishou = $benjin+$money['zonglixi'];
		$money['daishou'] = $daishou;		
		$moneys[0] = $money;

		$money = array();
		$money['yuelixi'] = round($benjin * $yuelilv,2);
		for ($i=0; $i<$yue; $i++) {
			$money['yuebenjin'] = round($i == $yue - 1 ? $benjin:0,2);
			$benxi =($money['yuebenjin'] + $money['yuelixi']);
			$daishou -= $benxi;
			if ($daishou < 0){
				$daishou = 0;
			}
			$money['daishoubenxi'] = round($daishou,2);
			$moneys[$i+1] = $money;
		}
		return $moneys;
	}

}