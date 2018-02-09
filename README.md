GUEST NoCaptcha - Prevenzione spam per Laravel
=========

## Come funziona?

"NoCaptcha" e' un sistema antispam semplice ed efficace. Si basa sul concetto
che i robot utilizzati dagli spammer compilano in automatico tutti i campi
presenti all'interno di una form, indipendentemente dalla loro visibilità o meno
a livello di DOM.

Questo pacchetto crea un DIV nascosto con tre campi all'interno, un campo
"nocaptcha", un campo "nocaptcha_time" ed un campo "nocaptcha_nonce", 
tutti con nomi personalizzabili.

Il "nocaptcha" dovrà rimanere tassativamente vuoto in fase di invio delle form,
il "nocaptcha_time" contiene, in forma criptata, il timestamp di apertura del modulo
mentre il "nocaptcha_nonce" contiene, in forma criptata, una stringa univoca e funzionante
solo 1 singola volta, il cui contenuto sarà verificato dal server.
Un validatore verifica, una volta inviata la form, che il "nocaptcha"
sia vuoto (e quindi non popolato da un robot), che il "nocaptcha_time", una
volta decriptato, non indichi un orario inferiore a quello impostato nel
validatore e che il "nocaptcha_nonce", una volta decriptato, esista sul server
e contenga un valore noto. Se l'orario trascorso dalla visualizzazione della 
pagina e quello di invio della form è inferiore alla configurazione del validatore oppure
il valore contenuto dentro il "nocaptcha_nonce" decriptato non è presente sul server,
significa che la form è stata inviata da un sistema automatizzato o che è stata inviata
più volte e quindi viene marcata come spam.

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

    {!! Form::open(['method'=>'POST']) !!}
        ...
        {!! NoCaptcha::generate('nome_campo', 'nome_campo_time', 'nome_campo_nonce') !!}
        ...
    {!! Form::close() !!}

Aggiungere tre regole di validazione nel controller preposto alla ricezione della form:

    $rules = array(
        ...
        'nome_campo'       => 'nocaptcha',
        'nome_campo_nonce' => 'nocaptchanonce',
        'nome_campo_time'  => 'required|nocaptchatime:5'
    );

Il validatore "nocaptchatime" accetta un parametro indicante il numero di secondi
che l'utente deve impiegare per compilare la form. Se impiega meno tempo, l'invio
viene identificato come fraudolento.

## Credits

Alessandro Corbelli

## License

proprietary, closed-source

## Version
1.5.0