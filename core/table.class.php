<?php
class Table{
	private $table;

	public function __construct($rows, $header=false){
		if(is_array($rows) && count($rows)){
			$this->table.=_N.'<table>';
			foreach($rows as $row){
				$this->table.=_N_T.'<tr>';
				foreach($row as $val){
					$this->table.=_N_T_T.'<td>'.$val.'</td>';
				}
				$this->table.=_N_T.'</tr>';
			}
			$this->table.=_N.'</table>';
		}
	}

	public function __toString()
	{
		return $this->table;
	}
}