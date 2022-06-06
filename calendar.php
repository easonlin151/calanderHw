<?php 
class Calendar{
	/*
	 * 成員方法：生成日曆的各個邊界值
	 *  1. 計算這個月總天數
		2. 計算這個月第一天與最後一天，各是星期幾
		3. 計算上一個的最後一天，下一個月的第一天各是星期幾
		4. 計算日曆中的第一個日期與最後一個日期
	* */
	function threshold($year,$month){
		$today = strtotime(date("Ymd"));

		//返回當月第一天的時間戳；
		$firstday=mktime(0,0,0,$month,1,$year);
		//返回當月最後一天的時間戳；
		$lastday=strtotime('+1 month -1 day',$firstday); 
		
		//給定月份中包含的天數；
		$days=date("t",$firstday); 
		//獲取當月第一天是星期幾
		$firstday_week=date("N",$firstday);
		//獲取當月最後一天是星期幾
		$lastday_week=date("N",$lastday);
	
		//上一個月最後一天
		$lastmonth_date = strtotime('-1 day', $firstday);  //上一個月最後一天的時間戳
		$lastmonth_lastday = date('d', $lastmonth_date);   //最後一天是上一月的第幾天
	
		//下一個月第一天
		$nextmonth_date = strtotime('+1 day', $lastday);  //上一個月最後一天的時間戳
		$nextmonth_firstday = date('d', $nextmonth_date);  //最後一天是上一月的第幾天
	
		//日曆的第一個日期的時間戳
		if ($firstday_week==7) {
			$firstdate=$firstday;
		}else{
			$firstdate = strtotime('-'. $firstday_week .' day', $firstday);
		}
		//日曆的最後一個日期的時間戳
		if($lastday_week == 6){
			$lastdate = $lastday;
		}elseif($lastday_week == 7){
			$lastdate = strtotime('+6 day', $lastday);
		}else{
			$lastdate = strtotime('+'.(6-$lastday_week).' day', $lastday);
		}
		return array(
			'days'=>$days,
			'$firstday_week'=>$firstday_week,
			'$lastday_week'=>$lastday_week,
			'$lastmonth_lastday'=>$lastmonth_lastday,
			'$firstdate'=>$firstdate,
			'$lastdate'=>$lastdate,
			'year' => $year,
			'month' => $month,
			'today' => $today
		);
	}
	
	//計算日曆的天數
	function caculate($calendar) {
		$days = $calendar['days'];
		$firstday_week = $calendar['$firstday_week']; //本月第一天的星期
		$lastday_week = $calendar['$lastday_week']; //本月最後一天的星期
		$lastmonth_lastday = $calendar['$lastmonth_lastday'];//上個月的最後一天
		$year = $calendar['year'];
		$month = $calendar['month'];
		
		$dates = array();
		if($firstday_week != 7) {
			$lastdays = array();
			$current = $lastmonth_lastday;//上個月的最後一天
			for ($i = 0; $i < $firstday_week; $i++) {
				array_push($lastdays, $current);//向$latsdays數組尾部插入上一個月的日期天數
				$current--;
			}
			$lastdays = array_reverse($lastdays);//將$latsdays數組反序
			foreach ($lastdays as $index => $day) {
				array_push($dates, array('day' => $day, 'tdclass' => ($index ==0 ?'rest':''), 'chktoday' => '','pclass' => 'outter','festivalDay' => ''));
			}
		}
	
		//本月日曆信息
		for ($i = 1; $i <= $days; $i++) {
			$isRest = $this->_checkIsRest($year, $month, $i);
			$isToday = $this->_checkToday($year, $month, $i);
			$fDay = $this->_markFestivalDay($year, $month, $i);
			array_push($dates, array('day' => $i, 'tdclass' => ($isRest ?'rest':''),'chktoday' => ( $isToday ?'dycalendar-target-date':''),'pclass' => '','festivalDay' => $fDay));
		}
	
		//下月日曆信息
		if($lastday_week == 7) {//最後一天是星期日
			$length = 6;
		}
		elseif($lastday_week == 6) {//最後一天是星期六
			$length = 0;
		}else {
			$length = 6 - $lastday_week;
		}
		for ($i = 1; $i <= $length; $i++) {
			array_push($dates, array('day' => $i, 'tdclass' => ($i==$length ?'rest':''), 'chktoday' => '','pclass' => 'outter','festivalDay' => ''));
		}
	
		return $dates;
	}
	//判斷是否是休息天
	private function _checkIsRest($year, $month, $day) {
		$date = mktime(0, 0, 0, $month, $day, $year);  //當前日期的時間戳
		$week = date("N", $date);   //當前日期是星期幾
		return $week == 7 || $week == 6;
	}

	//判斷是否是今天
	private function _checkToday($year, $month, $day) {
		$today = date("Ynj"); //當前日期的時間戳
		
		return ($today == $year.$month.$day) ;
	}

	//增加日期class
	private function _markFestivalDay($year, $month, $day){
		$today = $year."-".$month."-".$day;
		$fDay = date("md",strtotime($today));
		return $fDay;
	}
	

	//畫表格，設置table中的tr與td
	function draw($caculate) {
		$tr=array();
		$length=count($caculate);
		$result=array();
		foreach ($caculate as $index=>$date){
			if ($index%7==0) {//第一列
				$tr=array($date);
			}elseif($index%7==6 || $index==($length-1)){
				array_push($tr,$date);
				array_push($result,$tr);  //添加到返回的數組中
				$tr=array(); //清空數組列表
			}else{
				array_push($tr,$date);
			}
		}
		return $result;
	}

	//文字輸出
	function poetry(){
		$text = array('活出自己的樣子，會有人喜歡你，也會有人討厭你，但重要的是，你會更喜歡自己。',
		'任何事情，決定後便去努力，無論結果是什麼，都只有自己能承擔',
		'離開了就是離開了，再不捨也要逼自己別老是頻頻回首，餘生海角天涯各自幸福。',
		'日子時常是辛苦，因此你才更要為自己而活著。學會深深擁抱自己，因為你是你的。',
		'不是所有的努力都一定有回報，但努力過，就沒有遺憾。',
		'不是所有的真心都一定被看到，但真誠地，就無愧於心。',
		'一份感情會結束，不一定是因為誰不好，而是你們已經無法在那份感情裡得到撫慰。',
		'每場相遇，都有它發生的原因。每場分離，最後也都一定會寫下意義。',
		'最好的默契，並非有人懂你的言外之意，而是有人懂你的欲言又止。',
		'二十以前，跟人不知怎的就熟稔了;二十以後，不知怎的，與許多人漸行漸遠。',
		'生命中真正重要的不是你遭遇了什麼，而是你記住了哪些事，又是如何銘記。',
		'在不能共享沉默的兩個人之間，任何語言都無法使他們的靈魂發生溝通。',
		'如果我們努力變得更好，圍繞著我們的每樣事務也會變得更好。',
		'人人都是月亮，都有不曾向別人展示的暗面。'
	);
		return $text[rand(0,13)];
		
	}

	//判斷月份是否換年
	function switchYear($year, $month){

		switch($month){
			case 1:
				$prevMonth=12;
				$prevYear=$year-1;
				$nextMonth=$month+1;
				$nextYear=$year;
				break;
			case 12:
				$prevMonth=$month-1;
				$prevYear=$year;
				$nextMonth=1;
				$nextYear=$year+1;
				break;
			default:
				$prevMonth=$month-1;
				$prevYear=$year;
				$nextMonth=$month+1;
				$nextYear=$year;
				
		}
		return array(
			'prevMonth'=>$prevMonth,
			'prevYear'=>$prevYear,
			'nextMonth'=>$nextMonth,
			'nextYear'=>$nextYear,
			'year'=>$year,
			'month'=>$month,
			// 'monthFont'=>$year."-".$month."-1"
			'monthFont'=>"{$year}-{$month}-1"

		);
	}

}
?>