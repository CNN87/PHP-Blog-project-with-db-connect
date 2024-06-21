-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Jun 2024 um 11:31
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `blogprojekt`
--
CREATE DATABASE IF NOT EXISTS `blogprojekt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blogprojekt`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `blogID` int(11) NOT NULL,
  `blogHeadline` varchar(255) NOT NULL,
  `blogImagePath` varchar(255) DEFAULT NULL,
  `blogImageAlignment` varchar(10) NOT NULL,
  `blogContent` text NOT NULL,
  `blogDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `catID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `blogs`
--

INSERT INTO `blogs` (`blogID`, `blogHeadline`, `blogImagePath`, `blogImageAlignment`, `blogContent`, `blogDate`, `catID`, `userID`) VALUES
(1, 'Enimen neues Album Top1', './uploaded_images/1925512244_28yx11whsca6do7b_270rupj9tzgn4ql3mf_vk4893650e5i_17188753459711.jpg', 'left', 'Jemand musste Josef K. verleumdet haben, denn ohne dass er etwas Böses getan hätte, wurde er eines Morgens verhaftet. »Wie ein Hund!« sagte er, es war, als sollte die Scham ihn überleben. Als Gregor Samsa eines Morgens aus unruhigen Träumen erwachte, fand er sich in seinem Bett zu einem ungeheueren Ungeziefer verwandelt. Und es war ihnen wie eine Bestätigung ihrer neuen Träume und guten Absichten, als am Ziele ihrer Fahrt die Tochter als erste sich erhob und ihren jungen Körper dehnte. »Es ist ein eigentümlicher Apparat«, sagte der Offizier zu dem Forschungsreisenden und überblickte mit einem gewissermaßen bewundernden Blick den ihm doch wohlbekannten Apparat. Sie hätten noch ins Boot springen können, aber der Reisende hob ein schweres, geknotetes Tau vom Boden, drohte ihnen damit und hielt sie dadurch von dem Sprunge ab. In den letzten Jahrzehnten ist das Interesse an Hungerkünstlern sehr zurückgegangen. Aber sie überwanden sich, umdrängten den Käfig und wollten sich gar nicht fortrühren.Jemand musste Josef K. verleumdet haben, denn ohne dass er etwas Böses getan hätte, wurde er eines Morgens verhaftet. »Wie ein Hund!« sagte er, es war, als sollte die Scham ihn überleben. Als Gregor Samsa eines Morgens aus unruhigen Träumen erwachte, fand er sich in seinem Bett zu einem ungeheueren Ungeziefer verwandelt. Und es war ihnen wie eine Bestätigung ihrer neuen Träume und guten Absichten, als am Ziele ihrer Fahrt die Tochter als erste sich erhob und ihren jungen Körper dehnte. »Es ist ein eigentümlicher Apparat«, sagte der Offizier zu dem Forschungsreisenden und überblickte mit einem gewissermaßen bewundernden Blick den ihm doch wohlbekannten Apparat. Sie hätten noch ins Boot springen können, aber der Reisende hob ein schweres, geknotetes Tau vom Boden, drohte ihnen damit und hielt sie dadurch von dem Sprunge ab. In den letzten Jahrzehnten ist das Interesse an Hungerkünstlern sehr zurückgegangen. Aber', '2024-06-20 09:22:25', 1, 3),
(2, 'Taylor Swift ausverkauftes Konzert in Berlin', NULL, 'right', 'Er hörte leise Schritte hinter sich. Das bedeutete nichts Gutes. Wer würde ihm schon folgen, spät in der Nacht und dazu noch in dieser engen Gasse mitten im übel beleumundeten Hafenviertel? Gerade jetzt, wo er das Ding seines Lebens gedreht hatte und mit der Beute verschwinden wollte! Hatte einer seiner zahllosen Kollegen dieselbe Idee gehabt, ihn beobachtet und abgewartet, um ihn nun um die Früchte seiner Arbeit zu erleichtern? Oder gehörten die Schritte hinter ihm zu einem der unzähligen Gesetzeshüter dieser Stadt, und die stählerne Acht um seine Handgelenke würde gleich zuschnappen? Er konnte die Aufforderung stehen zu bleiben schon hören. Gehetzt sah er sich um. Plötzlich erblickte er den schmalen Durchgang. Blitzartig drehte er sich nach rechts und verschwand zwischen den beiden Gebäuden. Beinahe wäre er dabei über den umgestürzten Mülleimer gefallen, der mitten im Weg lag. Er versuchte, sich in der Dunkelheit seinen Weg zu ertasten und erstarrte: Anscheinend gab es keinen anderen Ausweg aus diesem kleinen Hof als den Durchgang, durch den er gekommen war. Die Schritte wurden lauter und lauter, er sah eine dunkle Gestalt um die Ecke biegen. Fieberhaft irrten seine Augen durch die nächtliche Dunkelheit und suchten einen Ausweg. War jetzt wirklich alles vorbei,', '2024-06-20 09:23:27', 1, 3),
(3, 'Ein Hersteller zeigt eine Grafikkarte im Design von Elden Ring, verlangt dafür nur etwa 200 Euro', './uploaded_images/1988289914_jxn426e001ps_byav5igz29h_dwo8797qkfm638t3uc45rl1_17188755298294.jpg', 'left', 'The fallen leaves tell a story.\r\nThe great Elden Ring was shattered.\r\nIn our home, cross the fog, the Lands Between.\r\nNow, Queen Marika the Eternal is nowhere to be found,\r\nand in the Night of the Black Knives, Godwyn the Golden was the first to perish.\r\nSoon, Marika&apos;s offspring, demigods all, claimed the shards of the Elden Ring.\r\nThe mad taint of their newfound strength triggered the Shattering.\r\nA war from which no lord arose.\r\nA war leading to abandonment by the Greater Will.\r\nArise now, ye Tarnished.Ye dead, who yet live.', '2024-06-20 09:25:29', 2, 3),
(4, 'Kaffemaschinen verbrauchen zu viel Strom', './uploaded_images/1129837943_mne9tpbsl278rckivw6z8qh09g_32udx0514175_64o3yafj_17188755950261.jpg', 'right', 'The fallen leaves tell a story.\r\nThe great Elden Ring was shattered.\r\nIn our home, cross the fog, the Lands Between.\r\nNow, Queen Marika the Eternal is nowhere to be found,\r\nand in the Night of the Black Knives, Godwyn the Golden was the first to perish.\r\nSoon, Marika&apos;s offspring, demigods all, claimed the shards of the Elden Ring.\r\nThe mad taint of their newfound strength triggered the Shattering.\r\nA war from which no lord arose.\r\nA war leading to abandonment by the Greater Will.\r\nArise now, ye Tarnished.Ye dead, who yet live.', '2024-06-20 09:26:35', 2, 3),
(5, 'Energiewende: VDE arbeitet an neuen Anschlussregeln', './uploaded_images/648955888_0397k1x552o_6jc70h3sm8pezvlnrby464ut_a8dw91qgi2f_17188757086936.jpg', 'left', 'Die Energiewende wird Millionen neuer Verbrauchs- und Erzeugungsanlagen mit sich bringen. Um die Stromnetze dafür fit zu machen und die Wende zu beschleunigen, arbeitet das Forum Netztechnik/Netzbetrieb im VDE (VDE FNN) momentan an neuen Technischen Anschlussregeln (TAR). Sie sollen demnächst zur Konsultation vorliegen und 2025 in Kraft treten.', '2024-06-20 09:28:28', 4, 3),
(6, 'Zwischen Tradition und Bruch, wie die junge Generation Luxus neu definiert', './uploaded_images/1002419046__7lznf4w103cm9qs2ix9o3j8pv724k6ah68tg5_51beyur0d_17188757765127.jpg', 'right', 'Wie wird in einer Welt, die von sozialen Netzwerken und Instant Messaging beherrscht wird, der Begriff Luxus an die jüngere Generation weitergegeben? Welche Werte schätzt sie und wie unterscheidet sie sich in ihrem Umgang mit Luxus von ihren Eltern?', '2024-06-20 09:29:36', 3, 3),
(7, 'Mailänder Modewoche erweitert Programm um einen zusätzlichen Tag', NULL, 'right', 'Um den wachsenden Anforderungen der Modeindustrie gerecht zu werden, hat die Milan Fashion Week (MFW) eine Erweiterung ihres Programms für die September-Ausgabe der Modewoche angekündigt. Die Veranstaltung, die zwischen den Fashion Weeks in London und Paris liegt, wird in diesem Jahr vom 17. bis 23. September stattfinden und damit einen Tag länger dauern als bisher.\r\n\r\nDie Entscheidung zur Verlängerung der Modewoche fiel nach einjährigen Verhandlungen zwischen dem italienischen Modeverband Camera Nazionale della Moda Italiana (CNMI) und den Verbänden in Großbritannien, den USA und Frankreich. Der British Fashion Council (BFC), der Council of Fashion Designers of America (CFDA) und die Fédération de la Haute Couture et de la Mode (FHCM) haben gemeinsam mit dem CNMI den Modekalender', '2024-06-20 09:30:43', 3, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `catID` int(11) NOT NULL,
  `catLabel` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`catID`, `catLabel`) VALUES
(1, 'Musik'),
(2, 'Elektronik'),
(3, 'Mode'),
(4, 'Business');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userFirstName` varchar(255) NOT NULL,
  `userLastName` varchar(255) NOT NULL,
  `userEmail` varchar(255) NOT NULL,
  `userCity` varchar(255) DEFAULT NULL,
  `userPassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`userID`, `userFirstName`, `userLastName`, `userEmail`, `userCity`, `userPassword`) VALUES
(3, 'Ingmar', 'Ehrig', 'a@b.c', 'Berlin', '$2y$10$slxODNbJUEktd.jHUE312uLgru6QgF8Ysge2r7zR9toEYPQbeoVrq'),
(4, 'Sinn', 'Sinnlos', 'sinn@los.de', 'Berlin', '$2y$10$slxODNbJUEktd.jHUE312uLgru6QgF8Ysge2r7zR9toEYPQbeoVrq');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blogID`),
  ADD KEY `fk_blog_userid` (`userID`),
  ADD KEY `fk_blog_catid` (`catID`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`catID`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `blogs`
--
ALTER TABLE `blogs`
  MODIFY `blogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `catID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `fk_blog_catid` FOREIGN KEY (`catID`) REFERENCES `categories` (`catID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_blog_userid` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
