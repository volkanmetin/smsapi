<?php

/**
 * Laravel 5 SMS Api
 * @license MIT License
 * @author Volkan Metin <ben@volkanmetin.com>
 * @link http://www.volkanmetin.com
 *
*/

namespace Volkanmetin\Smsapi\Facades;

use Illuminate\Support\Facades\Facade;

class Smsapi extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'smsapi';
	}
	
}