<?php
class SazkaStatistics{
	private $core;

	public function __construct($core)
	{
		$this->core=$core;
	}

	public function __toString()
	{
		$return=false;
		$return.=_N.'<h1>Statistiky</h1>';
		$return.=$this->getSummary();

		return $return;
	}

	private function getSummary(){
		$return=false;
		$query="SELECT
			(SELECT COUNT(*) FROM `addresses`) AS `addresses_count`,
			(SELECT COUNT(*) FROM `scores_resume`) AS `draw_count`,
			(SELECT COUNT(*) FROM `scores`) AS `scores_count`,
			(SELECT MIN(`score_date`) FROM `addresses`) AS `from`,
			(SELECT MAX(`score_date`) FROM `addresses`) AS `to`,
			(SELECT (SELECT COUNT(*) FROM `scores_resume` WHERE `tah`!='šance') - (SELECT COUNT(*) FROM (SELECT DISTINCT(`numbers`) FROM `scores_resume` WHERE `tah`!='šance') AS `same_draw`)) AS `count_of_same_draw_at_first_and_second_draw`,
			(SELECT GROUP_CONCAT(DISTINCT CONCAT(`score`,' (', (SELECT COUNT(*) FROM `scores` AS `sss` WHERE `sss`.`score`=`scores`.`score` AND  `tah`!='šance'), ')') ORDER BY (SELECT COUNT(*) FROM `scores` AS `ss` WHERE `ss`.`score`=`scores`.`score` AND  `tah`!='šance') ASC SEPARATOR ', ') FROM `scores` WHERE `tah`!='šance') AS `list_of_count_numbers_asc_at_first_and_second_draw`
			";

		$return.=_N.'<h2>Shrnutí</h2>';
		$return.=_N.'<table>';
		foreach($this->core->db->query($query)->getRows() AS $row){
			foreach($row as $i => $v){
			$return.=_N_T.'<tr>';
			$return.=_N_T_T.'<td>'.$i.'</td>';
			$return.=_N_T_T.'<td>'.$v.'</td>';
			$return.=_N_T.'</tr>';
			}
		}
		$return.=_N.'</table>';

		return $return;
	}
}