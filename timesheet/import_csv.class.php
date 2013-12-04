<?php
class ImportCsv{
	private $file_name;
	private $separator=',';
	private $value_surround='"';
	private $null_value='';
	private $first_row_burn=true;
	private $import_start_time;
	private $import_end_time;

	private $row=0;
	private $file_content;
	private $each_rows_count;

	public function __construct($file_name){
		$this->setFileName($file_name)->setFileContent();
	}

	public function import(){
		$this
						->setImportStartTime()
						->beforeEachRows()
						->eachRows()
						->afterEachRows()
						->setImportEndTime()
						->printImportTime()
						;
		return $this;

	}

	protected function setFileName($file_name){
		if($file_name && file_exists($file_name)){
			$this->file_name=$file_name;
		}
		else{
			throw new Exception('File "'.$file_name.'" doesnt exitst!');
		}
		return $this;
	}

	protected function setImportStartTime(){
		$this->import_start_time=$this->microtime_float();
		return $this;
	}

	protected function setImportEndTime(){
		$this->import_end_time=$this->microtime_float();
		return $this;
	}

	protected function printImportTime(){
		echo '<p>Import probihal '.round(($this->import_end_time-$this->import_start_time),2).' sekund.<br>Zpracovano bylo '.($this->row-($this->first_row_burn ? 1 : 0)).' zaznamu.</p>';
		return $this;
	}

	protected function setFileContent(){
		$file_content=explode("<br />",nl2br(file_get_contents($this->file_name)));
		foreach($file_content as $index => $value){
			$trim_value=trim($value);
			if(!empty($trim_value)){
				$this->file_content[$index]=$value;
			}
		}
		if (empty($this->file_content)) {
			throw new Exception('File "'.$this->file_name.'" cant read, or hasnt content!');
		}
		return $this;
	}

	protected function beforeEachRows(){
		return $this;
	}

	protected function eachRows(){
		foreach($this->file_content as $data){
			if(!empty($data)){
				$this->row++;
				$data=explode($this->value_surround.$this->separator.$this->value_surround,$data);
				foreach($data as $key => $value){
					$value=trim($value);
					if($this->value_surround){
						$value=trim($value,$this->value_surround);
					}
					$data[$key]=($value==='\\N' ? $this->null_value : $value);
				}

				if($this->row===1 && $this->first_row_burn){
					$this->each_rows_count++;
					continue;
				}

				$this
								->insertRow($data)
								->updateRow($data);
			}
			if($this->each_rows_count && $this->each_rows_count===$this->row){
				break;
			}
		}
		return $this;
	}

	protected function afterEachRows(){
		return $this;
	}

	protected function insertRow($data){
		return $this;
	}

	protected function updateRow($data){
		return $this;
	}


	public function setEachRowsCount($count){
		$this->each_rows_count=(int)$count;
		return $this;
	}

	public function setSeparator($separator){
		$this->separator=$separator;
		return $this;
	}

	public function setValueSurround($value_surround){
		$this->value_surround=$value_surround;
		return $this;
	}


	protected function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	public function setFirstRowBurn($first_row_burn){
		$this->first_row_burn=($first_row_burn ? true : false);
		return $this;
	}
}