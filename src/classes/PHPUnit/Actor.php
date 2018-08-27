<?php

namespace WPAssure\PHPUnit;

use WPAssure\Exception;

class Actor {

	/**
	 * Facebook WebDrive instance
	 *
	 * @access private
	 * @var \Facebook\WebDriver\Remote\RemoteWebDriver
	 */
	protected $_webdriver = null;

	/**
	 * Test case instance.
	 *
	 * @access protected
	 * @var \PHPUnit\Framework\TestCase
	 */
	protected $_test = null;

	/**
	 * Sets a new instance of a web driver.
	 *
	 * @access public
	 * @param \Facebook\WebDriver\Remote\RemoteWebDriver $webdriver A web driver instance.
	 */
	public function setWebDriver( $webdriver ) {
		$this->_webdriver = $webdriver;
	}

	/**
	 * Returns a web driver instance associated with the actor.
	 *
	 * @access public
	 * @throws \WPAssure\Exception if a web driver is not assigned.
	 * @return \Facebook\WebDriver\Remote\RemoteWebDriver An instance of a web driver.
	 */
	public function getWebDriver() {
		if ( ! $this->_webdriver ) {
			throw new Exception( 'WebDriver is not provided.' );
		}

		return $this->_webdriver;
	}

	/**
	 * Sets a new instance of PHPUnit test case.
	 *
	 * @access public
	 * @param \PHPUnit\Framework\TestCase $test A test case instance.
	 */
	public function setTest( $test ) {
		$this->_test = $test;
	}

	/**
	 * Returns an instance of a test case associated with the actor.
	 *
	 * @access public
	 * @throws \WPAssure\Exception if a test case is not assigned.
	 * @return \PHPUnit\Framework\TestCase An instance of a test case.
	 */
	public function getTest() {
		if ( ! $this->_test ) {
			throw new Exception( 'Test case is not provided.' );
		}

		return $this->_test;
	}

	/**
	 * Accepts the current native popup window created by window.alert, window.confirm,
	 * window.prompt fucntions.
	 *
	 * @access public
	 */
	public function acceptPopup() {
		$this->getWebDriver()->switchTo()->alert()->accept();
		Log::instance()->write( 'Accepted the current popup.', 1 );
	}

	/**
	 * Dismisses the current native popup window created by window.alert, window.confirm,
	 * window.prompt fucntions.
	 *
	 * @access public
	 */
	public function cancelPopup() {
		$this->getWebDriver()->switchTo()->alert()->dismiss();
		Log::instance()->write( 'Dismissed the current popup.', 1 );
	}

	/**
	 * Takes a screenshot of the viewport.
	 *
	 * @access public
	 * @param string $name A filename without extension.
	 */
	public function takeScreenshot( $name = null ) {
		if ( empty( $name ) ) {
			$name = uniqid( date( 'Y-m-d_H-i-s_' ) );
		}

		$filename = $name . '.jpg';
		$this->getWebDriver()->takeScreenshot( $filename );
		Log::instance()->write( 'Screenshot saved to ' . $filename, 1 );
	}

	/**
	 * Moves back to the previous page in the history.
	 *
	 * @access public
	 */
	public function moveBack() {
		$webdriver = $this->getWebDriver();
		$webdriver->navigate()->back();
		Log::instance()->write( 'Back to ' . $webdriver->getCurrentURL(), 1 );
	}

	/**
	 * Moves forward to the next page in the history.
	 *
	 * @access public
	 */
	public function moveForward() {
		$webdriver = $this->getWebDriver();
		$webdriver->navigate()->forward();
		Log::instance()->write( 'Forward to ' . $webdriver->getCurrentURL(), 1 );
	}

	/**
	 * Refreshes the current page.
	 *
	 * @access public
	 */
	public function refresh() {
		$this->getWebDriver()->navigate()->refresh();
		Log::instance()->write( 'Refreshed the current page', 1 );
	}

	/**
	 * Navigates to a new URL.
	 *
	 * @access public
	 * @param string $url A new URl.
	 */
	public function moveTo( $url ) {
		$webdriver = $this->getWebDriver();
		$webdriver->navigate()->to( $url );
		Log::instance()->write( 'Navigate to ' . $webdriver->getCurrentURL(), 1 );
	}

}