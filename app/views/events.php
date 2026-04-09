<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Evenements</title>

    <link rel="stylesheet" href="assets/styles/events_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js"></script>
</head>

<body class="body_margin">

    <?php require_once 'app/views/header.php'; ?>

    <h1>LES EVENEMENTS</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <?php $messageStyle = (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'error') ? 'error-message' : 'success-message'; ?>
        <div id="<?php echo $messageStyle; ?>"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="calendar-filters">
        <label for="filter-date">Date :</label>
        <input type="date" id="filter-date">

        <label for="filter-type">Type :</label>
        <select id="filter-type">
            <option value="all">Tous</option>
            <option value="soirée">Soirée</option>
            <option value="sport">Sport</option>
            <option value="réunion">Réunion</option>
            <option value="autre">Autre</option>
        </select>

        <button type="button" id="reset-filters">Réinitialiser</button>
    </div>
    <div class="event-wrapper">
        <div id="calendar"></div>
    </div>

    <section>
        <div class="events-display">
            <?php foreach ($eventsDisplay as $item):
                $event = $item['data'];
                $eventDateInfo = $item['dateInfo'];
                ?>
                <div class="event-box <?php echo $item['otherClasses']; ?>" id="<?php echo $item['closestId']; ?>">
                    <div class="timeline-event">
                        <h4><?php echo ucwords($joursFr[$eventDateInfo['wday']] . ' ' . $eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']]); ?>
                        </h4>
                        <div class="vertical-line"></div>
                        <p><?php echo $item['datePinLabel']; ?></p>
                        <div class="timeline-marker <?php echo $item['datePinClass']; ?>">
                            <div class="time-line"></div>
                        </div>
                    </div>
                    <div class="event" event-id="<?php echo $event['id_evenement']; ?>">
                        <div>
                            <h2><?php echo htmlspecialchars($event['nom_evenement']); ?></h2>
                            <p><?php echo ucfirst(htmlspecialchars($event['type_evenement'] ?? 'autre')); ?></p>
                            <?php echo ucwords(htmlspecialchars($event['lieu_evenement'])); ?>
                        </div>
                        <h4 class="<?php echo $item['eventClass']; ?>">
                            <?php echo $item['eventLabel']; ?>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a class="show-more" href="index.php?page=events&show=<?php echo (int) $showMore; ?>">Voir plus loin dans le
            passe</a>
    </section>


    <!--<script>
        const calendarEl = document.getElementById('calendar');
        const events = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            initialView: 'dayGridMonth',
            firstDay: 1,
            aspectRatio: 3,
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
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            }
        });
        calendar.render();
    </script>-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const filterDateEl = document.getElementById('filter-date');
            const filterTypeEl = document.getElementById('filter-type');
            const resetBtn = document.getElementById('reset-filters');

            const allEvents = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

            function filterEvents() {
                const selectedDate = filterDateEl.value;
                const selectedType = filterTypeEl.value;

                return allEvents.filter(event => {
                    const eventDate = String(event.start).split('T')[0];
                    const eventType = event.extendedProps?.type ?? 'autre';

                    const matchDate = !selectedDate || eventDate === selectedDate;
                    const matchType = selectedType === 'all' || eventType.toLowerCase() === selectedType.toLowerCase();

                    return matchDate && matchType;
                });
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
                firstDay: 1,
                aspectRatio: 3,
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
                events: function (fetchInfo, successCallback, failureCallback) {
                    successCallback(filterEvents());
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });

            filterDateEl.addEventListener('change', () => calendar.refetchEvents());
            filterTypeEl.addEventListener('change', () => calendar.refetchEvents());

            resetBtn.addEventListener('click', () => {
                filterDateEl.value = '';
                filterTypeEl.value = 'all';
                calendar.refetchEvents();
            });

            calendar.render();
        });
    </script>

    <?php require_once 'app/views/footer.php'; ?>
    <script src="assets/scripts/event_details_redirect.js"></script>
</body>

</html>