<?php

namespace App\Exceptions\Reservations;

/**
* Reservation exception
* Raised when you reserve device before current time
*/
class BeforeNow extends \Exception
{
	
}