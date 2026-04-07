<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">


    <link rel="stylesheet" href="assets/styles/planner_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js"></script>
</head>

<body class="body_margin">

    <?php require_once "app/views/header.php"; ?>

    <h1>Agenda</h1>

    <div class="agenda-wrapper">
        <div id="calendar"></div>
    </div>

    <?php
    $calendarEvents = [];

    foreach ($reunions as $reunion) {
        $fichier = $reunion['fichier_reunion'];

        if (!preg_match('#^https?://#', $fichier)) {
            $fichier = 'http://files.bdeinfo.fr/' . $fichier;
        }

        $calendarEvents[] = [
            'title' => 'Réunion ADIIL',
            'start' => substr($reunion['date_reunion'], 0, 10),
            'extendedProps' => [
                'fichier' => $fichier
            ]
        ];
    }
    ?>
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const events = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
                firstDay: 1,
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                buttonText: {
                    today: 'Aujourd’hui',
                    month: 'Mois',
                    list: 'Liste'
                },
                events: events,
                eventClick: function (info) {
                    info.jsEvent.preventDefault();  // Toujours au début

                    const fichier = info.event.extendedProps.fichier;

                    if (fichier) {
                        window.open(fichier, '_blank');
                    }
                }
            });

            calendar.render();
        });
    </script>

    <?php require_once "app/views/footer.php"; ?>

</body>

</html>