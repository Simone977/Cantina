<?php

// Recupero il contenuto della richiesta proveniente da Telegram (in JSON) e lo decodifico in un array
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Se la richiesta è vuota, termino lo script
if (!$update) {
    exit;
}

// Telegram API token
const TOKEN = "YOUR TELEGRAM BOT TOKEN";
// Definisco una costante che contiene la tastiera con i comandi.
// è un array di righe, dove ogni riga è un array di bottoni ed ogni bottone è un array composto dalla key "text" e come valore il comando associato.
const KEYBOARD = array("keyboard" =>
    array(
        // Prima riga
        array(
            array("text" => "Orario classi, professori o laboratori")
        ),
        // Seconda riga
        array(
            array("text" => "Lista delle palestre"),
            array("text" => "Orario mensile delle palestre")
        ),
        // Terza riga
        array(
            array("text" => "Planimetria")
        )
    ),
    "resize_keyboard" => true
);

// Raccolgo alcuni dati degli utenti dal json che telegram invia
$chatId = $update["message"]["chat"]["id"];
$messageId = $update["message"]["message_id"];
$firstName = $update["message"]["from"]["first_name"];
$lastName = $update["message"]["from"]["last_name"];
$username = $update["message"]["from"]["username"];

// Recupero il testo del messaggio, rimuovo gli spazi all'inizio e alla fine della stringa e lo trasformo tutto minuscolo
$text = $update["message"]["text"];
$text = trim($text);
$text = strtolower($text);

// Mi connetto al database
$pdo = dbConnect();

saveUser();

// Controllo i comandi
// strpos dice la posizione della stringa che passo come secondo argomento dentro la prima stringa, se non la trova torna false.
if (strpos($text, "/start") !== false || strpos($text, "ciao") !== false) {
    $message = "Ciao $firstName! Per iniziare a chattare utilizza la tastiera personalizzata qui sotto che contiene i comandi disponibili attualmente!";
    sendMessage($chatId, $message);
} elseif (strpos($text, "lista delle palestre") !== false) {
    $message = "Lista delle palestre:\r\n-Palestra 1: Laboratori di informatica (grande)\r\n-Palestra 2: Laboratori di informatica (piccola)\r\n-Palestra 3: Sotto laboratori di fisica (grande)\r\n-Palestra 4: Sotto laboratori di fisica (piccola)\r\n-Palestra 5: Satellite (grande)\r\n-Palestra 6: Satellite (piccola)";
    sendMessage($chatId, $message);
} elseif (strpos($text, "orario mensile delle palestre") !== false) {
    //sendMessage($chatId, "Ancora non disponibile!");
    sendPhoto($chatId, "link al file immagine dell'orario");
} elseif (strpos($text, "orario") !== false) {
    $message = "Clicca su 'Apri' per scaricare il PDF con tutti gli orari. Da quest'anno la ricerca in base a classe o docente non è più disponibile in quanto la scuola ha deciso di creare un orario unico senza professori.";
    $url = "http://www.iiscastelli.gov.it/documents/Varie/Orario%20Classi%20diurno%20sito.pdf";
    sendMessage(
        $chatId,
        $message,
        $inlineKeyboard = array("inline_keyboard" =>
            array(
                array(
                    array("text" => "Apri", "url" => $url)
                )
            )
        )
    );

/* Questo era il vecchio algoritmo per recuperare l'orario di una classe, di un prof o di un lab, ma questo orario non è più pubblico
$class = substr($text, 6);
$class = trim($class);

@$url = "http://www.iiscastelli.gov.it/orariotd/";
$html = file_get_contents($url);
$dom = new DOMDocument;
@$dom->loadHTML($html);
$links = $dom->getElementsByTagName('a');

$pageUrl = false;

foreach ($links as $link) {
    $value = $link->nodeValue;
    $value = trim($value);
    $value = strtolower($value);

    if ($class == $value) {
        $pageUrl = $link->getAttribute('href');
        break;
    }
}

if ($pageUrl == false) {
    $response = "*Nessuna classe, professore o laboratorio trovato!*\r\nIl formato di invio è: per le classi `Orario 4BI`, per i professori `Orario Cognome Nome` e per i laboratori `Orario nomeLaboratorio numeroLaboratorio`.";
    sendMessage($chatId, $response);
} else {
    $class = strtoupper($class);
    $message = "Clicca qui sotto per aprire l'orario di $class";
    $inlineKeyboard = array("inline_keyboard" => array(array(array("text" => "Apri", "url" => "http://www.iiscastelli.gov.it/orariotd/$pageUrl"))));
    sendMessage($chatId, $message, $inlineKeyboard);
}*/
} elseif (strpos($text, "planimetria") !== false) {
    // Recupero l'argomento, cioè il piano
    $planimetria = substr($text, 12);

    if ($planimetria == "rialzato") {
        sendDocument($chatId, "link al pdf del piano rialzato", "Planimetria piano rialzato");
    } elseif ($planimetria == "primo") {
        sendDocument($chatId, "link al pdf del primo piano", "Planimetria piano primo");
    } elseif ($planimetria == "secondo") {
        sendDocument($chatId, "link al pdf del secondo piano", "Planimetria piano secondo");
    } else {
        $message = "*Nessun piano trovato!*\r\nIl formato di invio è `Planimetria piano` dove `piano` deve corrispondere a `rialzato`, `primo` o `secondo`.";
        sendMessage($chatId, $message);
    }
} else {
    $message = "Il messaggio inviato non corrisponde a nessun comando 😢 Per piacere, usa la tastiera personalizzata qui sotto!";
    sendMessage($chatId, $message);
}

$pdo = null;

//FUNZIONI
function dbConnect()
{
    $servername = "ip server";
    $username = "username";
    $password = "password";
    $dbname = "nome database";

    try {
        $dbConnection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    $KEY = "chiave per crittare/decrittare";

    $sql = "SET @key = :key";
    $stmt = $dbConnection->prepare($sql);
    $stmt->bindValue(":key", $KEY);
    $stmt->execute();

    unset($KEY);

    return $dbConnection;
}

// Salvo l'utente
function saveUser($chatId, $firstName, $lastName, $username)
{
    global $conn;

    //Controllo se c'è già un utente con quel chatId
    $sql = "SELECT count(chatId) FROM gdpr_users WHERE chatId = AES_ENCRYPT(:chatId, @key)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':chatId', $chatId);
    $stmt->execute();
    $result = $stmt->fetchColumn();

    // Se il numero di righe ritornate è maggiore di 0 (esiste già un utente), esco dalla funzione
    if ($result > 0) {
        return;
    }

    // Creo la query e la eseguo
    $sql = "INSERT INTO gdpr_users (chatId, firstName, lastName, username)
		VALUES (AES_ENCRYPT(:chatId, @key), AES_ENCRYPT(:firstName, @key), AES_ENCRYPT(:lastName, @key), AES_ENCRYPT(:username, @key))";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':chatId', $chatId);
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
}

// Invia un messaggio di testo
function sendMessage($chatId, $text, $replyMarkup = KEYBOARD)
{
    // Creo l'array che i dati che Telegram vuole per inviare un messaggio
    $array = array("chat_id" => $chatId, "text" => $text, "parse_mode" => "Markdown", "reply_markup" => $replyMarkup);
    // Codifico l'array in JSON
    $jsonArray = json_encode($array);
    // Creo la richiesta e la eseguo
    $ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/sendMessage');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonArray);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
}

// Invia un messaggio con foto
function sendPhoto($chatId, $photo, $replyMarkup = KEYBOARD)
{
    // Creo l'array che i dati che Telegram vuole per inviare un messaggio
    $array = array("chat_id" => $chatId, "photo" => $photo, "reply_markup" => $replyMarkup);
    // Codifico l'array in JSON
    $jsonArray = json_encode($array);
    // Creo al richiesta e la eseguo
    $ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/sendPhoto');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonArray);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
}

// Invia un messaggio con un PDF
function sendDocument($chatId, $document, $caption, $replyMarkup = KEYBOARD)
{
    // Creo l'array che i dati che Telegram vuole per inviare un messaggio
    $array = array("chat_id" => $chatId, "document" => $document, "caption" => $caption, "reply_markup" => $replyMarkup);
    // Codifico l'array in JSON
    $jsonArray = json_encode($array);
    // Creo la richiesta e la eseguo
    $ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/sendDocument');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonArray);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
}
