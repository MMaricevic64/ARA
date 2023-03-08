-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2020 at 01:17 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ara_sustav`
--

-- --------------------------------------------------------

--
-- Table structure for table `eko_akcije`
--

CREATE TABLE `eko_akcije` (
  `ID_eko_akcije` int(11) NOT NULL,
  `Naziv` varchar(30) NOT NULL,
  `PocetakAkcije` datetime NOT NULL,
  `ID_lokaliteta` int(11) NOT NULL,
  `ID_korisnika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `komentari`
--

CREATE TABLE `komentari` (
  `ID_komentara` int(11) NOT NULL,
  `Komentar` text NOT NULL,
  `ID_lokaliteta` int(11) DEFAULT NULL,
  `ID_korisnika` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `ID_korisnika` int(11) NOT NULL,
  `Ime` varchar(25) NOT NULL,
  `Prezime` varchar(25) NOT NULL,
  `Licenca` varchar(2) NOT NULL,
  `Email` varchar(25) NOT NULL,
  `Lozinka` varchar(128) NOT NULL,
  `Slika` text NOT NULL,
  `ID_kluba` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`ID_korisnika`, `Ime`, `Prezime`, `Licenca`, `Email`, `Lozinka`, `Slika`, `ID_kluba`) VALUES
(17, 'Admin', 'Admin', 'R1', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'placeholderimg.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `korisnik_ekoakcija`
--

CREATE TABLE `korisnik_ekoakcija` (
  `ID_korisnika` int(11) DEFAULT NULL,
  `ID_ekoakcije` int(11) DEFAULT NULL,
  `ZavrsetakAkcije` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `korisnik_uron`
--

CREATE TABLE `korisnik_uron` (
  `ID_korisnika` int(11) DEFAULT NULL,
  `ID_urona` int(11) DEFAULT NULL,
  `TlakBoceK` smallint(6) DEFAULT NULL,
  `KrajUrona` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lokaliteti`
--

CREATE TABLE `lokaliteti` (
  `ID_lokaliteta` int(11) NOT NULL,
  `Naziv` varchar(30) NOT NULL,
  `Opis` text NOT NULL,
  `Lat` float NOT NULL,
  `Lng` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ron_klubovi`
--

CREATE TABLE `ron_klubovi` (
  `ID_kluba` int(11) NOT NULL,
  `Ime` varchar(50) NOT NULL,
  `Slika` text NOT NULL,
  `ID_voditelja` int(11) DEFAULT NULL,
  `Validacija` varchar(20) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `uroni`
--

CREATE TABLE `uroni` (
  `ID_urona` int(11) NOT NULL,
  `Naziv` varchar(30) NOT NULL,
  `Vrsta` varchar(30) NOT NULL,
  `Dubina` smallint(6) NOT NULL,
  `VolumenBoce` smallint(6) NOT NULL,
  `TlakBoceP` smallint(6) NOT NULL,
  `PocetakUrona` datetime NOT NULL,
  `ID_lokaliteta` int(11) NOT NULL,
  `ID_korisnika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `zahtjevi`
--

CREATE TABLE `zahtjevi` (
  `ID_zahtjeva` int(11) NOT NULL,
  `Opis` varchar(40) NOT NULL,
  `ID_podnositelja` int(11) NOT NULL,
  `ID_kluba` int(11) DEFAULT NULL,
  `ID_urona` int(11) DEFAULT NULL,
  `ID_eko_akcije` int(11) DEFAULT NULL,
  `Status_zahtjeva` varchar(20) DEFAULT 'obrada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eko_akcije`
--
ALTER TABLE `eko_akcije`
  ADD PRIMARY KEY (`ID_eko_akcije`),
  ADD KEY `FOR_ECO_USER` (`ID_korisnika`),
  ADD KEY `FOR_ECO_LOC` (`ID_lokaliteta`);

--
-- Indexes for table `komentari`
--
ALTER TABLE `komentari`
  ADD PRIMARY KEY (`ID_komentara`),
  ADD KEY `FOR_LOCALITY` (`ID_lokaliteta`),
  ADD KEY `FOR_USER` (`ID_korisnika`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`ID_korisnika`),
  ADD KEY `FOR_CLUB` (`ID_kluba`);

--
-- Indexes for table `korisnik_ekoakcija`
--
ALTER TABLE `korisnik_ekoakcija`
  ADD KEY `FOR_USER_USER_ECO` (`ID_korisnika`),
  ADD KEY `FOR_ECO_ECO_USER` (`ID_ekoakcije`);

--
-- Indexes for table `korisnik_uron`
--
ALTER TABLE `korisnik_uron`
  ADD KEY `FOR_USER_USER` (`ID_korisnika`),
  ADD KEY `FOR_DIVE_DIVE` (`ID_urona`);

--
-- Indexes for table `lokaliteti`
--
ALTER TABLE `lokaliteti`
  ADD PRIMARY KEY (`ID_lokaliteta`);

--
-- Indexes for table `ron_klubovi`
--
ALTER TABLE `ron_klubovi`
  ADD PRIMARY KEY (`ID_kluba`),
  ADD KEY `FORUSER` (`ID_voditelja`);

--
-- Indexes for table `uroni`
--
ALTER TABLE `uroni`
  ADD PRIMARY KEY (`ID_urona`),
  ADD KEY `FOR_LOC_DIVE` (`ID_lokaliteta`),
  ADD KEY `FOR_USER_DIVE` (`ID_korisnika`);

--
-- Indexes for table `zahtjevi`
--
ALTER TABLE `zahtjevi`
  ADD PRIMARY KEY (`ID_zahtjeva`),
  ADD KEY `FOR_REQ_USER` (`ID_podnositelja`),
  ADD KEY `FOR_REQ_CLUB` (`ID_kluba`),
  ADD KEY `FOR_REQ_DIVE` (`ID_urona`),
  ADD KEY `FOR_REQ_ECO` (`ID_eko_akcije`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eko_akcije`
--
ALTER TABLE `eko_akcije`
  MODIFY `ID_eko_akcije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `komentari`
--
ALTER TABLE `komentari`
  MODIFY `ID_komentara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `ID_korisnika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lokaliteti`
--
ALTER TABLE `lokaliteti`
  MODIFY `ID_lokaliteta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `ron_klubovi`
--
ALTER TABLE `ron_klubovi`
  MODIFY `ID_kluba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `uroni`
--
ALTER TABLE `uroni`
  MODIFY `ID_urona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `zahtjevi`
--
ALTER TABLE `zahtjevi`
  MODIFY `ID_zahtjeva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eko_akcije`
--
ALTER TABLE `eko_akcije`
  ADD CONSTRAINT `FOR_ECO_LOC` FOREIGN KEY (`ID_lokaliteta`) REFERENCES `lokaliteti` (`ID_lokaliteta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_ECO_USER` FOREIGN KEY (`ID_korisnika`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `komentari`
--
ALTER TABLE `komentari`
  ADD CONSTRAINT `FOR_LOCALITY` FOREIGN KEY (`ID_lokaliteta`) REFERENCES `lokaliteti` (`ID_lokaliteta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_USER` FOREIGN KEY (`ID_korisnika`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD CONSTRAINT `FOR_CLUB` FOREIGN KEY (`ID_kluba`) REFERENCES `ron_klubovi` (`ID_kluba`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `korisnik_ekoakcija`
--
ALTER TABLE `korisnik_ekoakcija`
  ADD CONSTRAINT `FOR_ECO_ECO_USER` FOREIGN KEY (`ID_ekoakcije`) REFERENCES `eko_akcije` (`ID_eko_akcije`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_USER_USER_ECO` FOREIGN KEY (`ID_korisnika`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `korisnik_uron`
--
ALTER TABLE `korisnik_uron`
  ADD CONSTRAINT `FOR_DIVE_DIVE` FOREIGN KEY (`ID_urona`) REFERENCES `uroni` (`ID_urona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_USER_USER` FOREIGN KEY (`ID_korisnika`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ron_klubovi`
--
ALTER TABLE `ron_klubovi`
  ADD CONSTRAINT `FORUSER` FOREIGN KEY (`ID_voditelja`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `uroni`
--
ALTER TABLE `uroni`
  ADD CONSTRAINT `FOR_LOC_DIVE` FOREIGN KEY (`ID_lokaliteta`) REFERENCES `lokaliteti` (`ID_lokaliteta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_USER_DIVE` FOREIGN KEY (`ID_korisnika`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zahtjevi`
--
ALTER TABLE `zahtjevi`
  ADD CONSTRAINT `FOR_REQ_CLUB` FOREIGN KEY (`ID_kluba`) REFERENCES `ron_klubovi` (`ID_kluba`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_REQ_DIVE` FOREIGN KEY (`ID_urona`) REFERENCES `uroni` (`ID_urona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_REQ_ECO` FOREIGN KEY (`ID_eko_akcije`) REFERENCES `eko_akcije` (`ID_eko_akcije`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FOR_REQ_USER` FOREIGN KEY (`ID_podnositelja`) REFERENCES `korisnici` (`ID_korisnika`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
