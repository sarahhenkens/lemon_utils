<?php
/**
 * RedirectComponent
 * 
 * This component is used to handle persistent referers acros redirects.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright 2011, Jelle Henkens (jelle.henkens@gmail.com)
 * @author			Jelle Henkens
 * @license			MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');

/**
 * RedirectComponent
 *
 * This component is used to handle persistent referers acros redirects.
 *
 */
class RedirectComponent extends Component {

	/**
	 * Contains the instance of the controller
	 *
	 * @var object
	 * @access private
	 */
	private $Controller = null;

	/**
	 * List of components that this component uses
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Session');

	/**
	 * During the initialization this component will store the referer to
	 * the current page and persists it until this page is visited from a 
	 * different location.
	 *
	 * @param object $controller Controller with components to load
	 * @return void
	 * @access public
	 */
	public function initialize(&$Controller, $settings) {
		$this->_set($settings);
		$this->Controller = $Controller;

		$referer = $this->Controller->referer(null, true);

		if ($referer != $this->Controller->request->here) {
			$key = 'Redirect.' . md5($this->Controller->request);
			$this->Session->write($key, $referer);
		}
	}

	/**
	 * Returns the persisted referer url for the action that this function is 
	 * called from.
	 * 
	 * If no persistent referer is found it will call the referer() function on
	 * the controller. If that referer is empty it will fall back to the referer
	 * given.
	 *
	 * @param mixed $fallback Array format or string of the URL to fall back too.
	 * @param boolean $useRefererFallback Set this to false if you dont want this method to call referer on the controller object
	 * @return string URL to redirect too
	 * @access public
	 */
	public function url($fallback = array(), $useRefererFallback = true) {
		$key = 'Redirect.' . md5($this->Controller->request->here);

		if ($this->Session->check($key)) {
			$redirect = $this->Session->read($key);
			$this->Session->delete($key);

			return $redirect;
		}

		if ($useRefererFallback) {
			return $this->Controller->referer($fallback);
		}

		return $fallback;
	}
}