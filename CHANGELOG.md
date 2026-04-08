# Journal des corrections

## 2026-04-07

### Sécurité et robustesse
- Sécurisation du routage dynamique dans `index.php` avec une validation par expression régulière avant chargement d'un contrôleur.
- Remplacement du `session_start()` silencieux dans `header.php` par un démarrage conditionnel propre.
- Durcissement des couches de connexion base de données dans `api/DB.php` et `app/models/Database.php` avec lecture des identifiants depuis des variables d'environnement `SAE_DB_USER`, `SAE_DB_PASS` et `SAE_DB_NAME`.
- Renforcement des sauvegardes de fichiers dans `app/models/files_save.php`, `api/tools.php` et `api/models/File.php` avec validation stricte des extensions et des types MIME.

### Refactorisation du code duplique
- `app/models/files_save.php` : extraction de constantes communes (extensions autorisees, MIME images, dossier d'upload) et de fonctions utilitaires (`getUploadedFileTmpPath`, `getUploadPath`) pour supprimer les verifications et chemins repetes.
- `api/tools.php` : suppression de blocs dupliques dans `saveFile`, `saveImage` et `deleteFile` en passant par des helpers internes et des constantes centralisees.
- `api/models/File.php` : centralisation des mappings MIME/extensions, ajout d'un helper de chemin d'upload, simplification de `getFile`/`deleteFile` et clarification des branches POST vs PUT/PATCH dans `saveImage`.

### Evenements
- `app/views/event_details.php` : ajout d'un bouton `Se desinscrire` pour les utilisateurs deja inscrits a un evenement a venir.
- `app/controllers/event_subscription.php` : prise en charge de l'action `unsubscribe` avec redirection vers la page detail de l'evenement.
- `app/models/eventsModel.php` : ajout des fonctions de suppression d'inscription et de retrait d'XP associees a une desinscription.
- `app/models/eventsModel.php` : credit XP automatique lors de l'inscription a un evenement et debit automatique lors de la desinscription.
- `app/controllers/event_subscription.php` : suppression des credits/debits XP manuels pour eviter les doublons, la logique est centralisee dans le modele.

### Commande
- Correction de la redirection après achat dans `app/controllers/order.php`.
- Ajout d'une validation du mode de paiement avant enre²istrement.
- Alignement du calcul des totaux affichés avec les quantités réellement plafonnées par le stock.
- Nettoyage de l'affichage dans `app/views/order.php` pour éviter les sorties non typées.
- `app/controllers/order.php` : prechargement des informations personnelles du compte (prenom, nom, email, TP) pour la validation panier.
- `app/views/order.php` : formulaire de validation unifie avec pre-remplissage automatique des infos perso et modes de paiement dynamiques.
- `assets/styles/order_style.css` : refonte visuelle de la page commande (cartes, grille responsive, champs plus lisibles, CTA modernise).
- `app/views/event_subscription.php` : reutilisation de la meme base visuelle que la page commande pour la validation d'inscription a un evenement.

### Validation
- Vérification de syntaxe PHP effectuée sur les fichiers modifiés.
- Aucun problème de syntaxe détecté sur les fichiers contrôlés.

### Impact attendu
- Réduction des alertes Sonar liées au routage, aux uploads, aux secrets en dur et aux sorties non échappées.
- Comportement plus cohérent sur le panier et la commande après achat.

- `api/DB.php` : connexion MySQL lue via variables d'environnement (`SAE_DB_USER`, `SAE_DB_PASS`, `SAE_DB_NAME`) et `clean()` renforce avec `ENT_QUOTES` et `UTF-8`.
- `api/models/File.php` : factorisation de l'upload (constantes, chemin centralise, whitelist extension/MIME), gestion plus claire de POST/PUT/PATCH et suppression fichier via helper commun.
- `api/tools.php` : suppression de duplication pour upload/suppression de fichiers avec constantes et helpers internes de validation/chemin.
- `app/controllers/event_subscription.php` : ajout du flux de desinscription (`action=unsubscribe`) et ajout de messages de confirmation/erreur en session pour inscription et desinscription.
- `app/controllers/order.php` : validation stricte du mode de paiement, redirection de fin de commande vers le panier, et correction du calcul total avec les quantites du panier normalisees.
- `app/models/Database.php` : meme durcissement que `api/DB.php` (variables d'environnement, exception de connexion, nettoyage HTML plus strict).
- `app/models/eventsModel.php` : ajout des fonctions SQL de desinscription et retrait d'XP avec plancher a 0, plus wrappers metier associes.
- `app/models/files_save.php` : factorisation complete des uploads (constantes d'extensions/MIME, helper de chemin, helper de fichier temporaire, suppression centralisee).
- `app/views/event_details.php` : affichage des messages session (success/error) et ajout du bouton `Se desinscrire` si l'utilisateur est deja inscrit.
- `app/views/events.php` : affichage des messages session apres redirection (inscription reussie, doublon, erreurs).
- `app/views/order.php` : correction HTML (lien retour panier), cast explicite pour valeurs numeriques affichées et nettoyage mineur du script.
- `index.php` : securisation du routage `page` via regex autorisant uniquement un identifiant alpha/underscore avant `require`.

### Evenements - gestion des images activee
- `admin/scripts/events.js` : suppression du blocage qui affichait le message "schema non pris en charge"; l'upload image repasse par `PATCH /api/event.php?id=...`.
- `api/event.php` : la route `PATCH` enregistre maintenant une image via `File::saveImage()`, met a jour l'evenement et retourne une erreur claire si le format est invalide.
- `api/models/Event.php` : le modele lit/ecrit desormais `image_evenement` et `description_evenement`, met a jour l'image en base et supprime l'ancienne image physique.
- `app/models/eventsModel.php` : la vue publique recupere maintenant la vraie image et la vraie description de l'evenement.
- `assets/script.sql` : ajout des colonnes `description_evenement` et `image_evenement` sur `EVENEMENT` (creation initiale + migration `IF NOT EXISTS`).

### Evenements - places illimitees
- `app/models/eventsModel.php` : correction de `isPlaceAvailable()` pour interpreter `places_evenement = -1` comme "illimite" au lieu de "complet".
- `assets/script.sql` : correction du trigger `verif_places_eventb` pour ne pas bloquer les inscriptions quand `places_evenement = -1`.
- `app/views/event_details.php` : prise en compte explicite des evenements illimites lors de l'affichage du bouton d'inscription.
- `app/models/eventsModel.php` : ajout d'un contournement de compatibilite pour les inscriptions illimitees sur les bases encore dotees de l'ancien trigger.
- `app/controllers/event_details.php`, `app/controllers/event_subscription.php`, `app/views/event_details.php`, `app/views/event_subscription.php` : affichage du nombre de places restantes, avec libelle "Illimite" pour les evenements a `-1`.

- `app/models/files_save.php` : ajout de `resolveStoredImageSrc()` pour ignorer `file_bdeinfo.fr`, basculer vers les fichiers locaux `api/files/` si disponibles, et utiliser l'image par defaut sinon.
- `app/views/shop.php`, `app/views/grade.php`, `app/views/account.php`, `app/views/cart.php`, `app/views/home.php` : affichage des images adapte aux donnees locales sans dependre d'un lien externe inaccessible.
- `app/controllers/cart.php` et `app/controllers/home.php` : chargement du helper d'images pour rendre la resolution de chemin disponible dans les vues.

### Administration - chat
- `admin/panels/chat.html` : remplacement du placeholder par une interface fonctionnelle pour consulter et envoyer des messages.
- `admin/scripts/chat.js` : chargement auto des messages, rafraichissement manuel, envoi de message et affichage auteur/date.
- `admin/styles/chat.css` : style dedie au chat administrateur.
- `api/chat.php` : ajout de l'API de chat admin (`GET` consulter, `POST` envoyer) avec controle d'acces admin.
- `assets/script.sql` : ajout de la table `ADMIN_CHAT` pour les installations neuves.
- `admin/scripts/admin.js` : ajout d'un cache-busting sur le chargement des panels pour eviter l'affichage d'anciennes versions en cache (ex: ancienne page chat avec image). 

### Paiement unifie
- `assets/styles/order_style.css` : le conteneur `order-layout` occupe toute la largeur de l'ecran pour les pages de paiement.
- `assets/styles/order_style.css` : suppression du debordement horizontal sur les pages de paiement (plus de scroll horizontal).

### Redirection apres connexion
- `app/controllers/order.php` et `app/controllers/event_subscription.php` : si l'utilisateur n'est pas connecte, redirection vers `login` avec une URL de retour (`next`).
- `app/controllers/login.php` : validation/sanitation de l'URL `next`, stockage temporaire en session puis redirection vers la page d'origine apres connexion reussie.
- `app/views/login.php` : conservation de l'URL de retour via un champ cache dans le formulaire.

