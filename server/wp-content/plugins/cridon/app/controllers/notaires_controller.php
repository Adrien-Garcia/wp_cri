<?php

/**
 * Class NotairesController
 * @author Etech
 * @contributor Joelio
 */
class NotairesController extends BasePublicController
{

	/**
	 * @var mixed
	 */
	public $current_user;

	/**
	 * Constructor 
	 */
	public function __construct()
	{
		global $current_user;

		$this->current_user = $current_user;

		parent::__construct();
	}
}