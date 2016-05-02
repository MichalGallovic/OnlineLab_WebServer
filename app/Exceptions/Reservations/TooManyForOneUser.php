<?php

namespace App\Exceptions\Reservations;

/**
* Reservation exception
* Raised when more then max reservations for a user
* was exceeded
*/
class TooManyForOneUser extends \Exception
{
	
}