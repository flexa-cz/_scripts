<?php
/**
 * resi html stranu a jeji nezbytne soucasti...
 */
class site{
	private $site=false;
	private $content=false;
	private $title=false;
	private $header=array();

	/* ************************************************************************ */
	/* magic methods																														*/
	/* ************************************************************************ */
	public function __construct($title=false)
	{
		$this->setTitle($title);
	}

	public function __toString()
	{
		return	$this->header().
						$this->site.
						report::getInstance()->getReport().
						$this->content.
						$this->footer();
	}

	/* ************************************************************************ */
	/* public methods																														*/
	/* ************************************************************************ */
	/**
	 * pripoji dodany retezec k obsahu
	 *
	 * @param type $content
	 *
	 * @since 28.11.11 10:34
	 * @author Vlahovic
	 */
	final public function addContent($content){
		$this->content.=$content;
		return $this;
	}

	final public function addHeader($header){
		$this->header[]=$header;
		return $this;
	}

	final public function setTitle($title){
		$this->title=$title;
		return $this;
	}

	/* ************************************************************************ */
	/* private methods																													*/
	/* ************************************************************************ */
	/**
	 * html hlavicka
	 *
	 * @param string $title [optional]
	 * @return string
	 *
	 * @since 28.11.11 10:30
	 * @author Vlahovic
	 */
	final private function header(){
		$r='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		$r.=_N.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">';
		$r.=_N.'<head>';
		$r.=_N_T.'<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
		$r.=_N_T.'<meta http-equiv="Content-language" content="cs" />';
		$r.=_N_T.'<meta http-equiv="imagetoolbar" content="no" />';
		$r.=_N_T.'<meta http-equiv="cache-control" content="cache" />';
		$r.=($this->title ? _N_T.'<title>'.$this->title.'</title>' : false);
		$r.=_N_T.'<link rel="stylesheet" type="text/css" href="../style/style.css" title="style" media="screen" />';
		if(!empty($this->header)){
			$r.=_N_T.implode(_N_T,$this->header);
		}
		$r.=_N.'</head>';
		$r.=_N.'<body>';
		return $r;
	}

	/**
	 * html paticka
	 *
	 * @since 28.11.11 10:30
	 * @author Vlahovic
	 * @return string
	 */
	final private function footer(){
		$r=debuger::get_panel();
		$r.=_N.'</body>';
		$r.=_N.'</html>';
		return $r;
	}
}

