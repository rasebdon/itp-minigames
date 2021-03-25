-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2021 at 04:01 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itp-minigames`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `CommentID` int(11) NOT NULL,
  `Text` text NOT NULL,
  `FK_PostID` int(11) NOT NULL,
  `FK_UserID` int(11) NOT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `favorite`
--

CREATE TABLE `favorite` (
  `FK_UserID` int(11) NOT NULL,
  `FK_GameID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `ForumID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `GameID` int(11) NOT NULL,
  `FK_UserID` int(11) NOT NULL,
  `FK_ForumID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Version` varchar(20) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `UploadDate` datetime DEFAULT NULL,
  `PlayCount` int(11) DEFAULT NULL,
  `Verified` tinyint(1) NOT NULL,
  `SourcePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `game_genre`
--

CREATE TABLE `game_genre` (
  `FK_GameID` int(11) NOT NULL,
  `FK_GenreID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `game_platform`
--

CREATE TABLE `game_platform` (
  `FK_GameID` int(11) NOT NULL,
  `FK_PlatformID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `GenreID` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `picture`
--

CREATE TABLE `picture` (
  `PictureID` int(11) NOT NULL,
  `SourcePath` varchar(255) DEFAULT NULL,
  `ThumbnailPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `picture_comment`
--

CREATE TABLE `picture_comment` (
  `FK_CommentID` int(11) NOT NULL,
  `FK_PictureID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `picture_game`
--

CREATE TABLE `picture_game` (
  `FK_GameID` int(11) NOT NULL,
  `FK_PictureID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `picture_post`
--

CREATE TABLE `picture_post` (
  `FK_PostID` int(11) NOT NULL,
  `FK_PictureID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `platform`
--

CREATE TABLE `platform` (
  `PlatformID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `PostID` int(11) NOT NULL,
  `Title` tinytext NOT NULL,
  `FK_ForumID` int(11) NOT NULL,
  `FK_UserID` int(11) DEFAULT NULL,
  `Text` text NOT NULL,
  `Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `FK_UserID` int(11) NOT NULL,
  `FK_GameID` int(11) NOT NULL,
  `Text` text DEFAULT NULL,
  `Date` datetime DEFAULT NULL,
  `Rating` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `social`
--

CREATE TABLE `social` (
  `SocialID` int(11) NOT NULL,
  `FK_UserID` int(11) NOT NULL,
  `Link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(25) NOT NULL,
  `LastName` varchar(25) NOT NULL,
  `Username` varchar(40) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `Usertype` varchar(20) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `SessionID` int(11) DEFAULT NULL,
  `FK_PictureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vote_comment`
--

CREATE TABLE `vote_comment` (
  `FK_UserID` int(11) NOT NULL,
  `FK_CommentID` int(11) NOT NULL,
  `Vote` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vote_post`
--

CREATE TABLE `vote_post` (
  `FK_UserID` int(11) NOT NULL,
  `FK_PostID` int(11) NOT NULL,
  `Vote` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `comment_post_PostID_fk` (`FK_PostID`),
  ADD KEY `comment_user_UserID_fk` (`FK_UserID`);

--
-- Indexes for table `favorite`
--
ALTER TABLE `favorite`
  ADD PRIMARY KEY (`FK_UserID`,`FK_GameID`),
  ADD KEY `favorite_game_GameID_fk` (`FK_GameID`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`ForumID`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`GameID`),
  ADD KEY `game_forum_ForumID_fk` (`FK_ForumID`),
  ADD KEY `game_user_UserID_fk` (`FK_UserID`);

--
-- Indexes for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD PRIMARY KEY (`FK_GameID`,`FK_GenreID`),
  ADD KEY `game_genre_genre_GenreID_fk` (`FK_GenreID`);

--
-- Indexes for table `game_platform`
--
ALTER TABLE `game_platform`
  ADD PRIMARY KEY (`FK_GameID`,`FK_PlatformID`),
  ADD KEY `game_platform_platform_PlatformID_fk` (`FK_PlatformID`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`GenreID`),
  ADD UNIQUE KEY `genre_Name_uindex` (`Name`);

--
-- Indexes for table `picture`
--
ALTER TABLE `picture`
  ADD PRIMARY KEY (`PictureID`);

--
-- Indexes for table `picture_comment`
--
ALTER TABLE `picture_comment`
  ADD PRIMARY KEY (`FK_CommentID`,`FK_PictureID`),
  ADD KEY `picture_comment_picture_PictureID_fk` (`FK_PictureID`);

--
-- Indexes for table `picture_game`
--
ALTER TABLE `picture_game`
  ADD PRIMARY KEY (`FK_GameID`,`FK_PictureID`),
  ADD KEY `picture_game_picture_PictureID_fk` (`FK_PictureID`);

--
-- Indexes for table `picture_post`
--
ALTER TABLE `picture_post`
  ADD PRIMARY KEY (`FK_PostID`,`FK_PictureID`),
  ADD KEY `picture_post_picture_PictureID_fk` (`FK_PictureID`);

--
-- Indexes for table `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`PlatformID`),
  ADD UNIQUE KEY `platform_Name_uindex` (`Name`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `post_forum_ForumID_fk` (`FK_ForumID`),
  ADD KEY `post_user_UserID_fk` (`FK_UserID`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`FK_UserID`,`FK_GameID`),
  ADD KEY `rating_game_GameID_fk` (`FK_GameID`);

--
-- Indexes for table `social`
--
ALTER TABLE `social`
  ADD PRIMARY KEY (`SocialID`),
  ADD KEY `social_user_UserID_fk` (`FK_UserID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `user_Email_uindex` (`Email`),
  ADD UNIQUE KEY `user_Username_uindex` (`Username`),
  ADD KEY `user_picture_PictureID_fk` (`FK_PictureID`);

--
-- Indexes for table `vote_comment`
--
ALTER TABLE `vote_comment`
  ADD PRIMARY KEY (`FK_UserID`,`FK_CommentID`),
  ADD KEY `vote_comment_comment_CommentID_fk` (`FK_CommentID`);

--
-- Indexes for table `vote_post`
--
ALTER TABLE `vote_post`
  ADD PRIMARY KEY (`FK_UserID`,`FK_PostID`),
  ADD KEY `vote_post_post_PostID_fk` (`FK_PostID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum`
--
ALTER TABLE `forum`
  MODIFY `ForumID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `GameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `GenreID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `picture`
--
ALTER TABLE `picture`
  MODIFY `PictureID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform`
--
ALTER TABLE `platform`
  MODIFY `PlatformID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social`
--
ALTER TABLE `social`
  MODIFY `SocialID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_post_PostID_fk` FOREIGN KEY (`FK_PostID`) REFERENCES `post` (`PostID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `favorite`
--
ALTER TABLE `favorite`
  ADD CONSTRAINT `favorite_game_GameID_fk` FOREIGN KEY (`FK_GameID`) REFERENCES `game` (`GameID`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_forum_ForumID_fk` FOREIGN KEY (`FK_ForumID`) REFERENCES `forum` (`ForumID`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD CONSTRAINT `game_genre_game_GameID_fk` FOREIGN KEY (`FK_GameID`) REFERENCES `game` (`GameID`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_genre_genre_GenreID_fk` FOREIGN KEY (`FK_GenreID`) REFERENCES `genre` (`GenreID`) ON DELETE CASCADE;

--
-- Constraints for table `game_platform`
--
ALTER TABLE `game_platform`
  ADD CONSTRAINT `game_platform_game_GameID_fk` FOREIGN KEY (`FK_GameID`) REFERENCES `game` (`GameID`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_platform_platform_PlatformID_fk` FOREIGN KEY (`FK_PlatformID`) REFERENCES `platform` (`PlatformID`) ON DELETE CASCADE;

--
-- Constraints for table `picture_comment`
--
ALTER TABLE `picture_comment`
  ADD CONSTRAINT `picture_comment_comment_CommentID_fk` FOREIGN KEY (`FK_CommentID`) REFERENCES `comment` (`CommentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `picture_comment_picture_PictureID_fk` FOREIGN KEY (`FK_PictureID`) REFERENCES `picture` (`PictureID`) ON DELETE CASCADE;

--
-- Constraints for table `picture_game`
--
ALTER TABLE `picture_game`
  ADD CONSTRAINT `picture_game_game_GameID_fk` FOREIGN KEY (`FK_GameID`) REFERENCES `game` (`GameID`) ON DELETE CASCADE,
  ADD CONSTRAINT `picture_game_picture_PictureID_fk` FOREIGN KEY (`FK_PictureID`) REFERENCES `picture` (`PictureID`) ON DELETE CASCADE;

--
-- Constraints for table `picture_post`
--
ALTER TABLE `picture_post`
  ADD CONSTRAINT `picture_post_picture_PictureID_fk` FOREIGN KEY (`FK_PictureID`) REFERENCES `picture` (`PictureID`) ON DELETE CASCADE,
  ADD CONSTRAINT `picture_post_post_PostID_fk` FOREIGN KEY (`FK_PostID`) REFERENCES `post` (`PostID`) ON DELETE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_forum_ForumID_fk` FOREIGN KEY (`FK_ForumID`) REFERENCES `forum` (`ForumID`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_game_GameID_fk` FOREIGN KEY (`FK_GameID`) REFERENCES `game` (`GameID`) ON DELETE CASCADE,
  ADD CONSTRAINT `rating_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `social`
--
ALTER TABLE `social`
  ADD CONSTRAINT `social_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_picture_PictureID_fk` FOREIGN KEY (`FK_PictureID`) REFERENCES `picture` (`PictureID`) ON DELETE CASCADE;

--
-- Constraints for table `vote_comment`
--
ALTER TABLE `vote_comment`
  ADD CONSTRAINT `vote_comment_comment_CommentID_fk` FOREIGN KEY (`FK_CommentID`) REFERENCES `comment` (`CommentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_comment_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `vote_post`
--
ALTER TABLE `vote_post`
  ADD CONSTRAINT `vote_post_post_PostID_fk` FOREIGN KEY (`FK_PostID`) REFERENCES `post` (`PostID`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_post_user_UserID_fk` FOREIGN KEY (`FK_UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
