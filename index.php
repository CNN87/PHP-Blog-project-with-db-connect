<?php
#************************************************************************************************#


				#***************************************#
				#********* PAGE CONFIGURATION **********#
				#***************************************#
 
				/*
					include(Pfad zur Datei): Bei Fehler wird das Skript weiter ausgeführt. Problem mit doppelter Einbindung derselben Datei
					require(Pfad zur Datei): Bei Fehler wird das Skript gestoppt. Problem mit doppelter Einbindung derselben Datei
					include_once(Pfad zur Datei): Bei Fehler wird das Skript weiter ausgeführt. Kein Problem mit doppelter Einbindung derselben Datei
					require_once(Pfad zur Datei): Bei Fehler wird das Skript gestoppt. Kein Problem mit doppelter Einbindung derselben Datei
				*/
				require_once('./include/config.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');
				require_once('./include/dateTime.inc.php');
				

#************************************************************************************************#



#************************************************************************************************#


				#****************************************#
				#********* INITIALZE VARIABLES **********#
				#****************************************#
				
				$userFirstName			= NULL;
				$errorLogin 			= NULL;
				$categoryFilterID 	= NULL;
				
			

#************************************************************************************************#

				#*******************************************#
				#************* VALIDATE LOGIN **************#
				#*******************************************#

				#********** PREPARE SESSION **********#
				// Der Sessionname an dieser Stelle muss dem Sessionnamen beim Loginvorgang entsprechen
				session_name('wwwblogprojektphpde');

				if( session_start() === false ) {
					// Fehlerfall 
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Fehler beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
				} else {
					//Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: session_name 'wwwblogprojektphpde' erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_SESSION);					
if(DEBUG_V)	echo "</pre>";		
*/			

					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#
					
					/*
						Ohne erfolgten Login ist das SESSION-Array an dieser Stelle leer.
						Bei erfolgtem Login beinhaltet das SESSION-Array an dieser Stelle 
						den beim Login-Vorgang vergebenen Index 'ID', dessen Existenz an
						dieser Stelle geprüft wird.
					*/
					/*
						SICHERHEIT: Um Session Hijacking und ähnliche Identitätsdiebstähle zu verhindern,
						wird die IP-Adresse des sich einloggenden Users beim Loginvorgang in die Session gespeichert.
						Hier wird die aufrufende IP-Adresse erneut ermittelt und mit der in der Session gespeicherten 
						IP-Adresse abgeglichen.
						Eine IP-Adresse zu fälschen ist nahezu unmöglich. Wenn sich also ein Cookie-Dieb von einer
						anderen IP-Adresse als der beim Loginvorgang aktuellen aus einloggen will, wird ihm an dieser Stelle
						der Zugang verweigert und der Login muss erneut durchgeführt werden.
						
						Diese Maßnahme hilft auch gegen das 'zufällige' Erraten eines fremden Sessionnamens,
						da sich die in der Sessiondatei gespeicherte IP-Adresse von der aktuell die Seite
						aufrufenden IP-Adresse unterscheidet.
					*/
					if( isset( $_SESSION['ID'] ) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// Fehlerfall 
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: User ist nicht eingeloggt.! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						#********** DENY PAGE ACCESS **********#
						// 1. Leere Sessiondatei löschen
						/*
							Da jeder unberechtigte Seitenaufruf eine neue leere Sessiondatei erzeugt,
							wird diese an dieser Stelle wieder gelöscht. So wird verhindert, dass
							der Server im Laufe der Zeit mit vielen unnötigen leeren Sessiondateien 
							zugemüllt wird.
						*/
						session_destroy();
						
						$isUserLoggedIn = false;
						
											
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: User ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						session_regenerate_id(true);
						
						$isUserLoggedIn = true;
										
					}
				} // END VALIDATE LOGIN END


#************************************************************************************************#


				#***************************************#
				#********* PROCESS FORM LOGIN **********#
				#***************************************#
				
				#********** PREVIEW POST ARRAY **********#

// if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
// if(DEBUG_V)	print_r($_POST);					
// if(DEBUG_V)	echo "</pre>";

				#****************************************#
				
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde				
				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte von 'Login' werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$userEmail 	= sanitizeString($_POST['f1']);
					$password 	= sanitizeString($_POST['f2']);
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userEmail: $userEmail <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$password: $password <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					$errorUserEmail 	= validateEmail($userEmail);
					$errorPassword 	= validateInputString($password);

// if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserEmail: $errorUserEmail <i>(" . basename(__FILE__) . ")</i></p>\n";
// if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorPassword: $errorPassword <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
					if( $errorUserEmail !== NULL OR $errorPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular 'Login' enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						// NEUTRALE Fehlermeldung für User
						$errorLogin = 'Benutzername oder Passwort falsch!';
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular 'Login' ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						// Schritt 4 FORM: Werte weiterverarbeiten
						
						
						#********** FETCH USER DATA FROM DATABASE **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";

						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blogprojekt');
						
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
						$sql 				= 'SELECT userID, userPassword, userFirstName, userLastName FROM users
												WHERE userEmail = :userEmail';
						
						$placeholders 	= array( 'userEmail' => $userEmail );
						
						
						// Schritt 3 DB: Prepared Statements
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);
							// showQuery($PDOStatement);
							
						} catch(PDOException $error) {
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
						}
						
						
						// Schritt 4 DB: Daten weiterverarbeiten
						/*
							Bei lesenden Operationen wie SELECT und SELECT COUNT:
							Abholen der Datensätze bzw. auslesen des Ergebnisses
						*/
						$userData = $PDOStatement->fetch(PDO::FETCH_ASSOC);
						
						// DB-Verbindung schließen
						dbClose($PDO, $PDOStatement);
					
// if(DEBUG_A)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
// if(DEBUG_A)			print_r($userData);					
// if(DEBUG_A)			echo "</pre>";

						
						#********** 1. VALIDATE EMAIL **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validiere Email-Adresse... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						/*
							In $userData ist nur dann ein Datensatz enthalten, wenn die EmailAdresse aus dem Formular 
							mit einer Emailadresse aus der DB übereinstimmt.						
							Wenn ein passender Datensatz gefunden wurde, liefert $PDOStatement->fetch() an dieser
							Stelle ein eindimensionales Array mit den ausgelesenen Datenfeldwerten zurück.
							Wenn KEIN passender Datensatz gefunden wurde, enthält $userData an dieser Stelle false.
						*/
						if( $userData === false ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmail' wurde nicht in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							// NEUTRALE Fehlermeldung für User
							$errorLogin = 'Benutzername oder Passwort falsch!';
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmail' wurde in der DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							
							#********** 2. VALIDATE PASSWORD **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validiere Password... <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							/*
								Die Funktion password_verify() vergleicht einen String mit einem mittels
								password_hash() verschlüsseltem Passwort. Die Rückgabewerte sind true oder false.
							*/
							if( password_verify( $password, $userData['userPassword'] ) === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt nicht mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
								// NEUTRALE Fehlermeldung für User
								$errorLogin = 'Benutzername oder Passwort Falsch!';
							
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
									
// 								#********** 3. PROCESS LOGIN **********#
// if(DEBUG)					echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Login wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";
									
									
// 								#********** 4a. PREPARE SESSION **********#
// 								// Der Sessionname sollte unique sein (beispielsweise aus dem Domainnamen der Webseite (ohne Punkte) bestehen)
// 								
									
									
								if( session_start() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
									$errorLogin = 'Der Login ist nicht möglich! 
														Bitte überprüfen Sie, ob in Ihrem Browser die Annahme von Cookies aktiviert ist.';
									
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
										
									#********** SAVE USER DATA INTO SESSION FILE **********#
									$_SESSION['ID'] 			= $userData['userID'];
									$_SESSION['IPAddress'] 	= $_SERVER['SERVER_ADDR'];
									
										
// if(DEBUG_A)						echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
// if(DEBUG_A)						print_r($_SESSION);					
// if(DEBUG_A)						echo "</pre>";


									#********** REDIRECT TO INTERNAL PAGE **********#
									/*
										Die Funktion header() versendet sofort den HTTP-Header an den Client.
										Über den HTTP-Header können diverse Verhalten gesteuert werden, wie
										beispielsweise die automatische Weiterleitung auf eine andere Seite.
										
										Durch die Funktion header() wird ein String in den HTTP-Header geschrieben,
										der in diesem Fall den Befehl 'LOCATION:' sowie eine Zielseite für die
										Umleitung enthält.
									*/
									header('LOCATION: ./dashboard.php');
										
								} // 3. PROCESS LOGIN END

							} // 2. VALIDATE PASSWORD END

						} // 1. VALIDATE EMAIL END

					} // FINAL FORM VALIDATION (FIELDS VALIDATION) END
					
				} // PROCESS FORM LOGIN END				


#************************************************************************************************#

				#***************************************#
				#********* PROCESS FORM LOGIN **********#
				#***************************************#
				
				#********** PREVIEW POST ARRAY **********#

// if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
// if(DEBUG_V)	print_r($_POST);					
// if(DEBUG_V)	echo "</pre>";

				#****************************************#
				
				// Schritt 1 URL: Prüfen ob Parameter übergeben wurde
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$action = sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";

					
					#************* LOGOUT ***************#
					if( $_GET['action'] === 'logout' ) {
if(DEBUG)			echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Lougout wird durchgeführt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
						session_destroy();
						header('LOCATION: ./'); 
						exit(); 

					#************* FILTER BY CATEGORY ***************#
					} elseif ($action === 'filterByCategory') {
if(DEBUG)			echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Kategoriefilter aktiv... <i>(" . basename(__FILE__) . ")</i></p>\n";										
						

						#************* FETCH SECOND URL PARAMETER ***************#
						if( isset($_GET['catID']) === true ) {

							//use $categoryFilterID as flag
							$categoryFilterID = sanitizeString($_GET['catID']);
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryFilterID: $categoryFilterID <i>(" . basename(__FILE__) . ")</i></p>\n";
	

						} // FETCH SECOND URL PARAMETER END

					} //BRANCHING END

				} //PROCESS URL PARAMETERS END
				

#************************************************************************************************#


				#*********************************************#
				#********** FETCH BLOG DATA FROM DB **********#
				#*********************************************#

if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Blogdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
	
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blogprojekt');

				
				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
				$sql 				= 'SELECT * FROM blogs 
										INNER JOIN users  USING(userID) 
										INNER JOIN categories USING(catID)';
				
				$placeholders 	= array();

				#********** A) FETCHC BLOG ENTRIES *************************#	
				if( $categoryFilterID === NULL ) {
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lade alle Blog-Einträge... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
				#********** B) FILTER BLOG ENTRIES BY CATEGORY ID **********#	
				} else {
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Filtere Blog-Einträge nach Kategorie-ID$categoryFilterID... <i>(" . basename(__FILE__) . ")</i></p>\n";

					/*
						für Fall b) eine Bedingung für den Kategoriefilter
						muss eine weitere Sql-Anweisung hinzugefügt werden
					*/

					$sql			.= ' WHERE catID = :ph_catID';

					/*
						Und deshalb muss ein Platzhalter zugewiesen und mit einem
						mit einem Wert gefüllt werden
					*/

					$placeholders['ph_catID']	=	$categoryFilterID;

				}
				#***********************************************************#

				/*
					für beide Fälle schließlich den Befehl 'order by' hinzufügen,
					der letzte Befehl in der Sql-Anweisung (nach einer WHERE-Bedingung)
				*/

				$sql				.= ' ORDER BY blogDate DESC';
				
				#***********************************************************#

				// Schritt 3 DB: Prepared Statements
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($placeholders);
					// showQuery($PDOStatement);
					
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					
				}

				// Schritt 4 DB: Daten weiterverarbeiten
				/*
					Bei lesenden Operationen wie SELECT und SELECT COUNT:
					Abholen der Datensätze bzw. auslesen des Ergebnisses
				*/
				/*
					Das PDOStatement-Objekt verfügt über mehrere Methoden zum Auslesen
					von Datensätzen:
					
					fetchAll()  	- liest alle Datensätze auf einmal aus und liefert ein zweidimensionales 
										  Array zurück.
					fetch()			- liest den ersten gefundenen Datensatz aus und liefert diesen als
										  eindimensionales Array zurück. Wird fetch() mehrmals nacheinander
										  (beispielsweise über eine Schleife) aufgerufen, wird bei jedem Aufruf
										  der nachfolgende Datensatz zurückgeliefert, bis alle Datensätze einmal
										  ausgelesen wurden.
					fetchColumn() 	- Liefert den Rückgabewert der SQL-Funktion COUNT() aus. COUNT() liefert
										  einen Integer mit der Anzahl der gezählten Datensätze zurück.
				*/
				/*
					Der fetchAll()-Parameter PDO::FETCH_ASSOC liefert o.g. assoziatives Array zurück.
					Der fetchAll()-Parameter PDO::FETCH_NUM liefert das gleiche Array als numerisches Array zurück.
				*/
				

				if (isset($PDOStatement)) {
					$blogEntriesArray = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
			  	} else {
if (DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: PDOStatement is not set. <i>(" . basename(__FILE__) . ")</i></p>\n";
			  	}
			  

				// DB-Verbindung schließen
				dbClose($PDO, $PDOStatement);
/*		
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogEntriesArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($blogEntriesArray);					
if(DEBUG_A)	echo "</pre>";
*/

#************************************************************************************************#


				#*************************************************#
				#********** FETCH CATEGORY DATA FROM DB **********#
				#*************************************************#
				
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Kategorie Datensätze aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
                           
         	// Schritt 1 DB: DB-Verbindung herstellen
         	$PDO = dbConnect('blogprojekt');

         	// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
         	$sql 				= 'SELECT * FROM categories';

         	$placeholders 	= array();

        	 	// Schritt 3 DB: Prepared Statements
         	try {
            	// Prepare: SQL-Statement vorbereiten
            	/* 
               	PREPARED STATEMENTS (PREPARE):
               	In diesem Schritt wird der Datenbank das SQL-Statement mit 
               	einem Platzhalter übergeben. Die DB analysiert nun die Struktur des SQL-Statements
            	*/
            	$PDOStatement = $PDO->prepare($sql);
            
            	// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					/*
						PREPARED STATEMENTS (EXECUTE):
						In diesem Schritt der Platzhalter im oben übergebenen SQL-Statement
						nachgeliefert. Die DB setzt den Wert für den Platzhalter in das Statement ein und prüft,
						ob sie die Struktur des Statements geändert hat, oder tatsächlich nur ein einfacher Wert
						eingefügt wurde. Ist Letzteres der Fall, wird das Statement wie gewünscht ausgeführt.
						Andernfalls wird die Ausführung blockiert.
					*/
					$PDOStatement->execute($placeholders);
					// showQuery($PDOStatement);
            
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
				}

				// Schritt 4 DB: Daten weiterverarbeiten
				/*
					Bei lesenden Operationen wie SELECT und SELECT COUNT:
					Abholen der Datensätze bzw. auslesen des Ergebnisses
				*/
				/*
					Das PDOStatement-Objekt verfügt über mehrere Methoden zum Auslesen
					von Datensätzen:
					
					fetchAll()  	- liest alle Datensätze auf einmal aus und liefert ein zweidimensionales 
										Array zurück.
					fetch()			- liest den ersten gefundenen Datensatz aus und liefert diesen als
										eindimensionales Array zurück. Wird fetch() mehrmals nacheinander
										(beispielsweise über eine Schleife) aufgerufen, wird bei jedem Aufruf
										der nachfolgende Datensatz zurückgeliefert, bis alle Datensätze einmal
										ausgelesen wurden.
					fetchColumn() 	- Liefert den Rückgabewert der SQL-Funktion COUNT() aus. COUNT() liefert
										einen Integer mit der Anzahl der gezählten Datensätze zurück.
				*/
				/*
					Der fetchAll()-Parameter PDO::FETCH_ASSOC liefert o.g. assoziatives Array zurück.
					Der fetchAll()-Parameter PDO::FETCH_NUM liefert das gleiche Array als numerisches Array zurück.
				*/

         	$categoryData = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
  		
// if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
// if(DEBUG_A)	print_r($categoryData);					
// if(DEBUG_A)	echo "</pre>";

				if ($categoryData === NULL) {
					//Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER Keine Kategorien in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
					// Fehlermeldung für User
					$errorCategoryData = 'Bitte Neue Kategorie erstellen.';

				} else {
					//Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Es wurden Kategorien in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
				}

				#********** CLOSE DB CONNECTION **********#
				dbClose($PDO, $PDOStatement);


#************************************************************************************************#


				#*******************************************#
				#********* PROCESS URL PARAMETERS **********#
				#*******************************************#
				
				#********** PREVIEW GET ARRAY **********#
/*			
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($_GET);
if(DEBUG_A)	echo "</pre>";
*/

#****************************************#
				
				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde
				/*
					Wurde ein Link mit einem URL-Parameter geklickt, 
					enthält das $_GET-Array an dieser Stelle einen Index, der dem Namen des
					URL-Parameters entspricht. Der Wert zu diesem Index entspricht entsprechend
					dem Wert des URL-Parameters.
					Umkehrschluss: Fehlt dieser Index, wurde auch kein Link angeklickt.
				*/	
				/*
					ISSET()-FUNKTION:
					Die Funktion isset() prüft eine Variable/einen Array-Index auf Existenz und auf einen anderen Wert als NULL.
					Trifft beides zu, liefert isset() den Boolean true zurück, ansonsten False.
					Der Sinn von isset() ist explizit die Prüfung auf Existenz. Existiert eine Variable/ein Index nicht, wird 
					keine PHP-Fehlermeldung ausgeworfen.
					Ohne die Verwendung von isset() würde an dieser Stelle bei Nichtexistenz die Fehlermeldung 'Undefined Variable...'
					ausgeworfen werden.
				*/
				if( isset($_GET['action']) === 'filterByCategory' ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Kategoriefilter aktiv. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					if( isset($_GET['catID']) === 'true' ){

						$categoryFilterID = sanitizeString($_GET['catID']);

					}
										
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					/*
						SCHUTZ GEGEN EINSCHLEUSUNG UNERWÜNSCHTEN CODES:
						Damit so etwas nicht passiert: <script>alert("HACK!")</script>
						muss der empfangene String ZWINGEND entschärft werden!
						htmlspecialchars() wandelt potentiell gefährliche Steuerzeichen wie
						< > " & in HTML-Code um (&lt; &gt; &quot; &amp;).
						
						Der Parameter ENT_QUOTES wandelt zusätzlich einfache ' in &apos; um.
						Der Parameter ENT_HTML5 sorgt dafür, dass der generierte HTML-Code HTML5-konform ist.
						
						Der 1. optionale Parameter regelt die zugrundeliegende Zeichencodierung 
						(NULL=Zeichencodierung wird vom Webserver übernommen)
						
						Der 2. optionale Parameter bestimmt die Zeichenkodierung
						
						Der 3. optionale Parameter regelt, ob bereits vorhandene HTML-Entities erneut entschärft werden
						(false=keine doppelte Entschärfung)
					*/
					/*
						trim() entfernt VOR und NACH einem String (aber nicht mitten drin) 
						sämtliche sog. Whitespaces (Leerzeichen, Tabs, Zeilenumbrüche)
					*/
					$action 	= trim( htmlspecialchars($_GET['action'], 	ENT_QUOTES | ENT_HTML5, 'UTF-8', false) );

					/*
						DEBUGGING:
						1. Ist der Variablenname korrekt geschrieben?
						2. Steht in jeder Variable der korrekte Wert?
					*/
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				

				} //Schritt 1 PROCESS URL PARAMETERS END



#************************************************************************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>PHP-Projekt Blog</title>
		
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
		
		<style>
			main {
				width: 60%;
			}
			aside {
				width: 30%;
				padding: 20px;
				border-left: 1px solid gray;
				opacity: 0.6;
				overflow: hidden;
			}
			article{
				border: 1px solid;
				padding: 10px;
				border-radius: 10px;
				
			}
			.image-left {
            float: left;
            margin: 0 20px 20px 0;
        }

        .image-right {
            float: right;
            margin: 0 0 20px 20px;
        }

        .image-none {
            float: none;
            margin: 0;
        }

        .clearer {
            clear: both;
        }
		  .blogImageSize{
				width: 200px;
		  }
		  .catLabelRight{
				float: right;
				color: darkred;
		  }
		  .postedBy{
				color: lightgrey;
		  }
		  .catTabs{
			display: block;
				border: 1px solid;
				padding: 10px;
				border-radius: 10px;
				border-color: blue;
				color: blue;
				text-align: center;
				cursor: pointer;
		  }
		  .catTabs:hover{
				color: black;
				background-color: royalblue;
				font-weight: bold;
		  }
		  .catTabs {
				text-decoration: none;
				color: black;
			}
			.catTabs.active {
				font-weight: bold;
				color: blue;
				background-color: lawngreen; 
			}
			.catTabPadding{
				padding: 10px;
			}
			.content-container{
				overflow-wrap: break-word;
				/* white-space: pre-wrap; */
				word-wrap: break-word; /* Für ältere Browser */
				max-width: 100%; 
			}
		</style>
		
	</head>
	
	<body>
		
		<!-- -------- PAGE HEADER START -------- -->
		<br>
		<header class="fright loginheader">
		<!-- ---- CHECK IF USER IS LOGGED IN in --- -->
      <?php if( $isUserLoggedIn === false ): ?>
			<?php if($errorLogin): ?>
				<p class='error'><?= $errorLogin ?></p>
			<?php endif ?>

			<!-- -------- LOGIN FORM START -------- -->
			<form action="" method="POST">
				<input type="hidden" name="formLogin">
				<fieldset>
					<legend>Login</legend>					
					
					<input class="short" type="text" name="f1" placeholder="Email-Adresse...">
					<input class="short" type="password" name="f2" placeholder="Passwort...">
					<input class="short" type="submit" value="Login">
				</fieldset>
			</form>
			<!-- -------- LOGIN FORM END -------- -->		
		<?php else: ?>
         <p>Willkommen User !</p>
         <p><a href="?action=logout"><< Logout</a></p>
			<p><a href="dashboard.php">zum Dashboard >></a></p>
      <?php endif ?>
         
			
		</header>
		<div class="clearer"></div>
		
		<hr>
		<!-- -------- PAGE HEADER END -------- -->
		
		
		<main class="fleft">
		
			<h1>PHP - Projekt Blog</h1>
         <p><a href="./"><i>Alle Einträge anzeigen</i></a></p>
			
			<!-- -------- USER MESSAGES START -------- -->
			<?php if( isset($error) === true ): ?>
				<h3 class="error"><?= $error ?></h3>
			<?php elseif( isset($info) === true ): ?>
				<h3 class="info"><?= $info ?></h3>
			<?php elseif( isset($success) === true ): ?>
				<h3 class="success"><?= $success ?></h3>
			<?php endif ?>
			<!-- -------- USER MESSAGES END -------- -->
			
			<p>
			<?php if (!empty($blogEntriesArray)): ?>
				<?php foreach ($blogEntriesArray as $entry): ?>
					<article>
							<p class="catLabelRight"><small>Kategorie: <?= htmlspecialchars($entry['catLabel']) ?></small></p>
							<h2><?= htmlspecialchars($entry['blogHeadline']) ?></h2>
							<p class="postedBy"><small>Posted on <?= htmlspecialchars($entry['blogDate']) ?> by <?= htmlspecialchars($entry['userFirstName']) . " " . htmlspecialchars($entry['userLastName']) ?></small></p>
							
							<div class="blog-entry">
								
								<?php if (!empty($entry['blogImagePath'])): ?>
									<div class="image-container image-<?= htmlspecialchars($entry['blogImageAlignment']) ?>">
											<img class="blogImageSize" src="<?= htmlspecialchars($entry['blogImagePath']) ?>" alt="<?= htmlspecialchars($entry['blogHeadline']) ?>">
									</div>
								<?php endif; ?>
								<div class="content-container">
									<!-- nl2br() dient dazu ein zeilenumbruch in einem Text in HTML umzuwandeln -->
									<p><?= nl2br(htmlspecialchars($entry['blogContent'])) ?></p>
								</div>
								<div class="clearer"></div>
							</div>
					</article>
					<hr>
				<?php endforeach; ?>
			<?php else: ?>
				<p>Keine Blog Einträge zu dieser Kategorie vorhanden.</p>
				<p>Bitte wählen Sie eine andere Kategorie aus.</p>
			<?php endif; ?>
			</p>
			
			
			
		</main>
		
		<!-- CATEGORY FILTER START  -->
		<aside class="fright">
		
			<h2>Kategorien</h2>
         	
			<?php
				if ($categoryData === false): ?>
					<p class="info">Keine Kategorien vorhanden</p>
				<?php else: ?>
					<?php foreach ($categoryData as $category): ?>
						<?php
						// Überprüfen, ob die aktuelle Kategorie ausgewählt ist
						$isActive = isset($_GET['action']) && $_GET['action'] === 'filterByCategory' && isset($_GET['catID']) && $_GET['catID'] == $category['catID'];
						?>
						<p class="catTabPadding"><a href="?action=filterByCategory&catID=<?= $category['catID'] ?>"
							<?php if ($isActive): ?>class="active catTabs"<?php else: ?>class="catTabs"<?php endif; ?>>
								<?= $category['catLabel'] ?>
						</a></p>
					<?php endforeach; ?>
				<?php endif; ?>

			
			<!-- CATEGORY FILTER END  -->
			
			
			
			

			<!-- <?php
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_SERVER);					
if(DEBUG_V)	echo "</pre>";	
			?> -->

			
			
		</aside>
		
		<div class="clearer"></div>

		
	</body>
	
</html>