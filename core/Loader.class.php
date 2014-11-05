<?php
/**
 * Description of Loader
 *
 * @author Pragodata {@link http://www.pragodata.cz} Vlahovic
 * @since 5.11.2014, 15:27:55
 */
class Loader{
	private $objects=array('core','model','view','controller');

	/*	 * *********************************************************************** */
	/* magic methods */
	/*	 * *********************************************************************** */

	/*	 * *********************************************************************** */
	/* public methods */
	/*	 * *********************************************************************** */
	public function getModel($model_name){
		return $this->getObject('model', $model_name);
	}

	public function getView($view_name){
		return $this->getObject('view', $view_name);
	}

	public function getController($controller_name){
		return $this->getObject('controller', $controller_name);
	}

	/*	 * *********************************************************************** */
	/* protected methods */
	/*	 * *********************************************************************** */

	/*	 * *********************************************************************** */
	/* private methods */
	/*	 * *********************************************************************** */
	private function getObject($object_type, $object_name){
		$return=null;
		if(in_array($object_type, $this->objects)){
			if(empty($this->objects[$object_type][$object_name])){
				$return=$this->loadObject($object_type, $object_name);
				$this->objects[$object_type][$object_name]=$return;
			}
			else{
				$return=$this->objects[$object_type][$object_name];
			}
		}
		else{
			throw new Exception('Unsupported object type "'.$object_type.'".');
		}
		return $return;
	}

	private function loadObject($object_type, $object_name){

	}
}