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


				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#
				
				#********** PREPARE SESSION **********#
				// Der Sessionname an dieser Stelle muss dem Sessionnamen beim Loginvorgang entsprechen
				session_name('wwwblogprojektphpde');
				
				
				#********** START/CONTINUE SESSION **********#
				/*
					Der Befehl session_start() liest zunächst ein Cookie aus dem Browser des Clients aus,
					das dem Namen des im ersten Schritts gesetzten Sessionnamens entspricht. Existiert
					dieses Cookie, wird aus ihm der Name der zugehörigen Sessiondatei ausgelesen und geprüft,
					ob diese auf dem Server existiert. Ist beides der Fall, wird die bestehende Session fortgesetzt.
					
					Existieren Cookie oder Sessiondatei nicht, wird an dieser Stelle eine neue Session
					gestartet: Der Browser erhält ein frisches Cookie mit dem oben gesetzten Namen, und auf dem Server
					wird eine neue, leere Sessiondatei erstellt, deren Dateinamen in das Cookie geschrieben wird.
				*/
				session_start();
/*				
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($_SESSION);					
if(DEBUG_A)	echo "</pre>";				
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
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
					
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Sessiondatei löschen
					/*
						Da jeder unberechtigte Seitenaufruf eine neue leere Sessiondatei erzeugt,
						wird diese an dieser Stelle wieder gelöscht. So wird verhindert, dass
						der Server im Laufe der Zeit mit vielen unnötigen leeren Sessiondateien 
						zugemüllt wird.
					*/
					session_destroy();
					
					
					// 2. User auf öffentliche Seite umleiten
					/*
						Die Funktion header() versendet sofort den HTTP-Header an den Client.
						Über den HTTP-Header können diverse Verhalten gesteuert werden, wie
						beispielsweise die automatische Weiterleitung auf eine andere Seite.
						
						Durch die Funktion header() wird ein String in den HTTP-Header geschrieben,
						der in diesem Fall den Befehl 'LOCATION:' sowie eine Zielseite für die
						Umleitung enthält.
					*/
					header('LOCATION: ./');
					
					
					// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
					// Die Funktion 'exit()' beendet sofort die weitere Ausführung des Skripts
					exit();
					
					
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Login wurde erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
					
					/*
						SICHERHEIT: Um Cookiediebstahl oder Session Hijacking vorzubeugen, wird nach erfolgreicher
						Authentifizierung eine neue Session-ID vergeben. Ein Hacker, der zuvor ein Cookie mit einer 
						gültigen Session-ID erbeutet hat, kann dieses nun nicht mehr benutzen.
						Die Session-ID muss bei jedem Seitenaufruf erneuert werden, um einen ausreichenden Schutz 
						zu gewährleisten.
						
						Damit die neue Session-ID auch im Cookie upgedated wird, muss session_regenerate_id() den 
						optionalen Parameter delete_old_session=true erhalten.
					*/
					/*
						Dieser Erfolgsfall wird bei jedem einzelnen Seitenaufruf ausgeführt, d.h. die Session-ID wird
						tatsächlich bei jedem Seitenaufruf neu generiert.
					*/
					session_regenerate_id(true);
					
					
					// Identifizieren des Users anhand der ID in der Session
					$userID = $_SESSION['ID'];					
				}
				

#************************************************************************************************#


				#****************************************#
				#********* INITIALZE VARIABLES **********#
				#****************************************#
				
            $userFirstName       = NULL;
            $userLastName        = NULL;

            $catLabel            = NULL;
            $blogHeadline			= NULL;
				$blogImageAlignment  = NULL;
				$blogContent			= NULL;
				

				$errorCatLabel 	   = NULL;
				$errorCategoryData	= NULL;
				$errorBlogImage	 	= NULL;
				$errorBlogHeadline 	= NULL;
				$errorBlogContent 	= NULL;
				$errorCatSelectBox 	= NULL;
								
				

#************************************************************************************************#


				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				#********** PREVIEW GET ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_GET);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#
				
				
				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde				
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$action = sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					// Schritt 3 URL: Je nach erlaubtem Parameterwert verzweigen
					if( $action === 'logout' ) {
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Logout wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// Schritt 4 URL: Werte verarbeiten
						
						// 1. SESSION Datei löschen
						session_destroy();
						
						
						// 2. User auf öffentliche Seite umleiten
						/*
							Die Funktion header() versendet sofort den HTTP-Header an den Client.
							Über den HTTP-Header können diverse Verhalten gesteuert werden, wie
							beispielsweise die automatische Weiterleitung auf eine andere Seite.
							
							Durch die Funktion header() wird ein String in den HTTP-Header geschrieben,
							der in diesem Fall den Befehl 'LOCATION:' sowie eine Zielseite für die
							Umleitung enthält.
						*/
						header('LOCATION: ./');
						
						
						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						// Die Funktion 'exit()' beendet sofort die weitere Ausführung des Skripts
						exit();
						
						
					} // BRANCHING END
					
				} // PROCESS URL PARAMETERS END


#************************************************************************************************#


				#*********************************************#
				#********** FETCH USER DATA FROM DB **********#
				#*********************************************#
				
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Benutzerdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blogprojekt');
				
				
				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen

				$sql 				= 'SELECT userFirstName, userLastName FROM users
										WHERE userID = :userID';
				
				$placeholders 	= array( 'userID' => $userID );
				
				
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
				
				
				// Schritt 4 DB: Datenbankoperation auswerten und DB-Verbindung schließen
				/*
					Bei lesenden Operationen wie SELECT und SELECT COUNT:
					Abholen der Datensätze bzw. auslesen des Ergebnisses
				*/
				/*
					$PDOStatement->fetch() liefert den ersten gefundenen Datensatz in Form 
					eines eindimensionalen Arrays zurück und merkt sich, welcher Datensatz
					zuletzt zurückgeliefert wurde
				*/
				$userData = $PDOStatement->fetch(PDO::FETCH_ASSOC);
				
				// DB-Verbindung schließen
				dbClose($PDO, $PDOStatement);
/*				
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($userData);					
if(DEBUG_A)	echo "</pre>";
*/
				
				/*
					Werte aus DB-Array in Variablen umkopieren, damit diese später ggf. durch
					die Formularverarbeitung überschrieben werden können
				*/
				$userFirstName 				= $userData['userFirstName'];
				$userLastName 					= $userData['userLastName'];
				
// if(DEBUG_V)	echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userFirstName: $userFirstName <i>(" . basename(__FILE__) . ")</i></p>\n";
// if(DEBUG_V)	echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userLastName: $userLastName <i>(" . basename(__FILE__) . ")</i></p>\n";
            
				
#************************************************************************************************#


				#**********************************************************#
				#********* PROCESS FORM CREATE NEW CATEGORY DATA **********#
				#**********************************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#
				
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde				
				if( isset($_POST['formNewCategory']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'CREATE NEW CATEGORY ' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte von 'formNewCategory' werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$catLabel 			      = sanitizeString($_POST['catLabel']);
					
					/*
						DEBUGGING:
						1. Ist der Variablenname korrekt geschrieben?
						2. Steht in jeder Variable der korrekte Wert?
					*/
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catLabel: $catLabel <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$errorCatLabel 	= validateInputString($catLabel, mandatory:true, minLength:3);


					#********** FINAL FORM VALIDATION I (FIELDS VALIDATION) **********#
					if( $errorCatLabel !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Bitte eine Kategorie eintragen! Es darf nicht leer sein und muss mind.3Zeichen lang sein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der Name der Kategorie ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						// Schritt 4 FORM: Werte verarbeiten
						

						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#						
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blogprojekt');
						
						
						#********** 1. CHECK IF CATEGORY IS ALREADY IN THE DB **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob Kategorie bereits in der DB categories enthalten ist... <i>(" . basename(__FILE__) . ")</i></p>\n";
						

						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)		   echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Kategorie werden in die DB gespeichert... <i>(" . basename(__FILE__) . ")</i></p>\n";

                  // Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
						$sql 				= 'SELECT COUNT(catLabel) FROM categories
                                    WHERE catLabel = :catLabel';

                  $placeholders 	= array( 'catLabel' => $catLabel );

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
							Bei SELECT COUNT(): Rückgabewert von COUNT() über $PDOStatement->fetchColumn() auslesen
						*/
						$count = $PDOStatement->fetchColumn();
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$count: $count <i>(" . basename(__FILE__) . ")</i></p>\n";
				
                  if( $count !== 0 ) {
                     // Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Diese Kategorie ist bereits in der DB enthalten! <i>(" . basename(__FILE__) . ")</i></p>\n";				
                     
                     // Fehlermeldung für User
                     $errorCatLabel = 'Es existiert bereits eine Kategorie mit diesem Namen!';
                     
                  } else {
                     // Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Diese Kategorie ist noch nicht in der DB. <i>(" . basename(__FILE__) . ")</i></p>\n";				
   
							#********** 3. SAVE CATEGORY DATA INTO DATABASE **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Kategoriedaten in die DB... <i>(" . basename(__FILE__) . ")</i></p>\n";
						   
                     $sql 				= 'INSERT INTO categories 
                                       (catLabel)
                                       VALUES
						 						   (:catLabel)';
						
						   $placeholders 	= array( 'catLabel' => $catLabel);
							
                     // Schritt 3 DB: Prepared Statements
							try {
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
								$PDOStatement->execute($placeholders);
								// showQuery($PDOStatement);
									
							} catch(PDOException $error) {
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							}
                     // Schritt 4 DB: Daten weiterverarbeiten
							/*
								Bei schreibenden Operationen (INSERT/UPDATE/DELETE):
								Schreiberfolg prüfen anhand der Anzahl der betroffenen Datensätze (number of affected rows).
								Diese werden über die PDOStatement-Methode rowCount() ausgelesen.
								Der Rückgabewert von rowCount() ist ein Integer; wurden keine Daten verändert, wird 0 zurückgeliefert.
							*/

                     $rowCount = $PDOStatement->rowCount();
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
                     
                     if( $rowCount !== 1 ) {
                        // Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern des Kategoriedatensatzes! <i>(" . basename(__FILE__) . ")</i></p>\n";				

                        // Fehlermeldung für User
                        $error = 'Es ist ein Fehler aufgetreten! Es konnte keine Kategorie gespeichert werden.';
                        
                        // DB-Verbindung schließen
                        dbClose($PDO, $PDOStatement);

                     } else {
                        // Erfolgsfall
                        /*
                           Bei einem INSERT die Last-Insert-ID nur nach geprüftem Schreiberfolg auslesen. 
                           Im Zweifelsfall wird hier sonst die zuletzt vergebene ID aus einem älteren 
                           Schreibvorgang zurückgeliefert.
                        */
                        $newCatID = $PDO->lastInsertID();									
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Kategoriedatensatz erfolgreich unter ID$newCatID gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
                 
								$success = '! Ein neue Kategorie wurde erfolgreich erstellt !';
                  }
					} // 1. CHECK IF CATEGORY IS ALREADY IN THE DB END
						
						
					#********** CLOSE DB CONNECTION **********#
					dbClose($PDO, $PDOStatement);
               

            } 	// FINAL FORM VALIDATION I (FIELDS VALIDATION) END
         } 		// PROCESS FORM CREATE NEW CATEGORY DATA  END **

#************************************************************************************************#

         	#*************************************************************#
         	#********** FETCH CategoryDATA FROM DATABASE *****************#
         	#*************************************************************#
                        
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
/*  		
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($categoryData);					
if(DEBUG_A)	echo "</pre>";
*/

				if ($categoryData === NULL) {
					//Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER Keine Kategorien in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
					// Fehlermeldung für User
					$errorCategoryData = 'Bitte neue Kategorie erstellen.';

				} else {
					//Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Es wurden Kategorien in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
				}
				

				#********** CLOSE DB CONNECTION **********#
				dbClose($PDO, $PDOStatement);

#************************************************************************************************#

         	#************************************************************#
         	#********** PROCESS FORM CREATE NEW BLOG ENTRY DATA *********#
         	#************************************************************#
         
				// d. Formularverarbeitung für Blogformular durchführen, Debug
				// Ausgaben, Feldprüfungen, Datenbankoperation, Erfolgs
				// /Fehlermeldungen 

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde				
				if( isset($_POST['formNewBlog']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'formNewBlog' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
				
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte von 'formNewBlog' werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$catID         		= sanitizeString($_POST['catLabel']);  
					$blogHeadline     	= sanitizeString($_POST['blogHeadline']);  
					
					$blogImageAlignment  = sanitizeString($_POST['blogImageAlignment']);  
					$blogContent      	= sanitizeString($_POST['blogContent']);  

					/*
						DEBUGGING:
						1. Ist der Variablenname korrekt geschrieben?
						2. Steht in jeder Variable der korrekte Wert?
					*/
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catID: $catID <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogHeadline: $blogHeadline <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogImageAlignment: $blogImageAlignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogContent: $blogContent <i>(" . basename(__FILE__) . ")</i></p>\n";
				
					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					/*
						[x] Validieren der Formularwerte (Feldprüfungen)
						[x] Vorbelegung der Formularfelder für den Fehlerfall 
						[x] Abschließende Prüfung, ob das Formular insgesamt fehlerfrei ist
					*/
					$errorCatSelectBox	=	validateInputString($catID);
					$errorBlogHeadline 	= 	validateInputString($blogHeadline , minLength:4);
					$errorBlogContent 	= 	validateInputString($blogContent , minLength:4 , maxLength:5000);

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorBlogHeadline: $errorBlogHeadline <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorBlogContent: $errorBlogContent <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** CHECK IF CATEGORY SELECTED MATCHES REQUIREMENTS AND ARE FILLED **********#

if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob die Kategorie SelectBox den Anforderungen entspricht... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					if( $errorCatSelectBox != NULL  ) { 
						//Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: Inhalt der CATEGORY SELECT leer <i>(" . basename(__FILE__) . ")</i></p>\n";	
						
						$errorCatSelectBox = 'Kategorie leer! Es muss vorher eine neue Kategorie erstellt!';
					} else {
						//Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Kategorie SelectBox entspricht den Anforderungen. <i>(" . basename(__FILE__) . ")</i></p>\n";				



					#********** CHECK IF BLOGHEADLINE MATCHES REQUIREMENTS AND ARE FILLED **********#
					
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob die neue Blog-Überschrift den Anforderungen entspricht... <i>(" . basename(__FILE__) . ")</i></p>\n";
	
						if( $errorBlogHeadline != NULL  ) {
							//FEHLERFALL
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Überschrift muss mind. 4 Zeichen lang sein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$errorBlogHeadline = 'Die Überschrift muss mind. 4 Zeichen lang sein!';
						} else {
							//Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Überschrift entspricht den Anforderungen. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							#********** CHECK IF BLOGCONTENT MATCHES REQUIREMENTS AND ARE FILLED **********#
							if( $errorBlogContent != NULL  ) {
								//Erfolgsfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Der Blog-Inhalt muss mind.4 bis max.255 Zeichen lang sein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$errorBlogContent = 'Der Blog-Inhalt muss mind.4 bis max.255 Zeichen lang sein!';

							} else {
								//FEHLERFALL
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der Blog-Inhalt entspricht den Anforderungen. <i>(" . basename(__FILE__) . ")</i></p>\n";	

							} // END CHECK IF BLOGCONTENT MATCHES REQUIREMENTS AND ARE FILLED END


						} // END CHECK IF BLOGHEADLINE MATCHES REQUIREMENTS AND ARE FILLED END

					} // CHECK IF CATEGORY SELECTED MATCHES REQUIREMENTS AND ARE FILLED END


					#********** FINAL FORM VALIDATION I (FIELDS VALIDATION) **********#
					if( $errorBlogHeadline !== NULL OR $errorBlogContent !== NULL OR $errorCatSelectBox !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION I (FIELDS VALIDATION): Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION (FIELDS VALIDATION): Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
						// Schritt 4 FORM: Werte verarbeiten
						
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#		

						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blogprojekt');

						#********** 1. CHECK IF BLOGHEADLINE ALREADY EXIST IN THE DB **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob bereits die Blog-Überschrift schon in der DB vorhanden ist... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
						$sql 				= 'SELECT COUNT(*) FROM blogs 
												WHERE blogHeadline = :blogHeadline';

						$placeholders 	= array('blogHeadline' => $blogHeadline);

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

						

						/*
							Bei SELECT COUNT(): Rückgabewert von COUNT() über $PDOStatement->fetchColumn() auslesen
						*/
						$count = $PDOStatement->fetchColumn();
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$count: $count <i>(" . basename(__FILE__) . ")</i></p>\n";
						if( $count !== 0 ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Diese Überschrift ist bereits in der DB enthalten ! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							// Fehlermeldung für User
							$errorBlogHeadline = 'Diese Überschrift ist bereits in der DB enthalten! Bitte wählen Sie eine andere Überschrift!';
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Diese Überschrift darf benutzt werden, da Sie nicht in der DB enthalten ist. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								

							#****************************************#
							#********** IMAGE UPLOAD START **********#
							#****************************************#
							/*
								Da im Fall von fehlerhaften Formulareingaben kein verwaistes Bild auf 
								den Server hochgeladen werden soll, findet der Bildupload erst NACH 
								der finalen Formularvalidierung statt.
								Außerdem soll zuerst sichergestellt werden, dass die verwendete Blog-Überschrift
								zulässig ist.
							*/	
/*							
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)				print_r($_FILES);					
if(DEBUG_V)				echo "</pre>";
*/

							#********** CHECK IF IMAGE UPLOAD IS ACTIVE **********#
							/*
								Beim Formulieren einer Bedingung geht es im Grunde genommen immer um zwei Fragen:
								1. Was habe ich zum Prüfen zur Verfügung?
								2. Auf welchen Zustand prüfe ich?
							*/	
							if( $_FILES['blogImagePath']['tmp_name'] === '' ) {
								// Image Upload is NOT active
if(DEBUG)					echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image Upload is NOT active. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
							} else {
								// Image Upload is active
if(DEBUG)					echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image Upload is active. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
								$validateImageUploadReturnArray = validateImageUpload( $_FILES['blogImagePath']['tmp_name'] );

/*								
if(DEBUG_A)					echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validateImageUploadReturnArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)					print_r($validateImageUploadReturnArray);					
if(DEBUG_A)					echo "</pre>";								
*/	
								
								#********** VALIDATE IMAGE UPLOAD RESULT **********#
								if( $validateImageUploadReturnArray['imageError'] !== NULL ) {
									// Fehlerfall
									/*
										AUSNAHMEFEHLER in PHP: Wenn innerhalb eines Strings auf einen assoziativen Index 
										zugegriffen wird, entfallen die Anführungszeichen für den Index.
									*/
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Bildupload: <i>'$validateImageUploadReturnArray[imageError]'</i> <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
									// Fehlermeldung für den User generieren
									$errorBlogImage = $validateImageUploadReturnArray['imageError'];
									
								} else {
									// Erfolgsfall
									/*
										AUSNAHMEFEHLER in PHP: Wenn innerhalb eines Strings auf einen assoziativen Index 
										zugegriffen wird, entfallen die Anführungszeichen für den Index.
									*/
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich nach <i>'$validateImageUploadReturnArray[imagePath]'</i> auf den Server geladen. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									

									// Neuen Bildpfad ind Bildpfadvariable speichern
									$blogImagePath = $validateImageUploadReturnArray['imagePath'];

								}

							} // IMAGE UPLOAD START END
							#********************************************************#
							
							#********** FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) **********#
							if( $errorBlogImage !== NULL ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION): Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
							}else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION): Das Formular ist komplett fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
								#********** 2. SAVE USER DATA INTO DB **********#
if(DEBUG)					echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Blogdaten in die DB... <i>(" . basename(__FILE__) . ")</i></p>\n";
			
								// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen

								$sql 				= 'INSERT INTO blogs (
														catID,
														blogHeadline,
														blogImagePath,
														blogImageAlignment,
														blogContent,
														userID
							  						) VALUES (
														:catID,
														:blogHeadline,
														:blogImagePath,
														:blogImageAlignment,
														:blogContent,
														:userID
							  						)';
					
								$placeholders = array(
									'catID' 					=> $catID,
									'blogHeadline' 		=> $blogHeadline,
									'blogImagePath' 		=> $blogImagePath,
									'blogImageAlignment' => $blogImageAlignment,
									'blogContent' 			=> $blogContent,
									'userID' 				=> $userID
								);


								// Schritt 3 DB: Prepared Statements
								try {
									// Prepare: SQL-Statement vorbereiten
									$PDOStatement = $PDO->prepare($sql);
									
									// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
									$PDOStatement->execute($placeholders);
									// showQuery($PDOStatement);
									
								} catch(PDOException $error) {
if(DEBUG) 						echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								}


								// Schritt 4 DB: Daten weiterverarbeiten
								/*
									Bei schreibenden Operationen (INSERT/UPDATE/DELETE):
									Schreiberfolg prüfen anhand der Anzahl der betroffenen Datensätze (number of affected rows).
									Diese werden über die PDOStatement-Methode rowCount() ausgelesen.
									Der Rückgabewert von rowCount() ist ein Integer; wurden keine Daten verändert, wird 0 zurückgeliefert.
								*/
								$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)					echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";

								if( $rowCount === 0 ) {
									// 'Fehlerfall'
									/*
										Bei UPDATE und bei DELETE bedeutet ein rowCount von 0 nicht zwingend einen Fehler,
										sondern viel mehr, dass es nichts zu ändern/zu löschen gab
									*/
if(DEBUG)						echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Es wurden keine Daten geändert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
									// Rückmeldung für User
									$info = 'Es wurden keine Daten geändert.';
							
								} else {
									// Erfolgsfall									
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Blogdaten wurden erfolgreich erstellt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
									$success = 'Ein neuer Blog-Eintrag wurde erfolgreich erstellt';
									
								
								} // 2. SAVE BLOG DATA INTO DB END


							} // FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) END

						} // CHECK IF BLOG HEADLINE ALREADY IN DB END

						#********** CLOSE DB CONNECTION **********#
						dbClose($PDO, $PDOStatement);

					}
					

				}// PROCESS FORM CREATE NEW BLOG ENTRY DATA END							



#************************************************************************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>PHP - Projekt Blog - Dashboard</title>
		
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
		
		<style>
			main {
				width: 50%;
			}
			aside {
				width: 40%;
				padding: 20px;
				opacity: 0.6;
				overflow: hidden;
			}
			form {
				border: 1px solid gray;
				border-radius: 5px;
				padding: 10px;
			}
			fieldset {
				width: 90%;
			}
			input[type=submit] {
				display:block;
				margin: auto;
				width: 90%;
			}
			select {
				padding: 5px;
				width: 94%;
				margin-top: 5px;
			}
         .imageAndAlign {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start;
         }
         
		</style>
		
	</head>
	
	<body>
		
		<!-- -------- PAGE HEADER -------- -->
		<header class="fright loginheader">
         <p><a href="?action=logout"><< Logout</a></p>
			<p><a href="./"><< zum Frontend</a></p>
		</header>
		<div class="clearer"></div>
		
		<!-- -------- PAGE HEADER END -------- -->
		
		
		<h1>PHP - Projekt Blog - Dashboard</h1>
		
		<main class="fleft">
			
			<p><i>Aktiver Benutzer: <?= $userFirstName ?> <?= $userLastName ?></i></p>
			
			<div class="clearer"></div>
			
			
			
			<!-- -------- USER MESSAGES START -------- -->
			<?php if( isset($error) === true ): ?>
				<h3 class="error"><?= $error ?></h3>
			<?php elseif( isset($info) === true ): ?>
				<h3 class="info"><?= $info ?></h3>
			<?php elseif( isset($success) === true ): ?>
				<h3 class="success"><?= $success ?></h3>
			<?php endif ?>
			<!-- -------- USER MESSAGES END -------- -->
			
			<br>
			
			<h2>Neuen Blog-Eintrag verfassen</h2>
			<!-- -------- FORM CREATE BLOG CONTENT START -------- -->
			<!-- 	
				Voraussetzung dafür, dass ein Formular Binärdaten (also Dateien) versenden kann, 
				ist der <form>-Parameter enctype="multipart/form-data"
			-->
			<form action="" method="POST" enctype="multipart/form-data">
				
				<input type="hidden" name="formNewBlog">			
					
					<br>
					
					<span class="error"><?= $errorCategoryData ?></span><br>
					
               <select  name="catLabel">
						<span class="error"><?= $errorCatSelectBox ?></span>
                  <?php foreach ( $categoryData as $category): ?>
                     <option value="<?php echo $category['catID'] ?>"
							<?php if (isset($catID) && $catID == $category['catID']) echo 'selected'; ?>> 
							<?php echo $category['catLabel']; ?></option>
                  <?php endforeach; ?>
               </select>

					<br><br><br><br><br>
					
					<label>Überschrift:</label>
					<span class="error"><?= $errorBlogHeadline ?></span><br>
					<input type="text" value="<?= $blogHeadline ?>" name="blogHeadline" placeholder="Überschrift"><br>
					
					<br>
					<br>
					<br>
					
					<fieldset>
						<legend>&nbsp;Bild hochladen&nbsp;</legend>
						<!-- -------- INFOTEXT FOR IMAGE UPLOAD START -------- -->
						<!-- <p class="small">
							Erlaubt sind Bilder des Typs 						
							<?php $allowedMimeTypes = implode(', ', IMAGE_ALLOWED_MIME_TYPES) ?>
							<?php $allowedMimeTypes = str_replace('.jpg, .jpg', '.jpg', $allowedMimeTypes) ?>
							<?php echo $allowedMimeTypes ?>.
							<br>
							Die Bildbreite darf <?= IMAGE_MAX_WIDTH ?> Pixel nicht übersteigen.<br>
							Die Bildhöhe darf <?= IMAGE_MAX_HEIGHT ?> Pixel nicht übersteigen.<br>
							Die Dateigröße darf <?= IMAGE_MAX_SIZE/1024 ?>kB nicht übersteigen.
						</p> -->
						<!-- -------- INFOTEXT FOR IMAGE UPLOAD END -------- -->
                  
                   <div class="imageAndAlign">
                     <div>
                        <span class="error"><?= $errorBlogImage ?></span>
                        <input type="file" name="blogImagePath"><br>
                     </div>
                     <div>
                     
                        <select name="blogImageAlignment" >
									<option value="left" <?php if (isset($blogImageAlignment) && $blogImageAlignment == 'left') echo 'selected'; ?>>align left</option>
                    			<option value="right" <?php if (isset($blogImageAlignment) && $blogImageAlignment == 'right') echo 'selected'; ?>>align right</option>
                        </select>
                     </div>
                     
                   </div>
						
					</fieldset>
					
					<br>
					<br>
					
					<fieldset>
						<legend>&nbsp;Blog-Inhalt&nbsp;</legend>
						
						<span class="error"><?= $errorBlogContent ?></span><br>
						
						<textarea name="blogContent" placeholder="Text..."><?= isset($blogContent) ? htmlspecialchars($blogContent) : '' ?></textarea>
						
					</fieldset>
					
					<br>
					
					<input type="submit" value="Veröffentlichen">
					<br>		
			</form>			
			<!-- -------- FORM CREATE BLOG CONTENT END -------- -->
			
		</main>
		
		<aside class="fright">
         <br><br><br>
			<h2>Neue Kategorie anlegen</h2>
         <!-- -------- FORM CREATE NEW CATEGORY START -------- -->
         <!-- 	
				Voraussetzung dafür, dass ein Formular Binärdaten (also Dateien) versenden kann, 
				ist der <form>-Parameter enctype="multipart/form-data"
			-->
         <form action="" method="POST" enctype="multipart/form-data" >
         
            <input type="hidden" name="formNewCategory">	
            <span class="error"><?= $errorCatLabel ?></span><br>	
            <input type="text" name="catLabel" placeholder="Name der Kategorie">
            <input type="submit" value="Neue Kategorie anlegen">
            <br>
         </form>		
         <!-- -------- FORM CREATE NEW CATEGORY END -------- -->	
			
		</aside>
		
		<div class="clearer"></div>
		
		
	</body>
	
</html>