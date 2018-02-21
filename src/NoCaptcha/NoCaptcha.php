<?php namespace GuestIsp\NoCaptcha;

use Cache;
use Crypt;
use Log;
use Request;

class NoCaptcha {

    protected $disabled    = false;
    protected $nonceLength = 64;

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
     * @param  string $nocaptcha_nonce
     * @return string
     */
    public function generate($nocaptcha_name, $nocaptcha_time, $nocaptcha_nonce)
    {
        // Cripto l'orario attuale
        $nocaptcha_time_encrypted = $this->getEncryptedTime();

        // Cripto un nonce e lo salvo in cache
        $nocaptcha_nonce_encrypted = $this->getEncryptedNonce();

        $html = '<div id="' . $nocaptcha_name . '_container" style="display:none;">' . "\r\n" .
                    '<input name="' . $nocaptcha_name . '" type="text" value="" id="' . $nocaptcha_name . '" autocomplete="off"/>' . "\r\n" .
                    '<input name="' . $nocaptcha_time . '" type="text" value="' . $nocaptcha_time_encrypted . '"/>' . "\r\n" .
                    '<input name="' . $nocaptcha_nonce . '" type="text" value="' . $nocaptcha_nonce_encrypted . '"/>' . "\r\n" .
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
     * Validatore del nonce
     * 
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @return boolean
     */
    public function validateNonce($attribute, $value, $parameters)
    {
        if ($this->disabled) {
            return true;
        }

        $value = $this->decryptNonce($value);

        return (!empty($value) && $value == Request::root());
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
     * Genera un nonce criptato e lo salva in cache
     * @return string
     */
    public function getEncryptedNonce()
    {
        // Genero un nonce (è la chiave in cache)
        $nonce = str_random($this->nonceLength);

        // Salvo in cache il nonce
        Cache::put($nonce, Request::root(), 120);        

        // Ritorno la chiave criptata del nonce
        return Crypt::encrypt($nonce);
    }

    /**
     * Decripta il nonce e ne ritorna la stringa originale
     * @return string
     */
    public function decryptNonce($cryptedNonce)
    {
        // Cripto il nonce (viene passato nella form)
        $decrypt = Crypt::decrypt($cryptedNonce);

        // Ritorno il valore non criptato del nonce o '' nel caso non ci fosse
        return Cache::pull($decrypt, '');
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
