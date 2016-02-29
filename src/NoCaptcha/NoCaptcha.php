<?php namespace GuestIsp\NoCaptcha;

use Crypt;

class NoCaptcha {

    protected $disabled = false;

    /**
     * Attiva validazione nocaptcha
     */
    public function enable()
    {
        $this->disabled = false;
    }

    /**
     * Disattiva validazione nocaptcha
     */
    public function disable()
    {
        $this->disabled = true;
    }

    /**
     * Genera il nocaptcha e ritorna la form html
     * @param  string $nocaptcha_name
     * @param  string $nocaptcha_time
     * @return string
     */
    public function generate($nocaptcha_name, $nocaptcha_time)
    {
        // Encrypt the current time
        $nocaptcha_time_encrypted = $this->getEncryptedTime();

        $html = '<div id="' . $nocaptcha_name . '_wrap" style="display:none;">' . "\r\n" .
                    '<input name="' . $nocaptcha_name . '" type="text" value="" id="' . $nocaptcha_name . '"/>' . "\r\n" .
                    '<input name="' . $nocaptcha_time . '" type="text" value="' . $nocaptcha_time_encrypted . '"/>' . "\r\n" .
                '</div>';

        return $html;
    }

    /**
    * Validatore per il nocaptcha come vuoto
    *
    * @param  string $attribute
    * @param  mixed $value
    * @param  array $parameters
    * @return boolean
    */
    public function validateNoCaptcha($attribute, $value, $parameters)
    {
        if ($this->disabled) {
            return true;
        }

        return $value == '';
    }

    /**
     * Validatore per la durata di compilazione form
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @return boolean
     */
    public function validateNoCaptchaTime($attribute, $value, $parameters)
    {
        if ($this->disabled) {
            return true;
        }

        // Decripto l'orario
        $value = $this->decryptTime($value);

        // L'orario attuale deve essere superiore all'orario di creazione form più il timelimit richiesto
        return ( is_numeric($value) && time() > ($value + $parameters[0]) );
    }

    /**
     * Prende l'orario criptato
     * @return string
     */
    public function getEncryptedTime()
    {
        return Crypt::encrypt(time());
    }

    /**
     * Decripta l'orario
     *
     * @param  mixed $time
     * @return string|null
     */
    public function decryptTime($time)
    {
    	try {
            return Crypt::decrypt($time);
    	}
    	catch (\Exception $exception)
        {
            return null;
        }
    }

}
