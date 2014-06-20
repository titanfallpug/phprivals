<?php
/**
*
* phpRivalsMOD [French]
*
* @package language
* @version $Id: lang_rivals.php 2.0 rev.003 $
* @copyright (c) 2012 toxic
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …

$lang	= array_merge($lang, array(
// DEFINITION FOR 2.0 START -->
  	'MEMBER_UPDATE_LIKE_COFOUNDER' 		=> 'Cet utilisateur est maintenant Team-Owner',
	'COF_SI' 							=> 'Level: Team-Owner',
	'COF_NO' 							=> 'Level: Membre',
	'ASSIGN_AS_COLEADER' 				=> 'Attribuer l’utilisateur comme Team-Owner',
	'DA_TEMPO' 							=> 'Historique des matchs:',
	'STATUS'							=> 'Status:',
	'CARATTERISTICHE' 					=> 'Kind of Ladder:',
	'COMBATTENTI' 						=> 'Team:',
	'VITTORIOSO' 						=> 'Win by:',
	'LEGENDA_COFOUNDER' 				=> 'Un utilisateur Team-Owner peut supprimer des membres du Team, ajouter des membres et lancer un match contre les autres clans.',
	'NOAZIONE' 							=> 'Pas action',
	'MEMBER_UPDATED' 					=> 'Mise à jour Liste des membres',
	'TITOLO_PAGINA_MVP' 				=> 'Player MVPs CHART',
	'NU' 								=> 'N&#183;',
	'UTENTE' 							=> 'Utilisateur',
	'CLAN' 								=> 'Team',
	'REGISTRATO' 						=> 'Playing from',
	'MVP' 								=> 'MVP',
	'GIOCANDO_PER'						=> 'Avec la Team: ',
	'DAL' 								=> 'From: ',
	'NO_MEMBERS_MVP' 					=> 'Aucun utilisateur avec MVP trouvés, peut-être il n’ya pas de jeu dans le ladder?',
	'GROUP_SITO' 						=> 'Site de l’équipe: ',
	'UTENTE_DEL_CLAN_SCELTO' 			=> 'Utilisateur',
	'EDIT_MVP_LEGENDA' 					=> 'Ajouter l’ID de la Team pour limité aux utilisateurs.',
	'MVP_BASE_USER' 					=> 'Actuel MVP',
	'A_CUI_SOMMO' 						=> 'Ajouter cette valeur: ',
	'MVP_MENU' 							=> 'Top MVP',
	'PARTITA' 							=> 'Match',
	'THE_WINNER_IS' 					=> 'Win:',
	'UWAR_INFO' 						=> 'Info de matche',
	'TITOLO_PAGINA_UWAR' 				=> 'Derniers matches joués',
	'GIOCATA_IL' 						=> 'joué au ',
	'GIOCATA_SU' 						=> 'on',
	
	'MVPLIST_NAME' 						=> 'MVP chart nom',
	'MVPLIST_DESC' 						=> 'MVP chart description',
	'MVPLIST_PLATFORM' 					=> 'Nombre de ligue liée',
	'MVPLIST_PLATFORM_ISTR' 			=> '[ATTENTION] Si le ladder que vous souhaitez lier à un graphique à MVP n’est pas dans la forme, c’est parce que vous avez besoin de modifier le ladder et activez l’option "Activer le système MVP sur ce ladder?” option.',
	'MVPLIST_LADDER' 					=> 'Linked ladder',
	'MVPLIST_LADDER_ISTR' 				=> '[Exemple] Il numero da prendere da un url della ladder è quello in grassetto:<br />
url/rivals.php?action=subladders&ladder_id=<strong>4</strong>&sid=030556093e593a3a5a766510eddaa6dd',
	'TITOLO_PAGINA_MVP_DESC' 			=> 'TOP MVP chart répertoriés par ladders. Les joueurs apparaissent ici seulement s’ils ont un MVP supérieur à 0.',
	'TITOLO_PAGINA_MVP_CHART' 			=> 'TOP MVP on',
	'MVP_CHART_UPDATED' 				=> 'TOP MVP LISTE MISE A JOUR',
	'CLAN_ATTENDI_WAR' 					=> 'Clans prêts pour les matchs',
	'AZIONE' 							=> 'Action',
	'SU_LADDER' 						=> 'Type',
	'FINO_ALLE' 						=> 'Début',
	'GROUP_POSITION' 					=> 'Ladder Rank',
	'CERCA_WAR_AGGIUNGITI' 				=> 'Utilisez le formulaire ci-dessous pour proposer une PCW!',
	'DOVE_CERCHI' 						=> 'War on: ',
	'TRA_QUANTO' 						=> 'Attendre: ',
	'NOUWAR' 							=> 'Les team sont à la recherche d’un match',
	'IL_CLAN' 							=> 'Le Clan',
	'DICE_DI_AVER_VINTO' 				=> 'ont rapporté une victoire contre vous',
	'A' 								=> 'à',
	'PUNTEGGIO' 						=> 'points de matche',
	'TU' 								=> 'Vous =',
	'ALTRO' 							=> 'autre =',
	'TITOLO_PAGINA_RANDOM' 				=> 'Maps du jour',
	'TITOLO_PAGINA_RANDOM_DESC' 		=> 'Cette map est la carte de la journée. Toutes les données changent automatiquement.',
	'NO_GIOCO_RANDOM' 					=> 'Tous les jeux enregistrés',
	
	'ACP_RIVALS_NEW_RULES' 				=> 'Ajouter des règles à un ladder',
	'PLATFORM_ID' 						=> 'ID Ligue',
	'LADDER_ID' 						=> 'ID Ladder',
	'CREA_NUOVA' 						=> 'Ajouter une nouvelle page de règles',
	'ACP_RIVALS_ALTRO_RULES' 			=> 'Modifier ou supprimer une page de règles',
	'LEGENDA_TEXTAREA' 					=> 'Le formulaire textarea en langage html permettent . Pour le formatage du texte, vous pouvez utiliser cette commande:<br /><br />&lt;b&gt;TEXT&lt;/b&gt; pour le texte en gras<br />
	&lt;i&gt;TEXT&lt;/i&gt; pour le texte en italique<br />
    &lt;u&gt;TEXT&lt;/u&gt; pour le texte souligné <br /> 
    &lt;p align=&quot;center&quot;&gt;TEXT&lt;/p&gt; pour centré le texte <em>-&gt; [right, left]</em><br />
    &lt;font size=&quot;NUMBER&quot;&gt;TEXT&lt;/font&gt;<br />
    &lt;font color=&quot;COLOR&quot;&gt;TEXT&lt;/font&gt; pour la couleur du texte<br />
    <em> -&gt; [red: #FF0000, blue: #0000FF, yellow: #FFFF00, green: #00FF00, white: #FFFFFF, black: #000000, etc...]</em>',
	'REQUISITI' 						=> 'Règles de souscription',
	'REG_GENERALI' 						=> 'règles général',
	'CONFIG' 							=> 'Configuration de match',
	'VIETATO' 							=> 'Actions non autorisées',
	'TITOLO_PAGINA_RULES_LADDER' 		=> 'Règles du ladder',
	'NO_RULES' 							=> '<br />Ces ladders n’ont pas de règles<br /><br />',
	'RULES_CHART_ADDED' 				=> 'Chart règle ajoutée',
	'RULES_CHART_UPDATED' 				=> 'Chart règle mis à jour',
	'RULES_CHART_DELETED' 				=> 'Chart règle supprimé',
	'PLATFORM_UPDATED' 					=> 'Ligue mis à jour',
	'MPV_LIST_ADDED' 					=> 'MVP Chart ajouté',
	'NO_UWAR' 							=> 'Les matchs encore joué',
	
	'REPORTED_MATCHES_DECERTO' 			=> 'Matchs DECERTO reportée ',
	'UNREPORTED_MATCHES_DECERTO' 		=> 'Matchs DECERTO non reportée  ',
	'CLAN_TEMPO' 						=> 'Team - Date',
	'PUNTEGGIO_PARZIALE' 				=> 'Score détaillé',
	'CERCA_E_DISTRUGGI' 				=> 'Seed and Destroy on',
	'DOMINIO' 							=> 'Demolition on',
	'BANDIERA' 							=> 'Capture the flag su',
	'DECERTO_UPDATED' 					=> 'Decerto id updated',
	'CONFIGURE_DECERTO' 				=> 'Réglez le ladder ou le système Decerto est actif',
	'U_SFIDA' 							=> 'Défier',
	'SFIDA_ACCETTATA' 					=> 'Défie accepté',
	'NON_SFIDARE_TE_STESSO' 			=> 'Vous ne pouvez pas vous défié!',
	'MOTIVO_ANINIMI' 					=> '* Oui, le nom de Team Que vous recherchez pour un matche est non visibles. Nous pensons que c’est utile pour assuré des matchs pour les meilleurs Teams<br />',
	'ORDINE_GIOCO' 						=> 'Games order:',
	'CED_SHORT' 						=> 'S&amp;D',
	'DOMINIO_SHORT' 					=> 'Demolition',
	'BANDIERA_SHORT' 					=> 'Capt. Flag',
	'FONDATORE' 						=> 'Level: Team-Owner',
	'GROUP_DELETED' 					=> 'Team supprimer',
	'COFONDATORE_ALTRO_CLAN' 			=> 'Un utilisateur que vous avez choisi est juste Team-Owner ou warranger dans d’autre Team. Vous pouvez le promouvoir dans votre Team',
	
	'PUNTI_VINCITORE' 					=> 'Winner score',
	'PUNTI_PERDENTE' 					=> 'Loser score',
	'COMPILA_SOLO_SE' 					=> 'Le formulaire ci-dessous apparaîtra seulement si le ladder est DECERTO',
	
	'LADDER_LIST_IMPORTANTE' 			=> '<strong>Votre team n’est pas dans la liste ci-dessous ? C’est parce que vous n’êtes dans aucun ladder.<br /><br />
  REJOIGNEZ UN LADDER MAINTENANT! <a href="./rivals.php?action=platforms">CLICK HERE</a></strong>',
    
	'ATTIVO'							=> 'Actif',
	'NON_ATTIVO' 						=> 'Non actif',
	'LISTA_CLAN_COMPLETA'  				=> 'Liste de team complète',
	'LISTA_CLAN_COMPLETA_DESC' 			=> 'Si vous êtes non actifs vous devez joindre un ladder',
	'ROLE' 								=> 'Level',
	'COF_SI_SHORT' 						=> 'Warranger',
	'COF_NO_SHORT' 						=> 'Player',
	'FONDATORE_SHORT' 					=> 'Team-Owner',
	
	'LEGENDA' 							=> '<h2>Légende de Récompense</h2>',
	'CORONA' 							=> 'Si votre Team gagne contre la team avec la meilleure différence entre ces victoires et ces défaite de match. vous recevrez des points supplémentaires!',
	'TOMBA' 							=> 'Si votre Team gagne contre la team avec la pire différence entre ces victoires et ces défaite de match.vous ne recevrez aucun points supplémentaires!',
	'CALDO' 							=> 'Top Team. Si votre Team gagne contre cette team avec cette récompense, vous recevrez des points supplémentaires!',
	'FREDDO' 							=> 'Plus mauvaise team.',
	'LADDER_STATUS' 					=> 'Activité',	
	'LADDER_GROUP_PARI' 				=> 'Draws',
	'PAREGGIO' 							=> 'Draw',
	'LADDER_GROUP_GOAL_FATTI' 			=> 'Goals done',
	'LADDER_GROUP_GOAL_SUBITI' 			=> 'Goals p.up',
	'LEG_CAPOCAN' 						=> 'Qui a cette icône est la Team avec la meilleure différence entre des Buts marquer et des Buts encaisser.',
	'LEG_PIUGOAL' 						=> 'Qui a cette icône est la Team avec la pire différence entre des Buts marquer et des Buts encaisser.',
	'PAVIDO' 							=> '<em>Team supprimer</em>',
	'GROUP_UNDELETABLE' 				=> 'Votre Team a joué 5 match ou plus. Pour sauvegarder les stats du site vous ne pouvez pas le supprimer.<br />
	Si vous voulez la restart vos stats, vous devez créer une nouvelle team.',
	'REPORTED_MATCHES_CALCIO' 			=> 'Match déclarées du ladder avec les règles du football',
	'UNREPORTED_MATCHES_CALCIO' 		=> 'Match non-déclarées du ladder avec les règles du football',
	'HA_RIPORTATO_IL_RISULTATO' 		=> 'A rapporté un résultat de:',
	'CONFIRM_CALCIO' 					=> 'Confirmé',
	'CONTEST_CALCIO' 					=> 'Constesté',
	'REPORT_CALCIO' 					=> 'Déclarées match',
	'STANDARD_LADDER' 					=> 'Standard Ladder',
	'DECERTO_LADDER' 					=> 'Ladder avec les règles Decerto(FPS)',
	'CPC_LADDER' 						=> 'Ladder avec les règles CPC (FPS)',
	'CALCIO_LADDER' 					=> 'Football ladder',
	'LEGENDA_LADDER' 					=> '<strong>Ce sont le genre de ladder sélectionnable avec leurs différences:</strong><br />
	STANDARD = Standard ladder pour tous les jeux.<br />
	DECERTO = Affiche 3 modes de jeu (Seek and Destroy, Demolition, Capture the Flag) aléatoire, au hasard sur la carte.<br />
	CPC = Affiche 3 cartes aléatoires.<br />
	CALCIO = Les point spécial et le système de rang qui permet des résultats. Les buts marquer et des buts encaissé sont utilisés pour calculéer le score.
	<br />',
	'MATCH_DELETED' 					=> 'Match supprimé.<br />Si c’était un classer un grand nombre de team serai ajusté aussi',
	'EDIT_MATCH' 						=> 'Supprimez des entrées de match',
	'EDIT_MATCH_EXP' 					=> 'Del illegittimate wars, but remember: if are classified wars the clan’s scores for that ladder will be modified',	
	
	'NON_SEI_NELLA_LADDER' 				=> 'Vous ne pouvez pas défié des team dans le ladder où vous n’êtes pas inscrit',
	'HAI_UN_MATCH_IN_CERCA' 			=> 'Vous êtes à la recherche d’une PCW sur les',
	'CLAN_ATTENDI_WAR_IO' 				=> 'Votre PCW trouver',
	'NOUWAR_IO' 						=> 'Vous ne trouverez pas une PCW en ce moment',
	'AVATAR_EXPLAIN' 					=> '<em>Max 160kb - Taille: min 100x100px, max 180x180px.</em>',
	'RETURN_UCP' 						=> 'Retoucher à àUCP',
	'MVP1' 								=> 'MVP round 1',
	'MVP2' 								=> 'MVP round 2',
	'MVP3' 								=> 'MVP round 3',
	'IMPOSTA_MVP_MATCH' 				=> 'FIXER MATCH MVPs',
	'IMPOSTA_MVP_MATCH_LEGENDA' 		=> '<em>Sélectionnez le joueur qui ont obtenu un MVP pour chaque round</em>',
	'GIOCATO_SU' 						=> 'joué sur',
	'CON_MVP' 							=> 'Avec des MVPs',
	'NONCLASSIFICATA2' 					=> '<br />(Unranked match)',
	'SFIDATO_TROPPO'					=> 'ERREUR! Vous avez défié cette team trop de fois. Pour évité tous abus vous ne pouvez pas défié cette team. < Br / > Vous devez attendre 3 jours à partir de maintenant.',
	'NO_DETTAGLIO'						=> 'Tous les détails trouvés',
	'LADDER_MVP' 						=> 'Activer le système MVP sur ce ladder?',
	'ABILITA_SU_LADDER' 				=> 'Lien vers le ladder',
	'ATTIVA_SU' 						=> '<strong>lié à</strong>',
	'RULES_DOPPIA' 						=> 'Vous avez déjà fixé des règles pour ce ladder!',
    'NON_QUADRATA' 						=> 'Vous n’avez pas téléchargé une image carré ar. S’il vous plaît utilisez seulement des images avec le même X et Y.',
	
	'ACP_RIVALS_NEW_RANDOM' 			=> 'Ajouter nouveau jeu et les cartes',
	'GAME_NAME' 						=> 'nom du jeu',
	'GAME_SHORT_NAME' 					=> 'Diminutif du nom du jeu',
	'UPLOAD_MAP_IMAGES' 				=> 'Fichier Zip avec des images des cartes',
	'MAX_10' 							=> 'Le nom doit ètre < 10 lettres',
	'ONLY_ZIP' 							=> 'Le fichier d’archive doit être en <strong>.ZIP</strong>',
	'ACP_RIVALS_RANDOM_ANTEPRIMA' 		=> 'Afficher les jeux ajouté',
	'SELEZIONA_GIOCO_RANDOM' 			=> 'Sélectionnez le jeu',
	'MOSTRA' 							=> 'Show',
	'ANTEPRIMA_RANDOM' 					=> 'Aperçu:',
	'RANDOM_DELETED' 					=> 'Jeu et cartes supprimés',
	'RANDOM_ADDED' 						=> 'Jeu et des cartes ajoutées, consultez l’aperçu',
	'ZIP_MANCANTE' 						=> 'Vous avez choisi l’archive zip avec des images de cartes!<br /><br /><A HREF="javascript:javascript:history.go(-1)">GO BACK!</A>',
	'NON_PIU_MVP' 						=> 'Ce match est terminé et signalé. Vous ne pouvez plus transmettre les MVPs.',
	'GIA_MVP' 							=> 'Vous avez déjà transmis des MVP pour ce match!',
	'LADDERS_CHIUSE' 					=> 'Ladders fermer',
	
	'VUOTO' 							=> 'vide',
	'SLOT' 								=> 'insérer',
	'GRUPPI_PRENOTATI' 					=> 'groupes inscrit',
	'POSITION' 							=> 'Placer les groupes',
	'POSIZIONE_OCCUPATA' 				=> 'Il y a déjà une team à cette position',
	'NO_RECORD_DATA' 					=> 'Aucune donnée trouvée',
	
	'TOURNAMENT_ADV' 					=> 'Avancée du tournoi',
	'TOURNAMENT_FORALL' 				=> 'Ouvert à tous',
	'TOURNAMENT_DIRECTELIM' 			=> 'Elimination directe',
	'TOURNAMENT_ROUNDROBIN' 			=> 'Round Robin',
	'ORGANIZZA_TORNEO' 					=> 'Configuration de tournoi',
	'STEP' 								=> 'ÉTAPE',
	'DISPONI_CLAN' 						=> 'Placez les teams sur les brackets',
	'PUBBLICA' 							=> 'Ouvrir le tournoi',
	'ROUNDS' 							=> 'Rounds',
	'NO_PAREGGI_TORNEO'					=> 'Sur les tournois à élimination directe du tirage au sort n’est pas autorisée<br />%sGO BACK!%s',
	'T_RISULTATO_RIPORTATO' 			=> 'Résultat correctement transmis. Attendre la validation de l’autre équipe',
	'MOSTRA_DETTAGLIO' 					=> 'Afficher les détails',
	'NASCONDI_DETTAGLIO' 				=> 'Cacher les détails',
	'MVP_ROUND_1' 						=> 'MVP round1',
	'MVP_ROUND_2'						=> 'MVP round2',
	'MVP_ROUND_3' 						=> 'MVP round3',
	'HA_GIOCATO' 						=> 'Comme joué',
	'KILLS' 							=> 'Kills',
	'DEADS' 							=> 'Deads',
	'ASSIST' 							=> 'Assists',
	'T_RISULTATO_CONFERMATO' 			=> 'Résultat confirmé correctement',
	'T_CONTESTA_TESTO' 					=> 'Ceci est un message automatisé.<br />
	La team [url=%s/rivals.php?action=group_profile&group_id=%s]%s[/url] à contestée un match %s tournoi.<br />
	Aller sur le panneau du modérateur et voir ce qui se passe. Merci.',
	
	'T_CONTESTA_TESTO_USER' 			=> 'Ceci est un message automatisé.<br />
	La team [url=%s/memberlist.php?mode=viewprofile&u=%s]%s[/url] à contestée un match %s tournoi.<br />
	Aller sur le panneau du modérateur et voir ce qui se passe. Merci.',
	
	'WAITED_TOURNAMENTS' 				=> 'Match en attente de confirmation',
	'SEGNALA_INATTIVITA' 				=> 'Faire suivre l’inactivité',
	'T_SEGNALA_INATTIVO' 				=> 'La team ne réponds pas aux match de tournoi signalé.',
	'T_POCO_TEMPO' 						=> 'Vous devez attendre au moins 3 jours avant pour un billet d’inactivité!<br />%sGO BACK!%s',
	'TORNEO_INIZIATO' 					=> 'Le tournoi a commencé',
	'TORNEO_CHIUSO' 					=> 'Le tournoi est terminer',
	'GAMERNAME' 						=> 'STEAMID',
	'RATIO' 							=> 'Ratio',
	'MVPS' 								=> 'MVPs',
	'SLOT_LIBERI' 						=> 'Place disponible',
	'TOURNAMENT_ACCESS' 				=> 'Accessibilité du tournoi',
	'NO_MEMBERS_DATA' 					=> 'Pas de données fondée',
	'INIZIATO' 							=> 'En cours',
	'CHIUSO' 							=> 'Terminer',
	'PRONTO_A_PARTIRE' 					=> 'Prêt à démarrer',
	'CLOSE' 							=> 'Fermer',
	'TOURNAMENT_DELETED' 				=> 'Tournoi correctement supprimés',
	'CPC' 								=> 'CPC',
	'DECERTO' 							=> 'Decerto',
	'AGGIUNGI_DECERTO' 					=> 'Ajouter nouveau jeu avec les règles de decerto',
	'DECERTO_GIOCO' 					=> 'Nom du jeu',
	'DECERTO_GIOCO_CORTO' 				=> 'Nom du jeu',
	'MAX_5' 							=> '<em>Longueur max 5</em>',
	'DECERTO_MODE1' 					=> 'Le premier mode',
	'DECERTO_MODE2' 					=> 'Le second mode',
	'DECERTO_MODE3' 					=> 'Le troisième mode',
	'DECERTO_CPC' 						=> 'Systeme',
	'LEGENDA_CPC' 						=> '<em>** Decerto donner un système complet pour le mode aléatoire et des cartes. CPC donner un mode de lecture aléatoire que pour les cartes.</em>',
	'AGGIUNGI_DECERTO_DETTAGLI' 		=> 'Ajoutez des cartes à un mode;',
	'DECERTO_MAPPA' 					=> 'Nom de la carte',
	'DECERTO_MODE' 						=> 'Le mode à lié',
	'AZIONI' 							=> 'Actions',
	'DECERTO_GIOCHI_E_MODALITA' 		=> 'Les enregistrements des listes ajouté',
	'NO_RECORD' 						=> 'Les données ajoutées',
	'AGGIUNGI_CPC' 						=> 'Ajouter un nouveau jeu avec les règles de la CPC',
	'DECERTO_MODO' 						=> 'Nom de mode',
	'LADDER_SHORTY' 					=> 'Lien vers Decerto ou CPC jeu',
	'STANDARD_T'						=> 'Tournoi normale',
	'DECERTO_T' 						=> 'Tournoi Decerto',
	'CPC_T' 							=> 'Tournoi CPC',
	'TOURNAMENT_DECERTO' 				=> 'Tournoi system',
	'MODALITA' 							=> 'Modes de jeu afin ',
	'ORDINE_MAPPE'						=> '<br /> Ordres des cartes ',
	'ORDINE_MAPPE_E_MODI' 				=> 'Cartes et ordre de modes',
	'SHORTY_PRESENTE' 					=> 'Le ShortName utilisé est lié à un autre jeu. Changez svp.',
	'MPV_LIST_ZERO' 					=> 'ERREUR: vous avez sélectionné un ladder, ou vous n’avez pas de nom de tableau MVP Ecrit .<br /><br /><A HREF="javascript:javascript:history.go(-1)">GO BACK!</A>',
	
	'IMG_DISALLOWED_EXTENSION' 			=> 'Vous avez chargé comme logo de la team une extension de fichier non valide. Nous permettons uniquement les fichiers png, bmp, jpg, giff!',
	'IMG_WRONG_FILESIZE' 				=> 'Vous avez chargé une image trop grosse: la taille max est de 160KB',
	'IMG_WRONG_SIZE' 					=> 'L’image que vous avez téléchargé est mauvaise dimension. S’il vous plaît utiliser une image entre les 100x100px et 180x180px.',
	'IMG_DISALLOWED_CONTENT' 			=> 'Fichier non autorisé.',
	'IMG_PARTIAL_UPLOAD' 				=> 'Téléchargement inachevée.',
	'IMG_NOT_UPLOADED' 					=> 'Processus de chargement du logo de team a échoué.',
	
	'LOGOMEN_DISALLOWED_EXTENSION' 		=> 'Vous avez chargé comme logo de la team une extension de fichier non valide. Nous permettons uniquement les fichiers png, bmp, jpg, giff!',
	'LOGOMEN_WRONG_FILESIZE' 			=> 'Vous avez chargé une image trop grosse: la taille max est de 160KB',
	'LOGOMEN_WRONG_SIZE' 				=> 'L’image que vous avez téléchargé est mauvaise dimension.',
	'LOGOMEN_DISALLOWED_CONTENT' 		=> 'Fichier non autorisé.',
	'LOGOMEN_PARTIAL_UPLOAD'			=> 'Téléchargement inachevée.',
	'LOGOMEN_NOT_UPLOADED' 				=> 'Processus de chargement du logo de ladder a échoué.',
	
	'FAVOURITE_MAP'						=> 'Cartes favorites',
	'FAVOURITE_MAP_EXPLAIN'				=> '<br /><em>Ecrivez ici vos cartes de prédilection pour chacun des jeux que vous jouez séparé par des virgules ,</em>',
	'FAVOURITE_TEAM'					=> 'Team favorites',
	'FAVOURITE_TEAM_EXPLAIN'			=> '<br /><em>Ecrivez ici équipes sportives que vous préférée pour chaque jeux auxquels vous jouez séparés par des virgules , </em>',
	'CLAN_NAME_USED'					=> 'Ce nom de team est déjà utilisé, s’il vous plaît changer.',
	'INVITEMEMBR'						=> 'Invitez des membres',
	'PENDINGMEMBER'						=> 'Membres en attente',
	'ASSISTS'							=> 'Assists',
	'GOALSF'							=> 'Goals F',
	'GOALSA'							=> 'Goals A',
	'RATIO'								=> 'Ratio',
	'INVITE_USERNAME'					=> 'Entrez le nom ou l’ID utilisateur du site que vous voulez inviter dans votre team:',
	'FOUNDER'							=> 'Team-Owner',
	'PLATFORM_LOGO'						=> 'Ligue logo',
	'PLATFORM_LOGO_EXP'					=> '<em>Vous devez utiliser une image avec des dimensions de 400x100px.</em>',
	'LADDER_LOGO'						=> 'Ladder logo',
	'LADDER_LOGO_EXP'					=> '<em>Seulement des images de 900x150 pxl</em>',
	'LADDERS_ATTIVE'					=> 'Active ladders',
	'CLAN_PARTECIPANTI'					=> 'Nombre de team qui ont rejoint le ladder: ',
	'LADDER_ADVSTATS'					=> 'Permettez-stats avancées:',
	'JOIN_LEAVE'						=> 'Rejoindre ou quitter',
	'COMMENT'							=> 'Commentaire',
	'REPORT_RESULT'						=> 'Rapport résultat',
	'CLASSIFICATA'						=> 'Classement',
	'NONCLASSIFICATA'					=> 'Pas de classement',
	'TYPE'								=> 'Type',
	'MAPSET'							=> 'Mapset:',
	'INFORMAZIONI_WAR'					=> 'Match info',
	'LADDER_WIN_SYSTEM'					=> 'Win/Lose liée',
	'SCORE_RELATED'						=> 'Score liée',
	'WIN_RELATED'						=> 'Rapport Manuel de victoire ',
	'RISULTATO'							=> 'par resultat',
	'RISULTATO_NO_SCORE'				=> 'Pouvez-vous marquer sur le terrain écrire les nembres seuls!<br /><br />%sGO BACK%s!',
	'LADDER_NOT_ALLOW_DRAW'				=> 'le ladder vous correspondent rapport permet pas l’ draw resulta.<br /><br />%sGO BACK%s!',
	'LIBERO'							=> ' Disponible.',
	'MODES_ORDER'						=> 'Ordes de Modes:',
	'RISULTATO_DECERTO'					=> 'Decerto resulta',
	'MODE1'								=> 'Mode1',
	'MODE2'								=> 'Mode2',
	'MODE3'								=> 'Mode3',
	'CALCIO_NON_RISULTATO_PER_SCELTA'	=> 'ERREUR! football ladder peut avoir seulement le Score du système de victoire Lié.',
	'NAME_LADDER_EMPTY'					=> 'ERREUR! Le nom du Ladder ne peut pas être vide!',
	'DECERTO_NON_RISULTATO_PER_SCELTA'	=> 'ERREUR! Decerto Ladder peut avoir seulement le Score du système de victoire Lié.',
	'DEVI_COLLEGARE_DECERTO_CPC'		=> 'ERREUR! Vous devez lier à votre ladder un jeu valable PCC.',
	'LADDER_MOD'						=> 'Ladder mod (User ID)',
	'LADDER_DESC'						=> 'Ladder déscription',
	'OPTIONAL'							=> '(option)',
	'MOD_ID_NON_NUMERIC'				=> 'Vous avez inséré un ID utilisateur invalide dans le mod le champ mod du ladder!',
	'TEAM_USED'							=> ' équipe utilisé',
	'RISULTATO_GIA_RIPORTATO'			=> 'Vous avez déjà rapportés ce match !<br /><br />%sGO BACK!%s',
	'GAMERTAG'							=> 'GamerTag',
	'GOALF'								=> 'Goals F',
	'GOALS'								=> 'Goals A',
	'NO_MVP'							=> 'Pas de MVP',
	'ROUND1' 							=> 'Round 1',
	'ROUND2' 							=> 'Round 2',
	'ROUND3'							=> 'Round 3',
	'STATISTICHE_AVANZATE_OK'			=> 'Succès de stats Avancée signalés.',
	'LOADING_ADVSTATS'					=> 'Chargement du formulaire de stats avancées...',
	'STATS_SOLO_NUMERI'					=> 'ERREUR! Vous pouvez que utiliser le numéro!<br /><br />%sGO BACK!%s',
	'REPORTED_TEXT'						=> 'la %s%s%s %s team à rapportés ce score, pour le %s match:',
	'VINCE'								=> 'Winner: ',
	'CONFIRM_RESULT'					=> 'Confirmé',
	'CONTESTA_RESULT'					=> 'Contesté',
	'ADVANC_STATS'						=> 'Stats avancées',
	'USERS_LEADERBOARD'					=> 'Classement utilisateurs',
	'EXP'								=> 'Score',
	'PLAYED_TIME'						=> 'Date',
	'CHALLANGER'						=> 'Team',
	'CHALLANGEE'						=> 'Team',
	'STATS'								=> 'Stats',
	'LATEST_WAR_TITLE'					=> 'Derniers matchs des équipes',
	'CURRENT_LOGO'						=> 'logo actuel',
	'ATTENZIONE_CANCEL'					=> 'ATTENTION! la Suppression dun ladder peut supprimé toutes les données et les statistiques de match!',
	
	'MATCH_FROM_MATCHFINDER' 			=> 'Match accepté dans le systemes de recherche de match.',
	'HAI_UN_MATCHFINDER_INSERITO' 		=> 'Vous êtes déjà trouver match pour ce ladder!',
	'LOGIN_TO_TEAM' 					=> 'Pour utiliser cette fonction vous devez vous inscrire dans une team.<br /><br />%sGO BACK!%s',
	'WELCOMETXT' 						=> 'Welcome %s dans le panel de controle de team. il semblent maintenant que vous n’avez pas encore de droit pour cette team.<br />
	Donc vous pouvez seulement gérer des matchs du ladder 1on1, les autres fonctions sont seulement pour les team-owner et le Propriétaire d’équipe.<br />
	Amusez-vous et jouer honnêtement!',
	'OTHER_CLAN_I' 						=> '<em>Cliquez sur le nom de team pour la gestion</em>',
	'1VS1_MATCHES' 						=> '1on1 matchs',
	'CLAN_MATCHES' 						=> 'Team matchs',
	'ICON_ADVSTATS'						=> 'Stats avancée actif',
	'ICON_LADDER'						=> 'Ladder icon',
	'1ICON_LADDER'						=> 'Decerto mode actif',
	'2ICON_LADDER' 						=> 'CPC mode actif',
	'3ICON_LADDER'						=> 'Football mode actif',
	'MEMBERSHIP_LOCKED'					=> 'Ladder Fermé pour rejoindre',
	'ICON_MVP'							=> 'MVP system actif',
	'PLATFORM_SHORT'					=> 'P',
	'LADDER_SHORT'						=> 'L',
	'SUBLADDER_SHORT'					=> 'S',
	'ADVSTATS_AT_LEAST_2'				=> 'Au moins un membre de chaque team doit avoir joué ce match!<br /><br />%sGO BACK%s!',
	'1VS1LADDER'						=> 'Ladder utilisateur sur la base 1on1 ',
	'VS'								=> 'vs',
	'SUBLADDER_1VS1'					=> 'Subladder 1on1 utilisateur sur la base',
	'SUBLADDER_1VS1_SHORT'				=> ' (utilisateur sur la base)',
	'LEFT_LADDER_USER'					=> 'Vous avez quitté le ladder.',
	'JOINED_WITH_LADDER_USER'			=> 'A partir de maintenant vous avez rejoint ce ladder.',
	'NUM_USERS'							=> 'Players',
	'FINISHED_MATCHES_USERS'			=> 'Matchs jouer',
	'LADDER_RULES'						=> 'Règles pour:',
	'NONEXISTANT_USER'					=> 'Utilisateur introuvable. Etes-vous sûr qu’il existe ?',
	'NONEXISTANT_USER2'					=> 'L’utilisateur que vous avez défié ne ​​jouent pas dans ce ladder.',
	'USER_ID_NAME'						=> 'Utilisateur ID / nom d’utilisateur',
	'USERS_PARTECIPANTI'				=> 'Nombres de joueurs qui ont rejoint le ladder:',
	'ROUND_W'							=> 'Wins round',
	'ROUND_L'							=> 'Loss round',
	'TICKED_TEXT'						=> 'Déscription de votre problème:',
	'RIPORTI_TICKET_FOR'				=> 'Vous envoyez un billet à propos du match:',
	'MATCHID_NONDEF'					=> 'indéfini',
	'MATCHID_TIPOONEONE'				=> '(1on1 utilisateur basé)',
	'LATEST_WAR_USERS_TITLE'			=> 'Derniers matchs des joueurs en 1on1',
	'USER_LADDER_HISTORY'				=> 'Historique de la base utilisateur 1on1',
	'SCORE'								=> 'Score',
	'WINS'								=> 'Wins',
	'LOSSES'							=> 'Losses',
	'STREAK'							=> 'période',
	'LADDER_PROFILE_GSTATS'				=> 'Stats générales',
	'USER_LADDER_LATESTMATCH'			=> 'Dernier match des joueurs',
	'RTH_LADDER'						=> 'RTH Ladder',
	'4ICON_LADDER'						=> 'RTH Ladder',
	'RTH'								=> 'RTH',
	'ELO_DESC'							=> '<strong>ELO:</strong> Le système ELO c’est la même chose que classement du monde des d’échecs. Les points de victoire et de perte sont attribuées compte tenu de la position de l’équipe sur ladder.',
	'SWAP_DESC'							=> '<strong>SWAP:</strong> Si le gagnant est l’équipe avec le moins de points marquer, la position de tous les joueurs seront échangées.',
	'RTH_DESC'							=> '<strong>RTH:</strong> Tous les équipes commencent avec 50 points et se termine lorsque l’équipe a obtenu 1000 points. Si le gagnant est celui avec le moins de point, il récupère 50% des points marquer aux autres équipes, 35% si il a marquer le même nombres de points et 20% si la team marque plus des points.',
	'CHICKEN_RISK_EXPLAIN'				=> 'Si vous voyez cette icône près d’un nom de Challanger pour ce ladder vous risqué d’être désigné comme le poulet.
	Si vous refusez, vous perdrez 25% de vos points. Si vous obtenez 3 Rapport de poulet, un logo de poulet sera affiché dans votre page publique du clan (pour ce ladder) et dans la page du rapport de subladder.',
	'POLLO_EXP'							=> 'Si une team ont ce logo près de son nom, il a refusé plus de 3 fois un défis par peur ... ahahah ...',
	'TROFEI'							=> 'trophée',
	'CHICKENALT'						=> 'Mauvais poulet',
	'CHICKENTXT'						=> 'Cette team sont des poulet',
	'STREAKTEBALT'						=> 'Streak +10 trophée',
	'STREAKTEBTXT'						=> 'Cette team ont reçu un streak +10',
	'LADDERWONALT'						=> 'Ladder winner trophée',
	'LADDERWONTXT'						=> 'Cette team ont remporté un ladder',
	'PATENTEALT'						=> 'licence level',
	'PATENTETXT'						=> 'Team licence level',
	'APERTA_A_TUTTI'					=> 'Open to all',
	'APERTA_ALMENO_B'					=> 'Seulement les teams avec une licence B',
	'APERTA_SOLO_A'						=> 'Seulement les teams avec une licence A',
	'LADDER_LIMIT'						=> 'Place limiter',
	'LICENZA_INSUFFICIENTE'				=> 'Je suis désolé, mais ce ladder est réservé à une licence supérieur ou égal à<strong>%s</strong>.<br />
	Votre team ont une licence <strong>%s</strong>.<br/>%sGO BACK!%s',
	'LICENZA_USER_INSUFFICIENTE'		=> 'Je suis désolé, mais ce ladder est réservé à une licence supérieur ou égal à<strong>%s</strong>.<br />
	vous avez une licence <strong>%s</strong>.<br/>%sGO BACK!%s',
	'LICENCE'							=> 'Ladder licence',
	'POLLI_PRESI'						=> 'Be Chicken times',
	'POWNERALT'							=> 'Powner logo',
	'POWNERTXT'							=> 'Powner award',
	'POWNS_PRESI'						=> 'Powns award',
	'LADDER_WINNER_IS'					=> 'RTH ladder won par: ',
	'LICENZA_A'							=> 'License A',
	'LICENZA_B'							=> 'License B',
	'LICENZA_C'							=> 'License C',
	'SELECT_CLANS'						=> 'Sélectionnez un match de team',
	'SPECIFICA_LADDER'					=> 'Vous devez sélectionner un match de ce ladder!',
	'EDITMATCH_NO_CLAN'					=> 'Vous devez sélectionner une team pour jouer ce match!',
	'EDITMATCH_SAME_CLAN'				=> 'Vous avez sélectionné la même team deux fois!',
	
	'MATCH_ID'							=> 'ID match',
	'LATEST_MATCHES_REPORTED'			=> 'Dernier match jouer (et confirmé) pour ces deux team',
	'MATCH_NON_RESETTABLE'				=> 'Ce match ne peut pas être remise à zéro.',
	'NO_MATCH_RECURSIVE'				=> 'Aucun resultat transmis pour cette team!',
	'MATCH_RESETTATO'					=> 'Match correctement remis à zéro.',
	'REPORTER'							=> 'Rapport par:',
	'MATCH_UPDATED'						=> 'Match correctement mis à jour.',
	'NO_MATCH_RECURSIVE_USER'			=> 'Toutes les correspondance trouvée pour cette team dans ce ladder!',
	'LATEST_MATCHES_REPORTED_USER'		=> 'Dernier match rapporté et confirmé pour ces deux teams',
	
	'TEAM'								=> 'Team',
	'EDIT_RIVALS_CONTESTED_MATCH'		=> 'Edité un match contesté',
	'EDIT'								=> 'Edité',
	'MID_NON_IMPOSTATA'					=> 'ERREUR! aucun match sélectionné!',
	'YOU_CANT_EDIT_THAT_MATCH'			=> 'Vous ne pouvez pas éditer ce match!',
	'LOG_MATCH_EDITED'					=> '<strong>Match ID:%s edité.</strong><br />» %s vs %s',
	'ULTIME_AZIONI_MODERATORI'			=> 'Dernières actions des modérateurs',
	'MOD_STRAIGHT'						=> 'Restreindre la modération matchs',
	'MOD_STRAIGHT_EXPL'					=> 'L’activation de cette option limite la capacité d’un modérateur à leur propre action pour ce ladder. Si ce n’est pas activée, les modérateurs peuvent modérer tous les ladders.',
	'MATCHES_CLANS'						=> 'Teams matchs',
	'MATCHES_USERS'						=> 'Utilisateur matchs',
	'RESULT'							=> 'Résultat',
	'WINNERIS'							=> 'win',
	'09'								=> '#^09',
	
	'IMG_CLAN_CHART'					=> 'Dernier matchs de la team',
	'IMG_USER_CHART'					=> 'Dernier matchs du joueur',
	'IMG_SUBLADDER_RULES'				=> 'Règles du Ladder',
	'IMG_REQUEST_JOIN'					=> 'Rejoindre La team',
	'IMG_CHALLANGE'						=> 'Défier cette team',
	'UCP_RIVALS'						=> 'Modifier la team',
	'SELECT_CLAN'						=> 'Selectionner la team',
	'STATS_UPDATED'						=> 'MVP et statistiques des utilisateurs mis à jour.',
	
	'REMOVE_FROM_LADDER'				=> 'Retirer le ladder?',
	'OLD_SEASONS'						=> 'Ancienne saison',
	'CURRENT_SEASONS'					=> 'saison en cours',
	'HIBERNATION'						=> 'Hibernation',
	'FROSTED_STATUS_ACTIVATE'			=> '<strong>A partir de maintenant vous avez le statut Hibernate pour ce ladder et vous ne perdez pas le point de délai d’inactivité, 
	mais attention: si vous restez comme ça pendant un maximum de 5 semaines, vousserrez supprimer du ladder<br />Pour repasserez vous-même dans l’état actif simplement en acceptant un challenge ou manuellement dans votre team CP.</ strong><br /> Si vous étiez sur la première place du ladder vous descendrez à la seconde.',
	'ON'								=> 'On',
	'GIORNI'							=> ' Jour ',
	'MINUTI'							=> ' minutes ',
	'ORE'								=> ' Heures ',
	'SECONDI'							=> ' seconds ',
	'CLAN_FROSTED'						=> 'Vous êtes actuellement en hibernation sur',
	'TI_RIMANGONO'						=> 'rester:',
	'DEFROST'							=> 'Dehibernate',
	'SBLOCCA'							=> 'Dehibernate',
	'FROSTED_STATUS_REMOVED'			=> 'Hibernated status retiré des ladders choisies',
	'HIBERNATION_COST'					=> 'Montant des points pour le commutateur de l’état de mise en veille prolongée',
	'HIBERNATION_COST_EXPL'				=> 'Sur le ladder RTH le montant est toujours de 25 points.',
	'LADDER_KICKOUT_TIME'				=> 'Limite dans les <strong>jours </ strong> pour cause d’inactivité.',
	'LADDER_KICKOUT_TIME_EXPL'			=> 'Temps Max <strong>en jours</ strong> pour qu’un challenge soit accpeté, sinon la team sera éliminé avec des points de pénalités.',
	'LADDER_KICKOUT_POINTS'				=> 'Points retirés pour cause d’inactivité',
	'LADDER_KICKOUT_POINTS_EXPL'		=> 'Sur le ladder RTH le montant est toujours de 10% des points.',
	'DELETE_AVATAR'						=> 'supprimer l’image',
	'REMOVE_FROSTING'					=> 'Retirez le statut hibernate',
	'DOUBLE_CHECK'						=> 'Vous ne pouvez pas accepter ou de refuser l’ensemble!<br />%sGO BACK!%s',
	'USER_PENDING_COMPLETE'				=> 'Dans l’attente de la fin de gestion des utilisateurs',
	'PMPENDINGMEMBERTXT_DECLINED'		=> 'L’utilisateur %s à refusé l’invitation dans votre team. Désolé.',
	'BBCODE_SMILE_ACTIVE'				=> '<em>BBCODE and SMILES active</em>',
	'SMSG_DESC'							=> 'Utilisez ce formulaire pour ajouter un message court (max 250 characters) qui apparaît dans votre page profil team.',
	'CHARACTER_LEFT'					=> 'caractère à gauche',
	'ENTER_SMSG_TEXT'					=> 'Le texte du message court ne peut pas être vide!',
	'SMSG_INSERTED'						=> 'Message inséré avec succès.',
	'LATEST_SMSG'						=> 'Derniers messages ajoutée',
	'GROUP_CHAT'						=> 'Team’s chat',
	'LOG_SMSG_REMOVED'					=> 'Message à supprimer de<strong>%s</strong> Team.',
	'TOURNAMENT_USERLEADERBOARD'		=> 'Classement d’utilisateurs du tournoi',
	'GAME'								=> 'Match ',
	
	'FIRST_HOME'						=> 'L’équipe qui doit jouer à la maison le premier match est',
	'REPORT'							=> 'signaler',
	'ANDATA'							=> 'Premier match',
	'RITORNO'							=> 'Second Match',
	'CANT_MENAGE_THIS_MATCH'			=> 'Vous ne pouvez pas gérer ce match!<br />%sGO BACK!%s',
	'INFOS'								=> 'Infos',
	'CONTEST'							=> 'Contesté',
	'TOURNAMENT_HOMEAWAY'				=> 'Domicile / Extérieur elimination',
	'TOURNAMENT_ADVSTATS'				=> 'système stats FPS avancéeactifs',
	'TOURNAMENT_USERBASED'				=> 'membres du système Tournoi',
	'CLAN_BASED'						=> 'fonction de la team',
	'USER_BASED'						=> 'fonction de l’utilisateur en (1on1)',
	'LICENZE_LIMIT'						=> 'License limitation',
	'APERTA_A_C'						=> 'License C - Open for all',
	'TOURNAMENT_HOMEAWAY_SHORT'			=> 'Domicile / Extérieur',
	'APERTA_B_A'						=> 'License A o B demandé',
	'APERTA_A'							=> 'License A demandé',
	'TOURNAMENT_LOGO'					=> 'Tournoi logo',
	'TOURNAMENT_LOGO_EXP'				=> '<em>L’image doit avoir une résolution de 200x80 px a.r.</em>',
	'USER_NOTINVITED'					=> 'Tournoi sur invitation seulement et vous ne pouvez pas le rejoindre!',
	'SUBSCRIPTED_AT_NOW'				=> '<strong>Ce sont inscrit maintenant:</strong>',
	'CLAN_WINS_RATIO'					=> 'Ratio wins/losses',
	'PLAYED'							=> 'Matchs jouer',
	'ALLTIME_STATS'						=> 'Les statistiques des matchs',
	'TOTAL_RATIO'						=> 'General ratio',
	'CLAN_XP'							=> 'Team XP',
	'REPUTATION'						=> 'Team’s reputation',
	'VOTA_COMPORTAMENTO_CLAN'			=> 'vote adversaire',
	'REPUTATION_1VS1'					=> 'La réputation de l’utilisateur',
	'RIPORTA_RISULTATO_DI'				=> 'Vous rapportez le match: ',
	'ADVANCED_STATS'					=> 'Stats avancer',
	'DONATE_US'							=> 'Si vous aimez ce script s’il vous plaît nous faire un don!',
	'REPORTER_SAME_YOU'					=> 'Vous ne pouvez pas confirmer un match que vous avez deja signalé!<br />%sGO BACK!%s',
	'MCP_ADVSTATS_LGND'					=> 'Laissez 0 dans tous les champs des utilisateurs pour le rapport de l’utilisateur.',
	'CHAT_DESC'							=> 'Utilisez cette discussion pour parler avec l’organisateur de ce match.',
	'CLAN_CHAT'							=> 'Match chat',
	'LADDER_STAFF'						=> 'Ladder staff',
	
	'MOD_CHATWRITE'						=> '[SYSTEM] Mods envoyer une réponse',
	'MOD_CHATWRITETEXT'					=> 'Le staff vous à envoyer un message dans le chat Match contestée. <br /> Allez dans la section Chat match dans votre Panneau de configuration team. Merci.',
	'ENTRA_CHAT'						=> 'JOIN CHAT',
	'MOD_CHATWRITE_USER'				=> '[SYSTEM] Nouveau message sur le Chat Match',
	'MOD_CHATWRITETEXT_USER'			=> '%s vous à envoyer un nouveau message sur le chat match.<br />>Allez dans la section Chat match dans votre Panneau de configuration team. merci.',
	'GAMES'								=> 'Games:',
	
	'CLAN_CONVERTOR_FOR_20'				=> 'RivalsMod 2.0 convertor tool for Clans',
	'CLAN_CONVERTOR_FOR_20_LEGEND'		=> 'Avec cet outil, vous pouvez importer votre profil de team et les membres d’un ladder avec le MOD phpRivals <= 1.4 vers le nouveau système 2.0.<br />
	Lorsque le processus est terminé, cocher si toutes les données a été correctement importé, puis supprimez anciens clans de ACP GROUPS MANAGEMENT.<br />Supprimer ce fichier php quand tout est fini.',
	'ALREADY_CLAN_IN_NEW_TABLE'			=> 'Les clans sont déjà établi en nouvelle table. Peut-être que vous l’avez déjà importés.',
	'IMPORTATION_FINISHED'				=> 'Tous les anciens clans sont importés aujourd’hui. Vérifiez si tout va bien,<br />aller sur <a href="%s">ACP Rivals page</a>.',
	'MATCHRESULTS'						=> 'Match résultat',
	'USER_NOT_IN_LADDER'				=> 'Vous devez joindre à un ladder basée sur la team ou un ladder basée sur l’utilisateur avant que vous puissiez créer un défi 1v1.',
	'CLAN_LADDER'						=> 'Team inscrit sur le ladder',
	'USER_LADDER'						=> 'Utilisateur inscrit sur le ladders',
	'MINS'								=> 'Minutes',
	'MATCH_PLAYED_AT_TODAY'				=> 'Nombres de Matchs joués: ',
	'RANDOM_MAP'						=> 'Map du jour',
	'LATEST_WAR'						=> 'Derniers matchs joués',
	'CLAN_MATCH'						=> 'Matchs en équipes',
	'USER_MATCH'						=> 'Matchs de l’tilisateur',
	'CLAN_FULL'							=> 'Les teams complètent la liste',
	'LIST_ONLY_ACTIVE'					=> 'Trop de Team? Utilisez la liste simplifiée avec les teams actif par le filtre du ladder!',
	'CLAN_FILTERED'						=> 'Liste de team Filtrée',
	'RESUME_DESC'						=> 'Dans cette page vous pouvez reprendre une team fermé ou bannis. Pour le faire, vous devez choisir le nouveau équipe-propriétaire (peut être le même précédent). 
	<br />Tous les autres utilisateurs  seront remis à zéro, puis la nouvelle équipe-propriétaire peut réaffecter chacun un niveau correct',
	'RESUME'							=> 'reprendre',
	'MOSTRA_SOLO_CHIUSI'				=> 'Afficher uniquement le clan fermé ou bannis',
	
	'TOTALS'							=> 'Totals',
	'ACTIVES'							=> 'Actif',
	'CLOSED'							=> 'Fermer / Bannis',
	'CONTESTED_MATCHES'					=> 'Matchs contester',
	'PENDING_MATCHES_USER'				=> 'recherche de match 1on1',
	'ONGOING_MATCHES_USER'				=> 'Match 1on1 en cours',
	'FINISHED_MATCHES_USER'				=> 'Signalés matchs 1on1',
	'CONTESTED_MATCHES_USER'			=> 'Contester matchs 1on1 ',
	'ONGOING_TOURNAMENTS'				=> 'Tournanoi en cours',
	
	'EMPTY'								=> 'Nul',
	'ICON_DECERTO_NO'					=> 'Decerto système désactiver',
	'ICON_DECERTO'						=> 'Decerto système activer',
	'ICON_CPC_NO'						=> 'CPC système désactiver',
	'ICON_CPC'							=> 'CPC système activer',
	'ICON_SOCCER_NO'					=> 'Football système désactiver',
	'ICON_SOCCER'						=> 'Football système activer',
	'1VS1LADDER_NO'						=> 'Ladder team based',
	'1VS1LADDER'						=> '1on1 ladder USER based',
	'RTH_LADDER_NO'						=> 'Standard ladder, RTH système désactiver',
	'RTH_LADDER'						=> 'RTH ladder, RTH système activer',
	'ICON_ADVSTATS_NO'					=> 'Stats avancer désactiver',
	'ICON_MVP_NO'						=> 'MVP système désactiver',
	
	'PM_TOURNAMENT_TXT'					=> 'le tournoi [b]%s[/b] est démarrer, vérifier dans votre panel team pour savoir contre qui vous devez jouer .<br />Merci.',
	'CHAT'								=> 'Chat',
	'CLAN_TOT_LEVEL'					=> 'Team level',
	'LEVEL'								=> 'Level',
	'PARI'								=> 'Draw',
	'NO_LEFT_LADDER'					=> 'Désolé mais vous avez joué plus de 1 match dans ce ladder, donc vous ne pouvez pas quitter désormais. Si vous voulez jouer de nouveau, hiberne vous-même.<br />Ici il n’existe pas de tableau de bord...',
	
	'LADDER_DELETE_TXT'					=> 'Etes-vous sûr de vouloir supprimer ce ladder?<br />Toutes les statistiques et subladder sera supprimé aussi.<br />
Si les utilisateurs jouer un match dans ce subladder. la meilleure façon est de bloquer le ladder!.<br /><br />
<p class="rvcenter">%sNO%s - %sYES%s</p>',
	'SUBLADDER_DELETE_TXT'				=> 'Etes-vous sûr de vouloir supprimer celadder?<br />Toutes les stats et les matches seront aussi supprimés.<br />
Si les utilisateurs jouer un match dans ces subladder la meilleure façon est de bloquer le ladder!.<br /><br />
<p class="rvcenter">%sNO%s - %sYES%s</p>',

	'LEAVE_YOU'							=> 'Retirez-vous',
	'REMOVED_FROM_TOURNAMENT'			=> 'L’inscription à ce tournoi sont supprimés avec succes',
	'TOURNAMENT_NAME_EMPTY'				=> 'Le nom du tournoi ne peut pas être vide!',
	'TOURNAMENT_MINUSER_0'				=> 'Tournoi limité “Min team membres” Ne peut pas être vide!',
	'TOURNAMENT_MAXUSER_0'				=> 'Tournoi limité “Max team membres” Ne peut pas être vide!',
	'TOURNAMENT_MIN_MBR'				=> 'Min de membres de team',
	'TOURNAMENT_MAX_MBR'				=> 'Max de membres de team',
	'TOURNAMENT_STRICTED'				=> 'Stricted tournament',
	'TOURNAMENT_USER_NOT_STRICTED'		=> 'Sur la base utilisateur le tournoi ne peut pas être  trop restreint!',
	'ZERO_FREE'							=> 'Laissez 0 pour illimité',
	'TOURNAMENT_MEMBERS_FAILED'			=> 'Désolé, votre team ne répond pas aux membres requis de %s pour rejoindre ce tournoi.',
	'NO_ROSTER_FOR_CLAN'				=> 'Votre team ont trop de membre du maximum autorisée pour ce tournoi. Pour participer à, vous devez créer une LineUP Roster avec un minimum de %s membres et un maximun de %s membres.',
	'TOURNAMENT_SIGN_UP_ROSTERS'		=> 'Rosters tournoi inscrivez-vous ',
	'NO_ONE_COMPATIBILE_ROSTER'			=> 'Vous n’avez pas Liste compatibile LineUp. Faire ou modifier une Liste Roster LineUP  afin de respecter le nombre de membres:',
	'ROSTER_ALREADY_IN'					=> 'Roster LineUP déjà abonné à ce tournoi!',
	'TOURNAMENT_REMOVE_ROSTERS'			=> 'Retirer le Rosters du tournoi',
	'ONLY_STRICTED'						=> 'Seulement avec le mod restreint',
	'LINEUP_SHORT'						=> 'LineUP',
	'BEFORE_REFRESH'					=> 'secondes avant rafraîchissement',
	'NOSHOW'							=> 'pas joué',
	'TOURN_CLAN_NOT_GOOD'				=> 'Toute correspondance entre ces identifiants sont trouvés dans ce tournoi!',
	'EDIT_MATCH_TOURNAMENT'				=> 'Gérer les matchs des tournois',
	'SELECT_WHO_WIN_THIS_ROUND'			=> 'Définir un gagnant par le staff pour un round',
	'MATCH_UP_DONE'						=> 'Victoire correctement assignés.',
	'YOUR_VERSION'						=> 'Nouvelle version de MOD phpRivals est sorti! Le vôtre est ',
	'LATEST_VERSION'					=> 'et le dernier est ',
	'UPDATE_NOW'						=> 'Téléchargez dès maintenant la nouvelle version et mettre à jour',
	
	'HIBERNATION_CONFIRM_TXT'			=> '<p class="rvcenter">Le statut d’hibernation vous permettre de ne pas accepter ou de refuser match sans perd des points.
Est une bonne chose à faire si vous ne pouvez pas jouer un match pendant un certain temps.<br />Pour supprimer ce statut, vous ne doit accepter d’un match ou utilisez le formulaire dans le clan CP à domicile.<br />
Cet action vous retirera<strong>%s points</strong>.Si vous êtes le n ° 1 sur l’échelle, pour empêcher l’action malveillante, rendez-vous de l’état de mise en veille prolongée vous mettra en deuxième position de réglage que vous marquerez.
<br /><br />%s - %s</p>',
	'ANNULLA'							=> 'annuler',
	
	'PM_MATCH_UNCONFIRM_ADV'			=> '[SYSTEM] Alert de non confirmation de match',
	'PM_MATCH_UNCONFIRM_ADV_TXT'		=> 'Vous n’avez pas a confirmé ou contesté le match contre %s. <br /> Vous avez 24 heures pour confirmer, autrement, le système le fera pour vous.',
	'ACCEPTED'							=> 'Accepté',
	'REPORTED'							=> 'rapporté',
	'DA'								=> ' par',
	'UNREPORTED'						=> 'non déclarée',
	
	'MAX_HOURS'							=> 'Temps Max avant confirmation',
	'MAX_HOURS_EXPL'					=> 'Temps Max <strong>en heures</strong> utilisateur doit confirmer le report d’un match.',
	'MIN_POST'							=> 'Messages min pour permettre à l’utilisateur de jouer',
	'BANNED_GROUP'						=> 'Définissez les groupes interdits (facultative)',
	'DONOTUSE'							=> 'Ne pas utiliser ce',
	'USER_CANT_PLAY'					=> 'Je suis désolé mais vous ne pouvez pas jouer. Pour jouer, vous devez:<br />- vous inscrire<br />- Ne pas être bannis<br />- ecrire ou répondre à %s messages',
	'SEL_USER_CANT_PLAY'				=> 'Je suis désolé, mais l’utilisateur sélectionné ne peut pas jouer. Pour jouer, vous devez:<br />- vous inscrire<br />- Ne pas être bannis<br />- ecrire ou répondre à %s messages',
	'SELECT_USERS_MATCH'				=> 'Sélectionnez les joueurs du match',
	'CONFIRMED_MATCHES'					=> 'matchs confirmés',
	'GUID_CHARA'						=> '(utiliser0->9 & a->z)',
	'UAC_CHARA'							=> '(utiliser 0->9)',
	'GUID'								=> 'GUID',
	'UAC'								=> 'UAC',
	'RANDOM_GEN'						=> 'Générer',
	'UAC_USED'							=> 'L’UAC que vous avez choisi est déjà assigné à d’autre team. Effectuer le changement.',
	'GUID_USED'							=> 'Le GUID que vous avez choisi est déjà assigné à d’autre team. Effectuer le changement.',
	'UAC_NON_SIX'						=> 'Invalide UAC. Vous devez utiliser seulement des nombres et doit être de 6 charactère.',
	'GUID_NON_ALPHANUM'					=> 'Invalide GUID. Vous devez utiliser seulement des nombres et lettres, et doit être de 6 charactère.',
	'BEST_POSITION'						=> 'Meilleur place',
	'STATS_LADDER'						=> 'Ladders stats',
	'STATS_TOURNAMENT'					=> 'Tournoi stats',
	'USER_TOURNAMENT_HISTORY'			=> 'Tournoi 1on1 historique',
	'PLATFORM_NAME_EMPTY'				=> 'Le nom de la league ne peut pas être vide!',
	
	'ANY_MAPS_SAVED'              		=> 'Pack de maps créé',
    'EMPTY_RANDOM_MAP_FIELD'       		=> 'Le nom de jeu et le Jeu shor le nom doivent être remplis!',
    'RANDOM_ADDED_NOIMGS'          		=> 'Jeu aléatoire sans un mapset a ajouté. Vous devez ajouter au moins une image manuellement avant que vous ne puissiez l’utiliser en public.',
    'ADD_MAP_MANUALLY'             		=> 'Ajoutez une map manuellement à ce jeu',
    'MAP_DISALLOWED_EXTENSION'     		=> 'Vous avez chargé comme carte une extension de fichier invalide. Nous permettons seulement des fichiers .gif .png. .jpg!',
    'MAP_WRONG_FILESIZE'           		=> 'Vous avez chargé des images trop grandes : Max 80KB',
    'MAP_WRONG_SIZE'              		=> 'Utilisez s’il vous plaît une image avec les dimensions de300x170px.',
    'MAP_DISALLOWED_CONTENT'       		=> 'Fichier non permis.',
    'MAP_PARTIAL_UPLOAD'           		=> 'Téléchargement inachevé.',
    'MAP_GENERAL_UPLOAD_ERROR'     		=> 'Erreur de téléchargement',
    'LOGOMEN_GENERAL_UPLOAD_ERROR' 		=> 'Erreur de téléchargement',
    'IMG_GENERAL_UPLOAD_ERROR'     		=> 'Erreur de téléchargement',
    'MAP_NOT_UPLOADED'             		=> 'Le processus du chargement d’image de map a échoué.',
    'MAP_MANUALLY_ADDED'           		=> 'Map ajouté avec succès dans le mapset.',
    'ADD_MAP_MANUALLY_DESC'        		=> 'Vous pouvez ajouter manuellement une carte à ce pack de cartes. Le système n\accepte que le format png, jpg, gif <strong> </ strong>, max 80Ko, avec la dimension de 300x170px.',
    'ANY_GAMES_SAVED'              		=> 'Tous les jeux sauvegardés',
    'DECERTO_ACTIVATION_DESC'			=> 'Pour utiliser un système de CPC avec un ladder, vous devez avoir enregistré au moins 3 cartes; pour un système decerto vous devez avoir enregistré au moins 3 cartes pour chacun des modes en rapport.',
	'ANY_MATCHES_SELECTED'				=> 'Vous n’avez pas sélectionné toutes les correspondances! Réveillez-vous!',
	'ANY_MATCHES_FOUNDED'				=> 'Pas de match à l’heure actuelle.',
	'REFRESH'							=> 'Recharger la page maintenant',
	'MAX_FOR_ROSTER'					=> 'Si vous définissez le nombre maximal de joueurs actifs par Alignements clan requis pour le système.',
	'PLAYERS'							=> 'Players',
	'ANY_MVP_LIST_ADDED'				=> 'Pas de MVP classement créé.',
	
	//Page Header addon
	'PROFILE_GROUP'						=> 'Team profil',
	'CLAN_LIST'							=> 'Liste des clan',
	'TOURNAMENTS'						=> 'Tournois',
	
	//Leagues addon start
	'NO_LEAGUES'					=> 'Pas de ligues créé.',
	'ADD_NEW_LEAGUE'				=> 'Gérer les ligues',
	'LEAGUE_NAME'					=> 'Nom de la ligue',
	'LEAGUE_LOGO'					=> 'Logo de la ligue',
	'LEAGUE_LOGO_EXP'				=> 'Image seulement autorisé jpeg, jpg, gif, png... 900x150 pxl.',
	'LEAGUE_DESC'					=> 'Ligue description',
	'BBCODE_ACTIVE'					=> '<em>BBcode et smiles actif</em>',
	'LEAGUE_STARTDATE'				=> 'Heure de début de la ligue',
	'LEAGUE_CYCLE'					=> 'Nombre de cycles de la ligue',
	'LEAGUE_WIN_SYSTEM'				=> 'Win signale système',
	'LAEGUE_ADVSTATS'				=> 'Stats dvancer',
	'LAEGUE_MVP'					=> 'MVP système',
	'LEAGUE_TYPE'					=> 'Ligue style',
	'LEAGUE_SHORTY'					=> 'Lien vers Decerto ou CPC',
	'LEAGUE_A_CLANS'				=> 'N° de team sur la Première Division',
	'LAEGUE_STRIGHT'				=> 'ligue restreint',
	'LAEGUE_STRIGHT_DESC'			=> 'Dans la ligue restreint le nombre de membres de team Max est actif. Si une team à un maximun de membres, il doit créer une nouvelle LineUP et la joindre; la lineup ne peut pas être changée jusqu’à la fin d’un cycle de ligue.',
	'LEAGUE_MIN_MBR'				=> 'Minimum de membres dans la team',
	'LEAGUE_MAX_MBR'				=> 'Maximun de membres dans la team',
	'LAEGUE_NETLIM'					=> 'Système de réseau de contrôle',
	'LAEGUE_STRIGHT_DESC'			=> 'Si le système de réseau de contrôle est actif quand une team rejoint le système et va vérifier tous les gamertags des membres et leur score réel.',
	'LEAGUE_MIN_NETSCR'				=> 'score minimum de utilisateur',
	'SAVED_LEAGUES'					=> 'Base de données de Ligues',
	'START_TIME'					=> 'Star time',
	'SUBSCRIBED'					=> 'Inscription',
	'LEAGUE_ADDED'					=> 'Ligue créé avec succès',
	'LAEGUE_ONEONE'					=> '1on1 ligue utilisateur',
	'UNACTIVE'						=> 'Inutilisée',
	'XBOX'							=> 'XboX Live',
	'PSN'							=> 'PSN',
	'STEAM'							=> 'Steam',
	'XFIRE'							=> 'XFire',
	'SETUP'							=> 'Setup divisions',
	'LEAGUE_UPDATED'				=> 'Ligue correctement mis à jour',
	'NAME_LEAGUE_EMPTY'				=> 'Le nom de la ligue ne peut pas être vide!',
	'SHOW_ALL'						=> 'Voir tous les',
	'SHOW_WAITS'					=> 'Afficher uniquement les joueur prêts à commencer',
	'SHOW_CURRENT'					=> 'Afficher uniquement ce en cours',
	'SHOW_CLOSED'					=> 'Afficher seulement ce fermé',
	'ICON_LIMIT_NO'					=> 'Pas de limite de réseau',
	'ICON_LIMIT_XBOX'				=> 'XboX Live limit',
	'ICON_LIMIT_PSN'				=> 'PSN limit',
	'ICON_LIMIT_STEAM'				=> 'Steam limit',
	'ICON_LIMIT_XFIRE'				=> 'XFire limit',
	'LEAGUES'						=> 'Ligues',
	'1on1LEAGUE'					=> 'Ligue basée sur 1on1 utilisateur ',
	'1on1LEAGUE_NO'					=> 'Teams basée sur la ligue',
	'ARCHIVED_LEAGUES'				=> 'Anciennes ligues',
	'USER_ROSTER_ALREADY_IN'		=> 'Ce membre est déjà dans la LineUP.<br />%sGO BACK!%s',
	'USER_ROSTER_ADDED'				=> 'L’utilisateur à été ajouté à la LineUP.',
	'ROSTER_ADDED'					=> 'Liste LineUP ajouter avec succes.',
	'ROSTER_DELETED'				=> 'Liste LineUP supprimer avec succes.',
	'ROSTER_UPDATED'				=> 'Liste LineUP mise a jour avec succes.',
	'SETUP_ROSTER'					=> 'Gérez la Liste LineUP',
	'CREA_UN_ROSTER'				=> 'Ajouter une nouvelle liste LineUP',
	'ROSTER_NAME'					=> 'Nom de la liste de la LineUp',
	'ADD_USER_TO_ROSTER'			=> 'Ajouter un joueur dans la LineUp',
	'MAKE'							=> 'Faites-le',
	'ROSTER_LIST'					=> 'Liste LineUP créer',
	'ROSTER_EXP'					=> 'LineUP level',
	'POINTS'						=> '<em>points</em>',
	'USER_EXP'						=> 'EXp joueur',
	'EDIT_ROSTER'					=> 'Edité le nom de la LineUP',
	'DELETE_ROSTER'					=> 'Supprimer la LineUP',
	'REMOVE_MBRS'					=> 'Supprimer les utilisateurs de la LineUP',
	'ROSTERS'						=> 'Listes des LineUP',
	'NO_ROSTERS'					=> 'Cet team n’a pas de liste de LineUp à aujourd’hui.',
	'LEADER'						=> 'Leader',
	'SET_LEADER'					=> 'Ajouter un leader',
	'ROSTER_LEADER_TEXT'			=> 'Le leader peut signalé/confirmer les résultat dans les tournois et les ligues où la liste se joue comme le clan principal team-owner et warranger.',
	'ROSTER_IN_COMPETITION'			=> 'Liste sélectionnée jouent actuellement à un tournoi en cours ou de la ligue, de sorte que vous ne pouvez pas changer la LineUp jusqu’à ce qu’elle soit terminée..<br />%sGO BACK!%s',
	'ROSTER_IN_COMPETITION_DEL'		=> 'Liste sélectionnée jouent actuellement à un tournoi en cours ou de la ligue, de sorte que vous ne pouvez pas la supprimer.<br />%sGO BACK!%s',
	'ROSTER_NAME_EMPTY'				=> 'Le nom de la Roster LineUP ne peut pas être vide!',
	'ROSTER_NAME_USED'				=> 'Le nom de la Roster LineUP vous avez entré est déjà en cours d’utilisation.',
	//Leagues addon end
	
//##############################################################################################################################################
// BASE VERSION START FROM HERE --->
	'ADMIN_NO_ONGOING'				=> 'La team n’a pas de matchs en cours.',
	'ALL'							=> 'All',
	'ADD_GROUP_TO_LADDER'			=> 'Ajouter une team au Ladder',
	'ADD_GROUP_TO_TOURNAMENT'		=> 'Ajouter une team au Tournoi',
	'ARCHIVED_TOURNAMENTS'			=> 'Archive Tournois',
	'ARCHIVE_TOURNAMENT'			=> 'Archive Tournoi?',
	'ACCEPT'						=> 'Accepter',
	'ADD_CHALLENGE'					=> 'Ajouter un défi',
	'ADD_GROUP'						=> 'Créer une équipe',
	'ADD_LADDER'					=> 'Ajouter un Ladder',
	'ADD_PLATFORM'					=> 'Ajouter une ligue',
	'ADD_SUBLADDER'					=> 'Ajouter un Sub-Ladder',
	'ADD_TOURNAMENT'				=> 'Ajouter un Tournoi',
	'ADD_TO_MATCH_FINDER'			=> 'Ajoutez votre team dans la recherche de Match',
	'ALREADY_IN_LADDER'				=> 'Votre team à déjà rejoint ce ladder.',
	'ALREADY_IN_TOURNAMENT'			=> 'Votre team est déjà asigné à ce tournoi',
	'ASSIGN_AS_BACKUP'				=> 'Attribuer en tant que backup leader',
	'ASSIGN_AS_LEADER'				=> 'Attribuer en tant que leader',

	'BRACKETS_CANT_GENERATE'		=> 'Les brackets ne peuvent pas être produites parce que tous les teams ne se sont pas inscrits encore ou le tournoi n’a pas encore été lancé par l’administrateur.',
	'BEST_RANK'						=> 'Meilleur rang',
	'BYE_GROUP'						=> 'BYE Team utilisé en tournois.',

	'CANT_INVITE_YOURSELF'			=> 'Vous ne pouvez pas vous inviter!',
	'CHALLENGE'						=> 'défis',
	'CHALLENGES_UPDATED'			=> 'Les défis ont été mis à jour.',
	'CHALLENGE_ADDED'				=> 'Le défi a été envoyer.',
	'CHEATER'						=> 'Essai de triche? Parce que vous ne pouvez pas vous défier!',
	'CHEATING'						=> 'Triche',
	'CHALLENGE_UNRANKED'			=> 'Unranked',

	'GROUP_NOTINVITED'				=> 'Votre groupe n’a pas été invité à rejoindre ce tournoi.',
	'GROUP_MOVED'					=> 'l’équipe a été déplacé vers le nouveau ladder.',
	'GROUP_ADDED'					=> 'l’équipe a été créé, vous pouvez maintenant vous connecter à votre team.',
	'GROUP_REMOVED_TOURNAMENT'		=> 'l’équipe a été retiré de la compétition.',
	'GROUP_DESC'					=> 'Team Information',
	'GROUP_ID'						=> 'Team ID',
	'GROUP_INFORMATION'				=> 'Team Information',
	'GROUP_LADDER'					=> 'Team Ladder',
	'GROUP_LEADER'					=> 'Team Leader',
	'GROUP_LOGIN'					=> 'Team Login',
	'GROUP_LOGO'					=> 'Team Logo',
	'GROUP_LOSSES'					=> 'Team Losses',
	'GROUP_MEMBERS'					=> 'Joueur de l’équipe',
	'GROUP_NAME'					=> 'Nom de l’équipe',
	
	
	
	
	'GROUP_NOTIN_LADDER'			=> 'Sorry, before you can use this feature, you must first join a ladder.<br /><br />%sGo back%s!',
	'GROUP_NOTSIGNED_UP_LADDER'		=> 'Your clan is not a part of this ladder. You can not sign up.',
	'GROUP_SCORE'					=> 'Team Score',
	'GROUP_SIGNED_UP'				=> 'Your clan is now signed up to the tournament.',
	'GROUP_STREAK'					=> 'Team Streak',
	'GROUP_TAG'						=> 'Team Tag',
	'GROUP_UPDATED'					=> 'The clan has been updated.',
	'GROUP_WINS'					=> 'Clan Wins',
	'CONFIGUREATION_UPDATED'		=> 'The configuration has been updated.',
	'CONFIGURE_RIVALS'				=> 'Configure Rivals',
	'CURRENT_RANK'					=> 'Rank',
	'CURRENT_SEASON'				=> 'Current Season',
	'CONFIRM_WIN'					=> 'Confirm Win',
	'CONTEST_RESULT'				=> 'Contest Result',
	'CONFIGURATION_NOT_COMPLETE'	=> 'You have not set your forum’s BYE clan. Please do so on the Rivals configure page.',
	'DECLINE'						=> 'Decline',
	'DETAILS'						=> 'Details',
	
	// MOD
	'CURRENT_CLAN_LOGO'             => 'Logo Team',
	
	'ELO'							=> 'ELO',
	'EDIT_BRACKETS'					=> 'Edité Brackets',
	'END_SEASON'					=> 'Finir la saison',
	'EDIT_GROUP'					=> 'Edité team',
	'EDIT_SEASON'					=> 'Edité Saison',
	'EDIT_GROUPS'					=> 'Edité les teams',
	'EDIT_FINISHED'					=> 'Edité Matchs terminé',
	'EDIT_TOURNAMENT'				=> 'Edité Tournoi',
	'EDIT_TOURNAMENTS'				=> 'Edité les tournois',
	'ENTER_GROUP_NAME'				=> 'Vous devez entrer un nom de team avant de créer votre team.',
	'EXTENSION_NOT_ALLOWED'			=> 'L’extension du fichier que vous tentez de télécharger n’est pas autorisé.',
	'EXTRA'							=> 'Details',
	'FILTER'						=> 'Filtrer',
	'FINAL_ROUND'					=> 'Finals',
	'FIND_GROUP'					=> 'Recherche une team',
	'FINISHED'						=> 'Terminé',
	'FINISHED_MATCHES'				=> 'Terminé Matchs',
	'INSTALLER_COMPLETE'			=> 'l’installation Mod phpRivals est maintenant compléter.
	                                    Assurez-vous de lire et de suivre les instructions trouvées dans install.xml pour apporter les modifications de fichiers propres à votre base phpBB.
                                        Vous pouvez maintenant supprimer le répertoire d’installation.
                                        <br /><br />
                                        Merci d’avoir choisi Mod phpRivals,
                                        Soshen',
	
	'INVITE_ONLY'					=> 'Seulement sur invitation',
	'INVITED_CLANS'					=> 'Inviter un joueur en inscrivant son IDs (un par ligne)',
	'INSTALLER_FOUNDER'				=> 'Désolé, vous devez être le Team-Owner de l’équipe pour exécuter ce script d’installation.',
	'INSTALLER_MF'					=> 'Un ou plusieurs modules failed to install. S’il vous plaît supprimer tous les modules phpRivals Mod de votre ACP de phpBB.',
	'INSTALL_OR_UPGRADE'			=> 'Vous souhaitez installer ou mettre à jour Rivals? Cliquez sur Oui pour "Install" ou le n° de "mise à niveau".',
	'INVITE_USER'					=> 'Inviter un utilisateur',
	'ISSUE_A_TICKET'				=> 'Envoyer un Ticket',

	'JOINED_GROUP'					=> 'Vous faites maintenant parti de cet team. Bienvenue! <br /> <em>Votre Session sera restauré </em>',
	'JOINED_WITH_LADDER'			=> 'Votre clan a désormais rejoint ce ladder.',
	'JOIN_LADDER'					=> 'Rejoindre ce ladder',
	'GROUP_AVATAR'                  => 'Group Avatar',
	'CHALLENGE_IMG'                 => 'défier',
	'REQUEST_TO_JOIN_IMG'           => 'Demande pour rejoindre la team',

	'LADDER_STRING_FORMAT'			=> 'P: %s, L: %, S: %',
	'LAST_RANK'						=> 'Dernier rang',
	'LADDER_RANKING'				=> 'Ladder Ranking Système',
	'LADDER'						=> 'Ladder',
	'LADDER_JOIN_LOCKED'			=> 'Désolé, vous ne pouvez pas quitter ce ladder. Il a été verrouillée par l’administrateur.',
	'LADDERS'						=> 'Ladders',
	'LADDER_ADDED'					=> 'Le ladder a été ajouté.',
	'LADDER_GROUP_ID'				=> 'ID',
	'LADDER_CL'						=> 'Afficher le lien du défis de ce Ladder?',
	'LADDER_TYPE'					=> 'Ladder Type',
	'LADDER_RESET_STATS'			=> 'Réinitialiser les statistiques?',
	'LADDER_RM'						=> 'Les membres obligatoires',
	'LADDER_GROUP_LOSSES'			=> 'Losses',
	'LADDER_GROUP_NAME'				=> 'Nom Team ',
	'LADDER_GROUP_SCORE'			=> 'Score',
	'LADDER_GROUP_STREAK'			=> 'Streak',
	'LADDER_GROUP_WINS'				=> 'Wins',
	'LADDER_MOVED'					=> 'La position du ladder’s a été déplacé.',
	'LADDER_NAME'					=> 'Nom du Ladder',
	'REQUIRED_MEMBERS_FAILED'		=> 'Désolé, votre team ne répond pas aux membres requis de %s.',
	'LADDER_PARENT'					=> 'Parent Ladder',
	'LADDER_PLATFORM'				=> 'Ladder ligue',
	'LADDER_RULES'					=> 'Ladder Règles',
	'LADDER_UPDATED'				=> 'Le ladder a été mis à jour.',
	'LEAVE_LADDER'					=> 'Quitte ladder',
	'LEFT_LADDER'					=> 'Votre team a quitté le ladder.',
	'LEFT_TO_RIGHT'					=> 'De gauche à droite',
	'LOSER'							=> 'Looser',
	'LOSER_BRACKET'					=> 'Loser Bracket',
	'LOSS'							=> 'Loss',
	'LADDER_RADIO_LOCKED'			=> 'Débloqué',
	'LADDER_RADIO_LOCKED2'			=> 'Bloqué',
	'LADDER_LOCKED'					=> 'Ladder Bloqué',

	'MANAGE_MEMBERS'				=> 'Gérer les membres',
	'MANAGE_TOURNAMENTS'			=> 'Gérer Tournois',
	'MATCHCOMM'						=> 'Message de team',
	'MATCHCOMM_MESSAGE'				=> 'Message',
	'MATCHCOMM_NOTE'				=> 'MatchComm mises à jour automatiquement toutes les 5 secondes. Soyez patient lors de la publication d’un message.',
	'MATCHCOMM_UNREAD'				=> 'messages non lus',
	'MATCHCOMM_WRONG_MATCH'			=> 'Votre team ne fait pas partie de ce match. Vous ne pouvez pas envoyer un message.',
	'MATCHES'						=> 'Matchs',
	'MATCH_FINDER'					=> 'Recherche de match',
	'MATCH_FINDER_ADDED'			=> 'Votre team a été ajoutée à la recherche de  match. <br /> Toute team qui vous met au défi sera affiché dans votre panel sous
                                        "<strong>notification de Matches</strong> ou <strong>Gérer match 1on1</strong>".',
	'MATCH_HISTORY'					=> 'Match Historique',
	'MATCH_REPORTED'				=> 'Le match a été rapporté.',
	'MATCH_RESULTS'					=> 'Match Resultats',
	'MATCH_WITHIN'					=> 'Match Within Next',
	'MEMBER_ASSIGNED_AS_BACKUP'		=> 'The member has been assigned as a backup leader.',
	'MEMBER_ASSIGNED_AS_LEADER'		=> 'The member has been assigned as the new leader.',
	'MEMBER_REMOVED'				=> 'The member has been removed.',
	'MOVE_DOWN'						=> 'Swap Down',
	'MOVE_RIGHT'					=> 'avancer',
	'MOVE_LEFT'						=> 'reculer',
	'MOVE_UP'						=> 'Swap Up',
	'MUST_ADD_PLATFORM'				=> 'Désolé, vous devez d’abord ajouter une ligue avant de pouvoir ajouter un ladder.',
	'MOVE_STATS'					=> 'Déplacer toutes les statistiques ainsi?',
	'MOVE_GROUP_LADDER'				=> 'Déplacez équipe du Ladder',
	'NO_REPORTED_MATCHES'			=> 'Il n’y a pas de matchs signalés.',
	'NO_UNREPORTED_MATCHES'			=> 'Il n’y a pas de matchs non déclarés .',
	'NONEXISTANT_GROUP'				=> 'L’équipe que vous avez demandée n’existe pas.',
	'NONEXISTANT_GROUP2'			=> 'L’équipe  que vous avez demandée n’existe pas dans le ladder choisie.',
	'NOT_READY'						=> 'Not Ready',
	'NO_ADMIN'						=> 'Désolé, vous ne pouvez pas accéder à cette page parce que vous n’êtes pas un administrateur de ce site.',
	'NO_ARCHIVED_TOURNAMENTS'		=> 'Il n’y a pas de tournois archivés.',
	'NO_GROUPSIN_LADDER'			=> 'Il n’y a actuellement pas d’équipes qui ont joints ce ladder.',
	'NO_GROUPS_FOUND'				=> 'Désolé, aucune des équipes ont été trouvés pour cette requête d’entrée.',
	'NO_GROUPS_MATCH_FINDER'		=> 'Il n’y a pas d’équipe ouverts pour un défi.',
	'NO_MATCHCOMMS'					=> 'Il n’y a actuellement aucune MatchComms. Votre équipe ne fait pas partie de toutes les correspondances en cours.',
	'NO_MATCHES'					=> 'Cette équipe n’a pas encore participé à aucun match.',
	'NO_MEMBERS'					=> 'Aucun membres font partie de cette équipe.',
	'NO_REPORT_READY'				=> 'Vous ne pouvez pas encore Signaler.',
	'NO_ONGOING_MATCHES'			=> 'Il y a actuellement aucun match en cours.',
	'NO_OTHER_GROUPS'				=> 'Vous n’avez pas d’autres équipes.',
	'NO_PENDING_MATCHES'			=> 'Il n’y a actuellement aucune défis en suspens.',
	'NO_PENDING_MEMBERS'			=> 'Aucun membres en attente.',
	'NO_SHOW'						=> 'No Show',
	'NO_SUBLADDERS'					=> 'Il n’y a pas de sub-ladders dans ce ladder.',
	'NO_TOURNAMENTS'				=> 'Aucun tournois en cours d’exécution.',
	'NUMBER_OF_GROUPS'				=> 'Nombre d’équipe',
	'NUM_GROUPS'					=> 'EQuipe',
	'ONGOING'						=> 'En cours',
	'ONGOING_MATCHES'				=> 'Matchs En cours',
	'OTHER'							=> 'autre',
	'OTHER_TYPE'					=> 'autre',
	'PENDING'						=> 'en attente',
	'PENDING_MATCHES'				=> 'Défis en attente',
	'PENDING_MEMBERS'				=> 'Membres en attente',
	'PLATFORM'						=> 'ligue',
	'PLATFORMS'						=> 'ligues',
	'PLATFORM_ADDED'				=> 'La ligue a été ajouté.',
	'PLATFORM_NAME'					=> 'Nom de la ligue',
	'PLATFORM_UPDATE'				=> 'La ligue a été supprimé.',
	'PMWINCONFIRMED'				=> '[SYSTEM] victoire confirmé',
	'PMWINCONFIRMEDTXT'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] confirme votre victoire.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',
										
	'PM_TOURNAMENTINVITE'			=> '[SYSTEM] Invitation Tournoi',
	'PM_TOURNAMENTINVITETXT'		=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        Vous a été invité à rejoindre le [b]%s[/b] tournoi. Si vous acceptez, cliquez sur [url=%s]ICI[/url] et rejoindre le tournoi.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',

										
	'PMCONFIRMWIN'					=> '[SYSTEM] victoire confirmé',
	'PMCONFIRMWINTXT'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] fait état d’une victoire contre vous. Vous devez confirmer cette victoire de matchs dans le Panneau de configuration de team ou, s’ils n’ont pas gagné, vous pouvez contester le résultat.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',
										
										
	'PMPENDINGMEMBERTXT'			=> 'Ceci est un message automatique à partir du système.
                                        [b]%s[/b] vous sollicite pour rejoindre dans votre équipe, %s. Vous pouvez approuver ou refuser cette demande dans le Panneau de configuration sous Gérer les membres de team.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',
										
										
	'PMPENDINGMEMBER'				=> '[SYSTEM] Demande membre',
	'PMINTVITETXT'					=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] vous a invité à rejoindre leur équipe. Acceptez-vous?
                                        [url=%s%s]oui[/url] ou [url=%s%s]Non[/url]
                                        Ne répondez pas à ce nom d’utilisateur, merci.',

										
										
	'PMINVITE'						=> '[SYSTEM] Invitation team',
	'PMREQUEST_APPROVED'			=> '[SYSTEM] Demande approuvée',
	'PMREQUEST_APPROVEDTXT'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] a approuvé votre demande pour rejoindre leur équipe. bienvenue :).
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PMREQUEST_DECLINED'			=> '[SYSTEM] Demande refusée',
	'PMREQUEST_DECLINEDTXT'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] a refusé votre demande d’adhésion à leur équipe. désolé :(.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',

										
										

	'PMTICKET'						=> '[SYSTEM] Ticket',
	'PMTICKETTXT'					=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                       [url=%s/rivals.php?action=group_profile&amp;group_id=%s]%s[/url] a envoyé un ticket.
                                       À propos de l’ID ddu match: %s - %s.
                                       [quote]%s[/quote]
                                       [url]%s/rivals/uploads/%s[/url]',
									   
									   
	'PMTICKETTXT_USER'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]

										[url=%s/memberlist.php?mode=viewprofile&u=%s]%s[/url] a envoyé un ticket..
										À propos de l’ID ddu match: %s - %s.

										[quote]%s[/quote]

										[url]%s/rivals/uploads/%s[/url]',

	'PMTICKETTXT_NOMATCH'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]

										[url=%s/memberlist.php?mode=viewprofile&u=%s]%s[/url] a envoyé un ticket..

										[quote]%s[/quote]

										[url]%s/rivals/uploads/%s[/url]',

	'PMTICKETTXT_NOATTCHMATCH'		=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]

										[url=%s/memberlist.php?mode=viewprofile&u=%s]%s[/url] a envoyé un ticket..

										[quote]%s[/quote]',								   
									   
									   
	'PMTICKET_TUR_TIMEOUT'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
	                                    <br />[url=%s/rivals.php?action=group_profile&group_id=%s]%s[/url] a envoyé un ticket.<br />
	                                    Il concerne un délai de confirmation de match sur le tournoi:<br />
	                                    [quote]%s[/quote]
	                                    Ne répondez pas à ce nom d’utilisateur, merci.',

										
										
	'PMTICKETTXT_NOATTACH'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [url=%s/rivals.php?action=group_profile&amp;group_id=%s]%s[/url] a envoyé un ticket.
                                        À propos de l’ID ddu match: %s - %s.
                                        [quote]%s[/quote]',


										
	'PM_TREPORTED'					=> '[SYSTEM] Tournoi Match Confirmer',
	'PM_TREPORTEDTXT'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        [b]%s[/b] a confirmé que vous avez gagné dans le tournoi, [b]%s[/b]. Félicitations, vous avez avancé dans les brackets.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',
										
										
										
	'PM_TREPORT'					=> '[SYSTEM] Confirme Win',
	'PM_TREPORTTXT'					=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        l’équipe [b]%s[/b] a fait état d’une victoire contre votre équipe dans le tournoi, [b]%s[/b].
                                        Vous devez confirmer que [b]%s[/b] ont gagné ou de contester le résultat pour terminer le signaleur.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',


	'PM_CHALLENGEDELETED'			=> '[SYSTEM] Défis supprimés',
	'PM_CHALLENGEDELETEDTXT'		=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        Le défi que vous avez fait à [b]%s[/b]a été supprimé car il n’a pas été acceptée ou refusée par [b]%s[/b] dans un délai maximum autorisée.
	                                    Ne répondez pas à ce nom d’utilisateur,merci.',




	'PM_CHALLENGEACCEPTED'			=> '[SYSTEM] défi accepté',
	'PM_CHALLENGEACCEPTEDTXT'		=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        L’équipe [b]%s[/b] a accepté votre défi.
                                        Ne répondez pas à ce nom d’utilisateur,merci.',



	'PM_CHALLENGEDECLINED'			=> '[SYSTEM] Défi Refusée',
	'PM_CHALLENGEDECLINEDTXT'		=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox]
                                        L’équipe [b]%s[/b] a refusé votre défi.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',


	'PM_CHALLENGE'					=> '[SYSTEM] Défi lancé par %s',
	'PM_CHALLENGETXT'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        L’équipe [b]%s[/b] dans (P: %s, L: %s, S: %s) vous à envoyer un défi. S’il vous plaît vous connecter à votre Panel, accepter ou refuser le défi quand vous êtes prêt.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PM_CHALLENGETXT_USER'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        Le joueur [b]%s[/b] dans  (P: %s, L: %s, S: %s) vous à envoyer un défi. S’il vous plaît vous connecter à votre Panel, accepter ou refuser le défi quand vous êtes prêt.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PM_CHALLENGETXT2'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        L’équipe [b]%s[/b] dans (P: %s, L: %s, S: %s) vous à envoyer un défi [b]unranked[/b]. S’il vous plaît vous connecter à votre Panel, accepter ou refuser le défi quand vous êtes prêt.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PM_CHALLENGETXT2_USER'			=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        Le joueur [b]%s[/b] dans (P: %s, L: %s, S: %s) vous à envoyer un défi [b]unranked[/b] . S’il vous plaît vous connecter à votre Panel, accepter ou refuser le défi quand vous êtes prêt.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PM_TOURNAMENT'					=> '[SYSTEM] Tournoi',
	'PM_TOURNAMENTMSG'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        [url=%s]%s[/url] à confirmé le résultat contre vous dans le %s tournoi.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'PM_TOURNAMENTMSG2'				=> '[warnbox]Ceci est un message automatique à partir du système.[/warnbox].
                                        [url=%s]%s[/url] à rapporté le résultat du match contre vous sur le %s tournoi.
                                        Ne répondez pas à ce nom d’utilisateur, merci.',



	'POS'							=> 'POS',
	'POSTED'						=> 'posté',
	'QUIT'							=> 'Quitter',
	'REPORTED_TOURNAMENTS'			=> 'Tournois signalés',
	'REPORTED_MATCHES'				=> 'Matchs déclarés',
	'REPORT_WIN'					=> 'Signaler une victoire',
	'RANKING_RANGE'					=> 'Désolé, mais l’équipe que vous désirez défier est hors de votre portée du classement. L’équipe doit être classer soit 3 ci-dessus ou en dessous de votre rang d’équipe.',
	'READY'							=> 'Ready',
	'REMOVE_GROUP_TOURNAMENT'		=> 'La team à été retiré de la compétition.',
	'REPORT_MATCH'					=> 'Signaler un match',
	'REQUEST_DECLINED'				=> 'Vous avez refusé la demande.',
	'REQUEST_SENT'					=> 'Votre demande d’adhésion à cette équipe a été soumis à l’examen.',
	'REPORT_TOURNAMENT'				=> 'Signaler un match du Tournoi',
	'REQUEST_TO_JOIN'				=> 'Demande de participation',
	'RIGHT_TO_LEFT'					=> 'De droite à gauche',
	'RIVALS_DATETIME'				=> 'Date / Heure',
	'RIVALS_DISALLOWED_EXTENSION'	=> 'L’extension %s n’est pas autorisée.',
	'RIVALS_EMPTY_FILEUPLOAD'		=> 'Le fichier téléchargé est vide.',
	'RIVALS_INVALID_FILENAME'		=> '%s est un nom de fichier invalide',
	'RIVALS_NOT_UPLOADED'			=> 'Le fichier ne peut être transféré.',
	'RIVALS_PARTIAL_UPLOAD'			=> 'Le fichier n’a été que partiellement téléchargé',
	'RIVALS_PHP_SIZE_NA'			=> 'Taille du fichier Le fichier téléchargé est trop grande. <br /> Impossible de déterminer la taille maximale définie par PHP dans php.ini.',
	'RIVALS_PHP_SIZE_OVERRUN'		=> 'Taille du fichier Le fichier téléchargé est trop grande, la taille maximale de téléchargement est de %d Mo. <br /> S’il vous plaît noter cette option est réglée dans le fichier php.ini et ne peut pas être remplacée.',
	'RIVALS_TITLE'					=> 'RivalsMOD | Gaming Sport League',
	'REPORTED_TOURNAMRNTS'			=> 'Les matchs du tournoi signalés',
	'ROUND'							=> 'Round %d',

	'SPOTS'							=> 'Spots',
	'SEASON'						=> 'Saison',
	'SEASON_EDITTED'				=> 'La saison a été modifié.',
	'SEASON_NAME'					=> 'Nom Saison',
	'SEASON_STARTED'				=> 'La saison a démarré pour ce ladder.',
	'SEASON_ENDED'					=> 'La saison est terminée et a été archivé. Vous pouvez maintenant commencer une nouvelle saison pour ce ladder.',
	'SEARCH_AVAILABLE_GROUPS'		=> 'Rechercher équipe disponibles',
	'SEED_OR_RANDOM'				=> 'Vous souhaitez voir le tournoi au hasard ou manuellement? Cliquez sur Oui pour "Random" et non pour «Manuel".',
	'SEED_TOURNAMENT'				=> 'Voir tournoi',
	'SIGNUP'						=> 'Inscription',
	'SIGN_UP'						=> 'Inscription',
	'STARTED'						=> 'Démarrage',
	'START_TOURNAMENT'				=> 'Début du tournoi',
	'STATISTICS'					=> 'statistiques',
	'START_SEASON'					=> 'Début de la saison',
	'SUBLADDER'						=> 'Sub-ladder',
	'SWAP'							=> 'Swap',

	'TOURNAMENT_INVITE'				=> 'seulement sur ​​invitation',
	'TICKET_FILE'					=> 'Screenshot/Video (facultatif)',
	'TICKET_RECEIVER'				=> 'Ticket Receiver',
	'TICKET_SENT'					=> 'Le Ticket a été envoyé. Soyez patient, il sera examiné dans les temps.',
	'TICKET_TYPE'					=> 'Type d’émission',
	'TOURNAMENT'					=> 'Tournoi',
	'TOURNAMENT_ADDED'				=> 'Le tournoi a été ajouté.',
	'TOURNAMENT_BRACKETS'			=> 'Tournoi Brackets',
	'TOURNAMENT_DIRECTION'			=> 'Tournoi Direction',
	'TOURNAMENT_DOUBLEELIM'			=> 'Double Elimination',
	'TOURNAMENT_FULL'				=> 'Les slots du tournoi ont été remplis. Vous ne pouvez plus vous inscrire.',
	'TOURNAMENT_STARTS'				=> 'Starts',
	'TOURNAMENT_STARTDATE'			=> 'Date de début du tournoi',
	'TOURNAMENT_INFO'				=> 'information sur le tournoi',
	'TOURNAMENT_LADDER'				=> 'Tournoi Ladder',
	'TOURNAMENT_NAME'				=> 'Nom du Tournoi',
	'TOURNAMENT_QUITTED'			=> 'Votre clan a quitté le tournoi.',
	'TOURNAMENT_REPORTED'			=> 'Le rapport de tournoi a été envoyé.',
	'TOURNAMENT_REPORT_INFO'		=> 'Vous faites une déclaration pour le "%s" tournoi et que votre adversaire est %s dans le bracket %s.',
	'TOURNAMENT_REPORTS'			=> 'Rapports du tournoi',
	'TOURNAMENT_SINGLEELIM'			=> 'Simple élimination',
	'TOURNAMENT_SPOTS'				=> 'Slots open: %d<br />Information: %s<br /><br /><a href="%s">Go Back</a>',
	'TOURNAMENT_TYPE'				=> 'type de tournoi',
	'TOURNAMENT_UPDATED'			=> 'Le tournoi a été mis à jour.',

	'UNASSIGNED'					=> 'non attribuer',
	'UNSTARTED'						=> 'non commencé',
	'UNREPORTED_MATCHES'			=> 'Matchs non attribuer',
	'UPGRADE_COMPLETE'				=> 'Votre version a maintenant été mis à jour.',
	'UPGRADE_NA'					=> 'Cette version n’est pas mis à jour.',
	'USER_ADDED_TO_GROUP'			=> 'L’utilisateur a été ajouté aux membres.',
	'USER_INVITED'					=> 'L’utilisateur a été invité par PM.',
	'USER_REMOVED_FROM_PENDING'		=> 'Demande de l’utilisateur a été refusé.',

	'VIEW_BRACKETS'					=> 'Voir Brackets',
	'VIEW_PROFILE'					=> 'Voir Profile',

	'WELCOME_CCP'					=> 'Bienvenue sur le Panneau de configuration',
	'WELCOME_CCPTXT'				=> '<br />Vous êtes actuellement connecté dans la team %s<strong>%s</strong>%s. Cet écran vous donnera un aperçu rapide des différentes statistiques. Utilisez les liens sur la gauche pour contrôler les aspects de votre expérience du clan.<br /><br />',
	'WELCOME_RIVALS'				=> 'Bienvenue à phpRivals Mod',
	'WELCOME_RIVALSTXT'				=> 'Merci d’avoir choisi Mod phpRivals pour votre solution de jeu. Cet écran vous donnera un aperçu rapide des différentes statistiques. Utilisez les liens sur la gauche pour contrôler les aspects de votre expérience Mod phpRivals.',
	'WIN'							=> 'Win',
	'WINNER'						=> 'Winner',
	'WINNER_BRACKET'				=> 'Winner Bracket',
	'WINNER_ROUND'					=> 'Winner!',
	'WORST_RANK'					=> 'Le plus mauvais Rang',

	'YOUR_APART_OF_GROUP'			=> 'Une erreur s’est produite dans votre proposition conjointe. S’il vous plaît réessayer.',
	'YOUR_OTHER_GROUPS'				=> 'Vos autres Team',
	'UPLOAD_AVATAR_URL_EXPLAIN'  	=> 'Upload une image pour le logo de la team',

	'USER_NOT_FOUND'				=> 'Le nom d’utilisateur ou ID que vous avez entré est incorrect. L’utilisateur n’a pas été trouvé.',
	'UNREPORTED_TOURNAMENTS'		=> 'Les matchs du tournoi non déclarées',
	
	// UCP HOME
	'MATCH_CHAT'			=> 'MATCH CHAT',
	'MATCH_CHAT_TXT'		=> 'Discuter avec vos adversaires pour organiser le match.',
	'ROSTER_LINUP'			=> 'Liste LINEUP',
	'ROSTER_LINUP_TXT'		=> 'Réglez votre liste LineUP,<br />aka vos sous-équipes.',
	'USER_TOURNAMENT'		=> 'TOURNOIS 1on1',
	'USER_TOURNAMENT_TXT'	=> 'Gérer les joueurs du tournoi vs joueur, aka 1on1.',
	'USER_MATCH'			=> 'MATCHS en 1on1',
	'USER_MATCH_TXT'		=> 'Gérer les matchs joueurs vs joueurs, aka 1on1.',
	'ADD_USER_MATCH'		=> 'Défier des joueurs',
	'ADD_USER_MATCH_TXT'	=> 'Défier des autres des joueurs<br />si ils  ne sont pas dans le même ladders.',
	'EDIT_CLAN'				=> 'MODIFIER TEAM',
	'EDIT_CLAN_TXT'			=> 'Modifier vos infos de profil de team: nom, etc logo site, ..',
	'SMSG'					=> 'MESSAGE TEAM',
	'SMSG_TXT'				=> 'Insérez des messages courts dans votre  profil de team.',
	'MEN_MEMBERS'			=> 'MEMBRES TEAM',
	'MEN_MEMBERS_TXT'		=> 'Gérer les membres de votre clan. Vous pouvez ajouter, les supprimer.',
	'SEND_TICKET'			=> 'ENVOYER TICKET',
	'SEND_TICKET_TXT'		=> 'Envoyer un ticket au staff.<br />Utilisez-le si vous avez eu des problèmes!',
	'FIND_MATCH'			=> 'RECHERCHE PCW',
	'FIND_MATCH_TXT'		=> 'Trouver une team prêts pour les matchs ou proposer un match.',
	'ADD_MATCH'				=> 'DEFIER TEAM',
	'ADD_MATCH_TXT'			=> 'Défier d’autres teams si elles ne sont dans les mêmes ladders.',
	'PENDING_MATCH'			=> 'DEFIS EN ATTENTES',
	'PENDING_MATCH_TXT'		=> 'Accepter ou refuser des défis que d’autres teams vous envoyent.',
	'REPORT_MATCHES'		=> 'MATCHS RAPPORT',
	'REPORT_MATCHES_TXT'	=> 'Transcrire le résultat d’un match que vous avez accepté.',
	'CONFIRM_MATCH'			=> 'CONFIRMER RESULTAT',
	'CONFIRM_MATCH_TXT'		=> 'Confirmez ou de contester résultat d’un match déjà signalé.',
	'CLAN_TOURNAMENT'		=> 'TEAM TOURNOIS',
	'CLAN_TOURNAMENT_TXT'	=> 'Gérer tournoi où vous jouez comme le clan.',
));

?>