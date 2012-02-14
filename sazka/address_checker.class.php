<?php
/**
 * prochazi web a zjistuje adresy na vysledky s jednotlivymi tahy
 */
class address_checker{
	private $source_address='http://www.sportka-aktualnivysledky.cz/historie.php?pageno=';
	private $max_loops=3;
	private $html=false;
	private $domain_info=array();
	private $real_url=false;
	private $core;
	private $http_codes;
	private $return;
	private $addr_already_exists;

	public final function __construct($core){
		$this->core=$core;
		// load the HTTP codes
		$this->http_codes = parse_ini_file(_ROOT."sazka/http_protocol_messages.ini");
		set_time_limit(0);
		$this->setAddrAlreadyExists();
	}

	public function __toString()
	{
		return $this->return;
	}

	public final function check_addresses(){
		$this->return.= '<h1>check addresses</h1>';
		for($x=$this->max_loops;$x>=1; $x--){
			$addr=$this->source_address.$x;
			$this->getHtml($addr);

			$this->return.= '<h2>'.$x.'. loop</h2>';
			$this->return.= '<p>';

			// info o spojeni
			$info= 'http code <strong>'.$this->domain_info['http_code'].'</strong><br />';
			$info.= 'response <strong>'.$this->domain_info['response'].'</strong>';

			// nejsou data
			if (empty($this->html)) {
					$this->return.= '<strong>no html</strong> for address <a href="'.$addr.'">'.$addr.'</a><br />';
					$this->return.=$info;
			}
			// zpracuje stranu
			else {
					$this->return.= 'html for address <a href="'.$addr.'">'.$addr.'</a> <strong>is ready</strong><br />';
					$this->return.=$info;
					$host = parse_url($this->real_url, PHP_URL_HOST);
					$ip = @gethostbyname($host);
					$this->domain_info['host']=$host;
					$this->domain_info['real_url']=$this->real_url;
					$this->domain_info['ip']=$ip;

					// zpracovani html
					$dom=str_get_html($this->html);
					$this->return.= _N.'<table>';
					foreach($dom->find('div[id=content] table') as $table){
						foreach($table->find('a') as $link){
							if(strlen($link->plaintext) > 3){
								$this->return.= _N.'<tr>';
								$score_page_addr=$link->getAttribute('href');
								$this->return.= _N_T.'<td><a href="'.$score_page_addr.'">'.$score_page_addr.'</a></td>';

								// zjisti datum tahu
								$d=$m=$y=false;
								list($d,$m,$y)=explode('.',end(explode('/',$score_page_addr)));
								$date=$y.'-'.str_pad($m,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
								$w=date("W", mktime(0, 0, 0, $m, $d, $y));
								$this->return.= _N_T.'<td>'.$date.'</td>';

								if(empty($this->addr_already_exists) || !in_array($date, $this->addr_already_exists)){
									// ulozi do db
									$query="INSERT INTO `addresses` (
										`address`,
										`score_date`,
										`created_datetime`,
										`year`,
										`week`,
										`first_second`
										) VALUE(
										'".$score_page_addr."',
										'".$date."',
										NOW(),
										'".$y."',
										'".$w."',
										IF((SELECT 1 FROM `addresses` AS a WHERE a.`year`='".$y."' AND a.`week`='".$w."' LIMIT 1)>0,'first','second')
										)";
									$res=$this->core->db->query($query)->getResult();
									$this->return.= _N_T.'<td>'.($res ? 'ok' : 'bad').'</td>';
								}
								else{
									$this->return.= _N_T.'<td>&mdash;</td>';
								}
								$this->return.= _N.'</tr>';
							};
						}
						// na strance je jen jedna tabulka
						break;
					}
					$this->return.= _N.'</table>';
			}


			$this->return.= '</p>';
			$this->return.= '<hr />';

		}
	}

	/**
	 * najde a ulozi vysledky jednotlivych tahu
	 */
	public final function check_scores(){
		$query="SELECT `id`,`address` FROM `addresses` WHERE `score_saved`=0";
		$this->core->db->query($query);
		$x=1;
		$this->return.=_N.'<h1>check scores</h1>';
		$this->return.=_N.'<table>';
		foreach($this->core->db->getRows() as $row){
			$this->return.=_N_T.'<tr>';
			$this->return.=_N_T_T.'<td>'.$x.'</td>';
			$this->return.=_N_T_T.'<td><a href="'.$row['address'].'">'.$row['address'].'</a></td>';

			// nacte html
			$this->getHtml($row['address']);

			if($this->html){
				$this->return.=_N_T_T.'<td>html is ready</td>';
				// zpracovani html
				$dom=str_get_html($this->html);
				foreach($dom->find('div[id=left] div[class=box] ul') as $ul){
					$queries_scores=array();
					$queries_scores_resume=array();
					foreach($ul->find('li') as $li){
						$text=$li->plaintext;
						$tah=false;
						if(strpos($text,'První tah')===0){
							$tah='první';
						}
						elseif(strpos($text,'Druhý tah')===0){
							$tah='druhý';
						}
						elseif(strpos($text,'Šance')===0){
							$tah='šance';
						}
						else{
							continue;
						}

						$this->return.=_N_T_T.'<td>'.$tah.'</td>';
						$cisla=array();
						foreach($li->find('div') as $div){
							$class=$div->getAttribute('class');
							if($class=='cislo' || $class=='dodatkove'){
								$cislo=$div->plaintext;
								// neuklada tahy bez cisel... asi zmena hry
								if($cislo || $cislo===0 || $cislo==='0'){
									$cisla[]=$cislo;
									$queries_scores[]=_N."(".intval($cislo).",".$row['id'].",'".$tah."','".$class."')";
								}
							}
						}
						if(count($cisla)){
							if($tah!='šance'){
								asort($cisla);
							}
							$queries_scores_resume[]=_N."(".$row['id'].",'".$tah."','".implode(', ',$cisla)."')";
						}
						$this->return.=_N_T_T.'<td>'.implode(', ',$cisla).'</td>';
					}

					// transakce
					$res=$this->core->db->query("START TRANSACTION")->getResult();
					if($res){
						$res_arr=array();
						// ulozi vysledky tahu
						$query=_N."INSERT INTO `scores` (`score`,`id_address`,`tah`,`type`) VALUES ".implode(',',$queries_scores);
						$res=$this->core->db->query($query)->getResult();
						$this->return.=_N_T_T.'<td>'.($res ? 'ok' : 'bad').'</td>';
						$res_arr[]=$res;


						// updatne info o tom, ze tato adresa uz byla zpracovana
						if($res){
							// ulozi sumare vysledku tahu
							$query=_N."INSERT INTO `scores_resume` (`id_address`,`tah`,`numbers`) VALUES ".implode(',',$queries_scores_resume);
							$res=$this->core->db->query($query)->getResult();
							$this->return.=_N_T_T.'<td>'.($res ? 'ok' : 'bad').'</td>';
							$res_arr[]=$res;

							if($res){
								$query="UPDATE `addresses` SET `score_saved`=1 WHERE `id`=".$row['id'];
								$res=$this->core->db->query($query)->getResult();
								$this->return.=_N_T_T.'<td>'.($res ? 'ok' : 'bad').'</td>';
								$res_arr[]=$res;
							}
							else{
								$this->return.=_N_T_T.'<td>&mdash;</td>';
							}
						}
						else{
							$this->return.=_N_T_T.'<td>&mdash;</td>';
						}
						// ukonceni transakce
						if(in_array(false, $res_arr)){
							$res=$this->core->db->query("ROLLBACK");
						}
						else{
							$res=$this->core->db->query("COMMIT");
						}
					}
					else{
						$this->return.=_N_T_T.'<td colspan="3">cant start transaction</td>';
					}
				}
			}
			else{
				$this->return.=_N_T_T.'<td>html isnt ready</td>';
			}

			$this->return.=_N_T.'</tr>';
			$x++;
		}
		$this->return.=_N.'</table>';
	}

	/**
	 * nacte retezec s html dane strany
	 */
	private function getHtml($addr){
		$this->html=false;
		$this->domain_info=array();
		// pomoci curl zjisti vse...
		$ch = curl_init(); // create cURL handle (ch)
		if (!$ch) {
				die("Couldn't initialize a cURL handle");
		}
		// set some cURL options
		curl_setopt($ch, CURLOPT_URL,            $addr);
		curl_setopt($ch, CURLOPT_HEADER,         0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,        60);

		// execute
		$this->html = curl_exec($ch);
		$info = curl_getinfo($ch);

		// informace o spojeni
		if (empty($info['http_code'])) {
			$this->domain_info['http_code']='no http code';
			$this->domain_info['response']='no response';
		} else {
			$this->domain_info['http_code']=$info['http_code'];
			$this->domain_info['response']=$this->http_codes[$info['http_code']];
		}
		$this->real_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch); // close cURL handler
	}

	private function setAddrAlreadyExists(){
		$query="SELECT `score_date` FROM `addresses`";
		foreach($this->core->db->query($query)->getRows() as $row){
			$this->addr_already_exists[]=$row['score_date'];
		}
	}
}