<?php
/**
 * PHP API for js slideshow
 * {@link http://jquery.malsup.com/cycle2/}
 */
class Cycle2{
	private $id;
	private $data;
	private $item_links;
	private $options=array(
			'allow-wrap'=>false,
			'auto-height'=>false,
			'autoSelector'=>false,
			'caption'=>false,
			'caption-template'=>false,
			'center-horz'=>'true',
			'center-vert'=>'true',
			'delay'=>false,
			'disabled-class'=>false,
			'easing'=>false,
			'fx'=>'scrollHorz',// scrollHorz, flipHorz, tileBlind, fade, fadeout, none
			'hide-non-active'=>false,
			'loader'=>false,
			'log'=>'false',
			'loop'=>false,
			'manual-speed'=>false,
			'manual-trump'=>false,
			'next'=>false,
			'next-event'=>false,
			'overlay'=>false,
			'overlay-template'=>false,
			'pager'=>false,
			'pager-active-class'=>false,
			'pager-event'=>false,
			'pager-template'=>false,
			'pause-on-hover'=>'true',
			'paused'=>false,
			'prev'=>false,
			'prev-event'=>false,
			'progressive'=>false,
			'random'=>false,
			'reverse'=>false,
			'slide-active-class'=>false,
			'slide-css'=>false,
			'slide-class'=>false,
			'slides'=>false,
			'speed'=>600,
			'starting-slide'=>false,
			'swipe'=>false,
			'sync'=>false,
			'timeout'=>5000,
			'tmpl-regex'=>false,
			'update-view'=>false,
	);
	private $default_pager=false;

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
	 * @param string $name
	 * @param string $value
	 * @return \Cycle2
	 * @throws Exception
	 */
	public function setOption($name, $value, $forse=true){
		if(isset($this->options[$name]) && (!$this->options[$name] || $forse)){
			$this->options[$name]=$value;
		}
		elseif(!isset($this->options[$name])){
			echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$this->options</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
			var_export($this->options);
			echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
			throw new Exception('Not allowed option "'.$name.'"!');
		}
		return $this;
	}

	/**
	 * @param boolean $default_pager
	 * @return \Cycle2
	 */
	public function setDefaultPager($default_pager){
		$this->default_pager=(empty($default_pager) ? false : true);
		return $this;
	}

	/**
	 * @return string
	 */
	public function printSlideShow(){
		$this->generateItems();
		$return='';
		$return.="\r\n<!-- begin of Cycle2 slideshow (".$this->id.") -->";
		$return.="\r\n".'<div id="'.$this->id.'">';
		$return.="\r\n\t".'<div class="cycle-slideshow" '.$this->getOptionsString().'>';
		$return.=$this->getTags();
		$return.=$this->getLinksItems();
		$return.="\r\n\t".'</div>';
		$return.="\r\n".'</div>';
		$return.="\r\n<!-- end of Cycle2 slideshow (".$this->id.") -->\r\n";
		return $return;
	}

	/**
	 * @return string
	 */
	private function getTags(){
		$arr=array();
		if($this->default_pager){
			$arr[]='<div class="cycle-pager"></div>';
		}
		return (!empty($arr) ? "\n\r\t\t".implode("\n\r\t\t", $arr) : false);
	}

	/**
	 * @return string
	 */
	private function getOptionsString(){
		$arr=array();
		foreach($this->options as $name => $value){
			if($value!==false){
				$arr[]='data-cycle-'.$name.'="'.$value.'"';
			}
		}
		return (!empty($arr) ? implode(' ',$arr) : false);
	}

	/**
	 * @return string
	 */
	private function getLinksItems(){
		return (!empty($this->item_links) ? "\r\n\t\t".implode("\r\n\t\t",$this->item_links) : false);
	}

	/**
	 * @param array $item
	 * @return \NivoSlider
	 * @throws Exception
	 */
	private function generateItems(){
		$this->item_links=array();
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
					if($item['url']){
						$this->setOption('slides', '> a', false);
						$this->item_links[]='<a href="'.$item['url'].'"><img src="'.str_replace(array('//',$_SERVER['SERVER_NAME']),false,$item['image']).'" /></a>';
					}
					else{
						$this->item_links[]='<img src="'.str_replace(array('//',$_SERVER['SERVER_NAME']),false,$item['image']).'" />';
					}
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