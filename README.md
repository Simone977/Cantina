# Itis Castelli Bot

## Informazioni sulle API

Tutta la documentazione relativa alle API di Telegram potete trovarla [**qui**](https://core.telegram.org/bots/api).

## File contenuti

- `bot.php`: viene eseguito da un cronjob ogni ora. Recupera le circolari dal
    sito della scuola e, quelle nuove, le invia nel
    [canale Telegram](t.me/ItisCastelli).

- `sendAll.php`: viene eseguito quando è direttamente chiamato con una
    richiesta GET (anche digitando il suo indirizzo nel browser). Recuperà tutti
    gli utenti dal database ed invia a ciascuno il messaggio.

- `webhook.php`: viene chiamato da Telegram per ogni messaggio. Analizza il
    contenuto della richiesta ed invia la risposta di conseguenza. Salva anche
    gli utenti nel database crittando i dati.

In realtà, in `webhook.php` veniva gestito anche il GDPR. Ad ogni richiesta,
veniva controllato se l'utente avesse consentito al salvaggio dei suoi dati
(**id**, **firstname**, **lastname** e **username**), altrimenti avrebbe dovuto
farlo per poter utilizzare il BOT.  
Ho deciso di rimuovere questa parte e salvare direttamente i dati, perchè non si
tratta di profilazione e non sono strettamente personali. Utilizzavo solo
l'**id** e il **firstname** per inviare i messaggi broadcast a tutti gli utenti
quando ne avevo bisogno, visto che sono gli unici campi certi che Telegram
fornisce (**lastname** e **username** potrebbero essere vuoti).

## Cosa si potrebbe fare

Stavo pensando a qualche modifica da apportare al progetto:

- si potrebbe cambiare linguaggio di programmazione e passare a Python oppure
    Javascript (Node.js). Siete liberissimi di farlo, magari creando un branch
    apposito.

- sarebbe interessante integrare a Github qualche strumento per il deploy
    automatico come [Zeit](zeit.co) (ottimo per Python e Node.js) o
    [Heroku](heroku.com) (solo per PHP, ma dopo mezz'ora di inattività ci
    metterà qualche secondo a rispondere alle richieste). Sostanzialmente, ad
    ogni merge in **master**, il codice viene preso e reso subito disponibile
    online.  
    Il problema, a questo punto, sarebbe l'assenza di un database visto che quei
    servizi offrono solo una piattaforma per far girare un server e non un
    intero ambiente personalizzabile.

## Come contribuire

Usando GitHub è sufficiente "forkare" la repository con l'apposito pulsante in
alto questa pagina, in modo da copiarla sul vostro profilo personale.  
Dopodichè potrete clonarla sul PC e lavorarci liberamente. Solitamente uso
[**GIT FLOW**](https://octodex.github.com/images/yaktocat.png) come metodo di
sviluppo basato su GIT, quindi creo un branch per ogni passaggio, come mostrato
in figura.
![Branch model](https://www.geeknews.it/wp-content/uploads/2015/08/git-workflow-release-cycle-4maintenance.png)

Una volta apportate le modifiche alla vostra copia, potrete eseguire commit sul
**branch develop**, pusharli sul vostro repository remoto e creare una **Pull
Request** per unire i vostri cambiamenti con quelli del repository originale
(quello che avete forkato).  
Se tutto andrà bene, mergerò personalmente i commit nel repository principale.
