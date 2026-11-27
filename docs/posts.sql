-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Gegenereerd op: 27 nov 2026 om 08:54
-- Serverversie: 8.4.11
-- PHP-versie: 8.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `video_timestamp` decimal(10,3) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `posts`
--

INSERT INTO `posts` (`id`, `title`, `place`, `text_content`, `date`, `latitude`, `longitude`, `video_timestamp`, `image`, `image2`, `image3`, `image4`, `image5`) VALUES
(6, '2 Maartensbrug', 'Maartensbrug, Utrecht', 'De Maartensbrug is de oudste brug over de Utrechtse Oudegracht. De brug werd oorspronkelijk\r\nde Borchbrug genoemd, omdat hij toegang gaf tot de bisschoppelijke burcht op de plek van het\r\nhuidige Domplein. De eerste stenen brug werd in 1404 gebouwd en in 1906 verbreed om ruimte\r\nte maken voor het toenemende verkeer en de paardentram. De huidige naam verwijst naar SintMaarten, de beschermheilige van Utrecht.', '1993-01-13', 52.0904111, 5.1204000, 94.019, 'posts/M6z70Dr0tqaMOrn8oUe0IXABu8qI4cDnpLMTFvWY.jpg', NULL, NULL, NULL, NULL),
(7, '1 De Gaardbrug', 'Utrecht', 'De Gaardbrug is een van de bruggen over de Utrechtse Oudegracht en ontleent haar naam aan\r\nde vroegere bisschoppelijke tuin, die ook wel de ‘gaard’ werd genoemd. Vanaf de brug is er zicht\r\nop de Oudegracht en de Domtoren, dit maakt het een populaire plek om foto’s te maken.', '2022-05-30', 52.0891890, 5.1211500, 0.000, 'posts/pPYItKVgof1Bt1JB8Dd2wiB2d7D0G65gNhatnxDZ.jpg', NULL, NULL, NULL, NULL),
(8, '3 Kalisbrug', 'Utrecht', 'De Kalisbrug, ook wel Visbrug genoemd, vormde een belangrijk onderdeel van de Vismarkt en\r\ndiende als visafslag. Wanneer er voldoende vis was aangevoerd, kondigde de stadsomroeper de\r\nverkoop aan. De vis werd vers gehouden in viskaren die in de gracht hingen en verkocht vanaf\r\nvisbanken op de brug. De traditionele vismarkt verdween aan het begin van de 20e eeuw, maar in\r\nde winkels aan de Vismarkt bleven nog lange tijd visverkopers actief. De laatste traditionele\r\nviswinkel sloot in 1970.', '1873-01-01', 52.0908860, 5.1202060, 129.435, 'posts/eFgeS9XM8dCF6kE5nCBLVSjKZqWuy4jO3u9ZHX48.jpg', NULL, NULL, NULL, NULL),
(9, '4 Stadhuisbrug en het stadhuis', 'Utrecht', 'De Stadhuisbrug dankt haar naam aan het Utrechtse stadhuis dat direct aan de brug ligt. Op\r\ndeze plek zetelde het stadsbestuur al vanaf de 14e eeuw. De brug ontstond in de 16e eeuw, toen\r\ntwee bestaande bruggen, de Broodbrug en de Huidenbrug, met elkaar werden verbonden en\r\noverkluisd. Het plein dat hierdoor ontstond, werd gebruikt voor openbare afkondigingen van\r\nbesluiten van het stadsbestuur en kreeg later de naam Stadhuisbrug.', '1976-01-01', 52.0915670, 5.1196480, 182.912, 'posts/vgp0Q6BGLxT1KaLWGSBmQle1ftvypvAEULIKhWmk.jpg', NULL, NULL, NULL, NULL);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
