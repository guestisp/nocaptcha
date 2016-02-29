GUEST NoCaptcha - Prevenzione spam per Laravel
=========

## Come funziona?

"NoCaptcha" e' un sistema antispam semplice ed efficace. Si basa sul concetto
che i robot utilizzati dagli spammer compilano in automatico tutti i campi
presenti all'interno di una form, indipendentemente dalla loro visibilità o meno
a livello di DOM.

Questo pacchetto crea un DIV nascosto con due campi all'interno, un campo
"nocaptcha" ed un campo "nocaptcha_time", entrambi con nomi personalizzabili.

Il "nocaptcha" dovrà rimanere tassativamente vuoto in fase di invio delle form
mentre il "nocaptcha_time" contiene, in forma criptata, il timestamp di apertura
del modulo. Un validatore verifica, una volta inviata la form, che il "nocaptcha"
sia vuoto (e quindi non popolato da un robot) e che il "nocaptcha_time", una
volta decriptato, non indichi un orario inferiore a quello impostato nel
validatore. Se l'orario trascorso dalla visualizzazione della pagina e quello
di invio della form è inferiore alla configurazione del validatore, significa
che la form è stata inviata da un sistema automatizzato e quindi viene marcata
come spam.

## Installazione:

`composer require guestisp/nocaptcha`

Successivamente, inserire la seguente riga nella sezione 'providers' del file
di configurazione di Laravel (/config/app.php)

    'GuestIsp\NoCaptcha\NoCaptchaServiceProvider',

Aggiungere il Facade nella sezione 'aliases' del file di configurazione

    'NoCaptcha' => 'GuestIsp\NoCaptcha\NoCaptchaFacade'

## Utilizzo:

Inserire il div nascosto all'interno delle form richiamando `NoCaptcha::generate(..)`
in maniera analoga alla seguente:

    {!! Form::open('contact') !!}
        ...
        {!! NoCaptcha::generate('nome_campo', 'nome_campo_time') !!}
        ...
    {!! Form::close() !!}

Aggiungere due regole di validazione nel controller preposto alla ricezione della form:

    $rules = array(
        ...
        'nome_campo'      => 'nocaptcha',
        'nome_campo_time' => 'required|nocaptchatime:5'
    );

Il validatore "nocaptchatime" accetta un parametro indicante il numero di secondi
che l'utente deve impiegare per compilare la form. Se impiega meno tempo, l'invio
viene identificato come fraudolento.

## Credits

Alessandro Corbelli

## License

E' tutto mio, guai a voi.
