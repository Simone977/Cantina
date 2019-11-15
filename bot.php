<?php
// Definisco una costante che contiene il token del bot di Telegram per poter usare le API
define("TOKEN", "YOUR TELEGRAM BOT TOKEN");

// Imposto la lingua per tutto ciò che riguarda l'orario e la data
setlocale(LC_TIME, 'it_IT.utf8');
// Salvo la data attuale
$date = strftime("%e %B %Y");

// Salvo l'url della pagina delle circolari e recupero il suo contenuto HTML
$url = 'http://www.iiscastelli.gov.it/ViewDocN.aspx?ftype=$FTYPE$&fltr=&section=doc&lock=no&shownav=yes&showlog=$SHOWLOG$';
$html = file_get_contents($url);
// Creo un nuovo documento DOM e carico l'HTML appena recuperato
$dom = new DOMDocument;
@$dom->loadHTML($html);
// Recupero tutti i tag <a> presenti nell'HTML
$links = $dom->getElementsByTagName('a');
// Recupero il codice dell'ultima circolare contenuto nel primo tag <a>
$codeLastCircolareSite = intval(substr($links[0]->nodeValue, 5, 3));

// Mi connetto al database e recupero il codice dell'ultima circolare salvata
$conn = dbConnect();
$codeLastCircolareDB = getLastCircolare();
// Se il codice del sito e quello del database coincidono, allora termino lo script perchè ho già inviato tutte le circolari
if ($codeLastCircolareDB == $codeLastCircolareSite) {
    exit;
}

// Parto dalla circolare più vecchia non ancora inviata per arrivare all'ultima
for ($i = $codeLastCircolareSite - $codeLastCircolareDB - 1; $i >= 0; $i--) {
    // Recupero il suo nome
    $title = $links[$i]->nodeValue;
    // Recupero il suo codice
    $code = substr($title, 5, 3);
    // Recupero il suo link e lo concateno al dominio della scuola (perchè il link che recupero è solo la directory senza dominio)
    $pdfUrl = "http://www.iiscastelli.gov.it/" . $links[$i]->getAttribute('href');

    // Invio il messaggio con il PDF
    sendPdfMessage($title, $pdfUrl, $date);
    // Salvo la circolare nel database
    setCircolare($title, $pdfUrl, $code);
    // Aspetto un secondo altrimenti Telegram blocca il bot per tot tempo
    sleep(1);
}

// Chiudo la connessione del database
$conn = null;

// Si connette al db
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

    return $dbConnection;
}

// Salva la circolare nel database
function setCircolare($title, $pdfUrl, $code)
{
    // Recupero la connessione
    global $conn;

    // Creo ed eseguo la query
    $sql = "INSERT INTO `circolari`(`code`, `title`, `url`) VALUES (:code, :title, :pdfUrl)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':pdfUrl', $pdfUrl);
    $stmt->execute();
}

// Recupero il codice dell'ultima circolare
function getLastCircolare()
{
    // Recupero la connessione
    global $conn;

    // Creo ed eseguo la query
    $sql = "SELECT * FROM `circolari` ORDER BY code DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result["code"];
}

// Invia il messaggio con il PDF
function sendPdfMessage($title, $pdfUrl, $date)
{
    // Rimuovo dal titolo l'estensione .pdf
    $name = substr(str_replace(".pdf", "", $title), 9);
    // Creo l'array che i dati che Telegram vuole per inviare un messaggio
    $array = array("chat_id" => "@ItisCastelli", "caption" => "$date\r\n$name", "document" => $pdfUrl, "disable_notification" => true);
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
