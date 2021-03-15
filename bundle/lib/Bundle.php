<?php

namespace App\Bundle\SystemsSelectionV2;

class Bundle extends \TAO\Bundle
{
	public function routes()
	{
		return [
			'{^/systems-selection-api/load-json/$}' => array('action' => 'loadJSON', 'controller' => 'Systems'),
			'{^/systems-selection-api/submit-form/$}' => array('action' => 'submitForm', 'controller' => 'Systems'),
			'{^/systems-selection-api/load-systems/$}' => array('action' => 'loadSystems', 'controller' => 'Systems'),
			'{^/systems-selection-api/get-code/$}' => array('action' => 'getCode', 'controller' => 'Systems'),
			'{^/systems-selection-api/verify-code/$}' => array('action' => 'verifyCode', 'controller' => 'Systems'),
		];
	}
}
