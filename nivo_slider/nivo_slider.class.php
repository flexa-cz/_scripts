<?php
class NivoSlider{
	private $id;
	private $data;
	private $item_links;
	private $item_titles;

	/**
	 * @param integer $id
	 * @param array $data array(array('image'=>'','url'=>'','text'=>''),...)
	 */
	public function __construct($id,$data){
		$this
						->setId($id)
						->setData($data);
	}

	/**
	 * @return string
	 */
	public function printSlideShow(){
		$return='';
		$return.="\r\n<!-- begin of NivoSlider (".$this->id.") -->\r\n";
		$return.='<div id="'.$this->id.'" class="nivoSlider">';
		$return.=$this->generateItems()->getLinksItems();
		$return.="\r\n";
		$return.='</div>';
		$return.=$this->getTitlesItems();
		$return.="\r\n";
		$return.="\r\n<!-- end of NivoSlider (".$this->id.") -->\r\n";
		return $return;
	}

	/**
	 * @return string
	 */
	private function getTitlesItems(){
		$return='';
		$return.="\r\n".implode("\r\n\t",$this->item_titles);
		return $return;
	}

	/**
	 * @return string
	 */
	private function getLinksItems(){
		$return='';
		$return.="\r\n\t".implode("\r\n\t",$this->item_links);
		return $return;
	}

	/**
	 * @param array $item
	 * @return \NivoSlider
	 * @throws Exception
	 */
	private function generateItems(){
		$this->item_links=array();
		$this->item_titles=array();
		foreach($this->data as $item){
			if(is_array($item)){
				if(empty($item['image'])){
					throw new Exception('Item array must contain "image" item!');
				}
				elseif(empty($item['url'])){
					throw new Exception('Item array must contain "url" item!');
				}
				elseif(!isset($item['text'])){
					throw new Exception('Item array must contain "text" item!');
				}
				else{
					$id_arr=explode('/',$item['image']);
					$id=str_replace(array('-','.','#','&','   ','  ',' '),'_',end($id_arr));
					$this->item_links[]='<a href="'.$item['url'].'"><img src="'.str_replace(array('//',$_SERVER['SERVER_NAME']),false,$item['image']).'" title="#'.$id.'" /></a>';
					$this->item_titles[]='<div id="'.$id.'" class="nivo-html-caption">'.$item['text'].'</div>';
				}
			}
			else{
				throw new Exception('Data for item must be array type!');
			}
		}
		return $this;
	}

	/**
	 * @param integer $id
	 * @return \NivoSlider
	 * @throws Exception
	 */
	private function setId($id){
		$id=(string)$id;
		if($id){
			$this->id=$id;
		}
		else{
			throw new Exception('Parameter id must be string type!');
		}
		return $this;
	}

	/**
	 * @param array $data array(array('image'=>'','url'=>'','text'=>''),...)
	 * @return \NivoSlider
	 * @throws Exception
	 */
	private function setData($data){
		if(is_array($data)){
			$this->data=$data;
		}
		else{
			throw new Exception('Parameter data must be array type!');
		}
		return $this;
	}
}