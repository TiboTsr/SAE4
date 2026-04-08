<?php

require_once 'app/models/reunionModel.php';
require_once 'app/models/agendaModel.php';
require_once 'app/models/eventsModel.php';

// Récupérer toutes les réunions pour les afficher dans le calendrier
$reunions = getAllReunions();

// Récupérer les événements auxquels on est inscrit à afficher dans le calendrier
$eventsInscrits = getSubscribedEventsByUser($_SESSION['userid']);

// Récupérer le lien de l'agenda externe de l'utilisateur pour pré-remplir le formulaire
$urlEdt = '';
$message = '';
$error = '';
$agendaUtilisateur = [];

// Vérifier qu'un membre est connecté
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

// Événements inscrits
foreach ($eventsInscrits as $event) {
    $calendarEvents[] = [
        'title' => $event['nom_evenement'],
        'start' => substr($event['date_evenement'], 0, 10),
        'url' => 'index.php?page=event_details&id=' . $event['id_evenement'],
        'extendedProps' => [
            'type' => 'evenement',
            'description' => $event['description_evenement'] ?? '',
            'location' => $event['lieu_evenement'] ?? '',
            'prix' => $event['prix_evenement'] ?? 0,
            'xp' => $event['xp_evenement'] ?? 0
        ],
        'classNames' => ['event-orange'],
        'backgroundColor' => '#f59e0b',
        'borderColor' => '#f59e0b',
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
