<?php namespace GuestIsp\NoCaptcha;

use Illuminate\Support\Facades\Facade;

class NoCaptchaFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'nocaptcha'; }

}
