<?php


interface LatitudeFinance_Payment_Method_Interface {

	/**
	 * @return string
	 */
	public function getSnippetUrl();

	/**
	 * @param $amount
	 * @return self
	 */
	public function setAmount( $amount);

	/**
	 * @return string
	 */
	public function getImagesApiUrl();
}
