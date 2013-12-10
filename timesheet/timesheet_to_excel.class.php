<?php
class TimesheetToExcel extends ImportCsv{
	private $month_from=array('ledna','února','března','dubna','května','června','července','srpna','září','října','listopadu','prosince',);
	private $month_to=array('1.','2.','3.','4.','5.','6.','7.','8.','9.','10.','11.','12.');
	private $last_date;
	private $row_data=array();
	private $sum_hours=0;

	public function __construct($file_name){
		parent::__construct($file_name);
	}

	/**
	 * update fake courses by real data
	 * @param array $data
	 * @return \TimesheetToExcel
	 */
	protected function updateRow($data){
		$date=str_replace($this->month_from, $this->month_to, $data[1]);
		if($date!==$this->last_date && !empty($this->row_data)){
			$this->printRow();
		}
		$str=$data[6].' ('.$data[5].')';
		if(!$this->row_data){
			$this->row_data['date']=$date;
			$this->row_data['from']=$data[2];
			$this->row_data['to']=$data[3];
			$this->row_data['text']=array($str=>(float)$data[4]);
			$this->row_data['hours']=(float)$data[4];
		}
		else{
			$this->row_data['to']=$data[3];
			if(!isset($this->row_data['text'][$str])){
				$this->row_data['text'][$str]=(float)$data[4];
			}
			else{
				$this->row_data['text'][$str]+=(float)$data[4];
			}
			$this->row_data['hours']+=(float)$data[4];
		}
		$this->last_date=$date;
		return $this;
	}

	private function printRow(){
		$arr=array();
		foreach($this->row_data['text'] as $text => $time){
			if(count($this->row_data['text'])===1){
				$arr[]=$text;
			}
			else{
				$arr[]=$text.' ['.$this->getFloatToTime($time).']';
			}
		}
		echo '<tr>';
		echo '<td>'.$this->row_data['date'].'</td>';
		echo '<td>'.$this->row_data['from'].'</td>';
		echo '<td>'.$this->row_data['to'].'</td>';
		echo '<td>'.implode('<br>',$arr).'</td>';
		echo '<td>'.$this->getFloatToTime($this->row_data['hours']).'</td>';
		echo '</tr>';

		$this->sum_hours+=$this->row_data['hours'];
		$this->row_data=array();
		return $this;
	}

	protected function beforeEachRows(){
		echo '
			<style>
			table{border-collapse: collapse; border: 2px solid black;}
			td{border: 1px solid black; padding: 5px 10px; vertical-align: top;}
			</style>';
		echo '<table>';
		return $this;
	}

	protected function afterEachRows(){
		$this->printRow();
		echo '<tr>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td>'.$this->getFloatToTime($this->sum_hours).'</td>';
		echo '</tr>';
		echo '</table>';
		return $this;
	}

	/**
	 * prevede float na hh:mm
	 * @param float $float
	 * @return string
	 */
	private function getFloatToTime($float){
		list($hours,$min_str)=explode('.',(string)$float);
		$min_float=(float)('0.'.$min_str);
		$minutes=round(60*$min_float);
		return str_pad($hours, 2,'0',STR_PAD_LEFT).':'.(strlen($minutes)<2 ? str_pad($minutes, 2, '0', STR_PAD_LEFT) : substr($minutes, 0, 2));
	}
}