<?php

require_once 'app/models/reunionModel.php';
require_once 'app/models/agendaModel.php';

// Récupérer toutes les réunions pour les afficher dans le calendrier
$reunions = getAllReunions();

// Récupérer le lien de l'agenda externe de l'utilisateur pour pré-remplir le formulaire
$urlEdt = '';
$message = '';
$error = '';
$agendaUtilisateur = [];

// Vérifier qu'un membre est connecté
/*if (!isset($_SESSION['userid'])) {
    $error = "Utilisateur non connecté.";
} else {
  
    $idMembre = $_SESSION['userid'];
    echo "ID MEMBRE: " . $idMembre . " (type: " . gettype($idMembre) . ")<br>";

    $agendaUtilisateur = getAgendaByUserId($idMembre);

    if (!empty($agendaUtilisateur)) {
        $urlEdt = $agendaUtilisateur[0]['url_edt'];
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_edt'])) {
        $urlEdtForm = trim($_POST['url_edt']);

        if (!empty($urlEdtForm) && (filter_var($urlEdtForm, FILTER_VALIDATE_URL) || preg_match('/^https?:\/\//i', $urlEdtForm)))  {
            if (!empty($agendaUtilisateur)) {
                updateAgendaUtilisateur($idMembre, $urlEdtForm, 'ics');
                $message = "Lien EDT mis à jour avec succès.";
            } else {
                insertAgendaUser($idMembre, $urlEdtForm, 'ics');
                $message = "Lien EDT enregistré avec succès.";
            }

            $urlEdt = $urlEdtForm;
        } else {
            $error = "Le lien renseigné n'est pas valide.";
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_edt'])) {
        echo '<div style="background:orange;color:white;padding:30px;">';
        echo "🚀 INSERT BRUT SANS FILTRE<br>";

        $urlEdtForm = trim($_POST['url_edt']);
        echo "ID membre: $idMembre<br>";
        echo "URL: " . htmlspecialchars($urlEdtForm) . "<br>";

        // INSERT DIRECT sans aucune condition
        $result = insertAgendaUser($idMembre, $urlEdtForm, 'ics');
        echo "Résultat insertAgendaUser(): ";
        var_dump($result);

        // Vérif en base
        $check = getAgendaByUserId($idMembre);
        echo "Après insert en base: ";
        var_dump($check);

        echo '</div>';
    }
}*/

if (!isset($_SESSION['userid'])) {
    $error = "Utilisateur non connecté.";
} else {
    $idMembre = $_SESSION['userid'];
    $agendaUtilisateur = getAgendaByUserId($idMembre);

    if (!empty($agendaUtilisateur)) {
        $urlEdt = $agendaUtilisateur[0]['url_edt'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_edt'])) {
        $urlEdtForm = trim($_POST['url_edt']);

        if (!empty($urlEdtForm)) {
            if (!empty($agendaUtilisateur)) {
                updateAgendaUtilisateur($idMembre, $urlEdtForm, 'ics');
                $message = "Lien EDT mis à jour avec succès.";
            } else {
                insertAgendaUser($idMembre, $urlEdtForm, 'ics');
                $message = "Lien EDT enregistré avec succès.";
            }

            $urlEdt = $urlEdtForm;
        } else {
            $error = "Le lien renseigné est vide.";
        }
    }
}

$calendarEvents = [];

// Réunions locales
foreach ($reunions as $reunion) {
    $fichier = $reunion['fichier_reunion'] ?? '';

    if (!empty($fichier) && !preg_match('#^https?://#', $fichier)) {
        $fichier = 'http://files.bdeinfo.fr/' . $fichier;
    }

    $calendarEvents[] = [
        'title' => 'Réunion ADIIL',
        'start' => substr($reunion['date_reunion'], 0, 10),
        'extendedProps' => [
            'fichier' => $fichier
        ],
        'backgroundColor' => '#11998e',
        'borderColor' => '#11998e',
        'textColor' => '#ffffff'
    ];
}

// EDT externe ICS
if (!empty($urlEdt)) {
    $rawICS = @file_get_contents($urlEdt);

    if ($rawICS !== false) {
        $rawICS = str_replace(["\r\n", "\r"], "\n", $rawICS);
        $lines = explode("\n", $rawICS);
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent) {
                if (
                    !empty($currentEvent['DTSTART']) &&
                    !empty($currentEvent['DTEND']) &&
                    !empty($currentEvent['SUMMARY'])
                ) {
                    /*$start = DateTime::createFromFormat('Ymd\THis\Z', $currentEvent['DTSTART']);
                    $end = DateTime::createFromFormat('Ymd\THis\Z', $currentEvent['DTEND']);*/
                    $start = DateTime::createFromFormat(
                        'Ymd\THis\Z',
                        $currentEvent['DTSTART'],
                        new DateTimeZone('UTC')
                    );
                    $end = DateTime::createFromFormat(
                        'Ymd\THis\Z',
                        $currentEvent['DTEND'],
                        new DateTimeZone('UTC')
                    );

                    if ($start && $end) {
                        $start->setTimezone(new DateTimeZone('Europe/Paris'));
                        $end->setTimezone(new DateTimeZone('Europe/Paris'));
                        $calendarEvents[] = [
                            'title' => $currentEvent['SUMMARY'],
                            'start' => $start->format('Y-m-d H:i:s'),
                            'end' => $end->format('Y-m-d H:i:s'),
                            'extendedProps' => [
                                'fichier' => null,
                                'location' => $currentEvent['LOCATION'] ?? ''
                            ],
                            'backgroundColor' => '#3b82f6',
                            'borderColor' => '#3b82f6',
                            'textColor' => '#ffffff'
                        ];
                    }
                }

                $currentEvent = null;
            } elseif (preg_match('/^([A-Z]+)(?:;[^:]+)?:([\s\S]*)$/', $line, $matches)) {
                $currentEvent[$matches[1]] = str_replace('\,', ',', trim($matches[2]));
            }
        }
    }
}
require_once 'app/views/agenda.php';
