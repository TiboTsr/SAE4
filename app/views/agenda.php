<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/planner_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js"></script>
</head>

<body class="body_margin">

    <?php require_once "app/views/header.php"; ?>

    <div class="page-back-wrap">
        <a class="page-back" href="index.php?page=home">
            <img src="assets/images/fleche_retour.png" alt="Retour">
            Retour à l'accueil
        </a>
    </div>

    <h1>Agenda</h1>

    <section class="edt-form-section">
        <h2>Mon emploi du temps</h2>
        <p>Collez ici votre lien iCal/ICS fourni par l’ENT pour afficher votre emploi du temps dans le calendrier.</p>

        <form action="index.php?page=agenda" method="POST" class="edt-form">
            <label for="url_edt">Lien de l’emploi du temps</label>
            <input type="url" id="url_edt" name="url_edt" placeholder="https://planning.univ-lemans.fr/..."
                value="<?= isset($urlEdt) ? htmlspecialchars($urlEdt) : '' ?>" required>

            <button type="submit" name="save_edt">Enregistrer mon EDT</button>
        </form>
    </section>

    <div class="agenda-wrapper">
        <div id="calendar"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const events = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

            console.log('Events finaux :', events);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
                firstDay: 1,
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Aujourd’hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                events: events,
                eventClick: function (info) {
                    info.jsEvent.preventDefault();

                    const fichier = info.event.extendedProps.fichier;
                    if (fichier) {
                        window.open(fichier, '_blank');
                    }

                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });

            calendar.render();
        });
    </script>

    <?php require_once "app/views/footer.php"; ?>

</body>

</html>