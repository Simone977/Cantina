<?php

// Mi connetto al database
$pdo = dbConnect();
const TOKEN = "YOUR TELEGRAM BOT TOKEN";

$sql = "SELECT AES_DECRYPT(chatId, @key) as chatId, AES_DECRYPT(firstName, @key) as firstName FROM gdpr_users";

// Per ogni utente eseguo queste istruzioni
foreach ($pdo->query($sql) as $row) {
    $message = "Il messaggio da inviare";
    // Creo l'array con i dati richiesti da Telegram
    $array = array("chat_id" => $row['chatId'], "text" => $message, "parse_mode" => "Markdown");
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
    print "Inviato a " . $row['chatId'] . "\n";
    // Aspetto 0.1 secondi, il limite di richieste è 30 al secondo
    sleep(0.1);
}

$pdo = null;

function dbConnect()
{
    $servername = "server ip";
    $username = "username";
    $password = "password";
    $dbname = "nome database";

    try {
        $dbConnection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(array("success" => false, "error" => "Impossibile accedere al database!"));
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
