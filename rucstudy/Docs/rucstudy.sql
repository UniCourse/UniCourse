-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2014 at 12:23 PM
-- Server version: 5.5.35-0ubuntu0.13.10.2
-- PHP Version: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rucstudy`
--
CREATE DATABASE IF NOT EXISTS `rucstudy` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rucstudy`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `ano` char(10) NOT NULL,
  `apsw` varchar(41) NOT NULL,
  PRIMARY KEY (`ano`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `apply`
--

DROP TABLE IF EXISTS `apply`;
CREATE TABLE `apply` (
  `app_cno` varchar(16) NOT NULL DEFAULT '',
  `app_sno` char(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`app_cno`,`app_sno`),
  KEY `app_sno` (`app_sno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assi_course`
--

DROP TABLE IF EXISTS `assi_course`;
CREATE TABLE `assi_course` (
  `ano` char(10) NOT NULL,
  `cno` varchar(16) NOT NULL,
  PRIMARY KEY (`ano`,`cno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `cno` varchar(16) NOT NULL DEFAULT '',
  `cname` varchar(255) NOT NULL,
  `cschool` varchar(31) DEFAULT NULL,
  `tno` char(8) NOT NULL,
  `checkway` text,
  `intro` text,
  `cnotes` text,
  PRIMARY KEY (`cno`),
  KEY `tno` (`tno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coursefile`
--

DROP TABLE IF EXISTS `coursefile`;
CREATE TABLE `coursefile` (
  `fno` int(10) NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `ftime` datetime DEFAULT NULL,
  `cno` varchar(16) NOT NULL,
  `furl` text NOT NULL,
  PRIMARY KEY (`fno`),
  KEY `cno` (`cno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=432 ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `fbno` int(10) NOT NULL AUTO_INCREMENT,
  `fbcontent` text,
  `uid` char(10) DEFAULT NULL,
  `fbtime` datetime DEFAULT NULL,
  `anonymous` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`fbno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Table structure for table `forgetpasswd`
--

DROP TABLE IF EXISTS `forgetpasswd`;
CREATE TABLE `forgetpasswd` (
  `uno` char(10) NOT NULL,
  `verifycode` varchar(41) DEFAULT NULL,
  `isverified` enum('1','0') DEFAULT '0',
  `att_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `gno` smallint(5) NOT NULL AUTO_INCREMENT,
  `gname` varchar(255) NOT NULL,
  `gnum` int(5) DEFAULT NULL,
  `gintro` text,
  `cno` varchar(16) NOT NULL,
  PRIMARY KEY (`gno`),
  KEY `cno` (`cno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `grpfile`
--

DROP TABLE IF EXISTS `grpfile`;
CREATE TABLE `grpfile` (
  `fno` int(10) NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `ftime` datetime DEFAULT NULL,
  `gno` smallint(5) NOT NULL,
  `gurl` text NOT NULL,
  `is_toclass` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`fno`),
  KEY `gno` (`gno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Table structure for table `grpnews`
--

DROP TABLE IF EXISTS `grpnews`;
CREATE TABLE `grpnews` (
  `gno` smallint(5) NOT NULL,
  `n_no` int(10) NOT NULL AUTO_INCREMENT,
  `n_sno` char(10) DEFAULT NULL,
  `n_content` text,
  `n_time` datetime DEFAULT NULL,
  PRIMARY KEY (`n_no`),
  KEY `gno` (`gno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=202 ;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

DROP TABLE IF EXISTS `homework`;
CREATE TABLE `homework` (
  `hno` int(10) NOT NULL AUTO_INCREMENT,
  `cno` varchar(16) NOT NULL,
  `htitle` varchar(255) DEFAULT NULL,
  `hcontent` text,
  `htime` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `sdno` int(11) DEFAULT NULL,
  PRIMARY KEY (`hno`),
  KEY `cno` (`cno`),
  KEY `sdno` (`sdno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `homeworkfile`
--

DROP TABLE IF EXISTS `homeworkfile`;
CREATE TABLE `homeworkfile` (
  `fno` int(10) NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `ftime` datetime DEFAULT NULL,
  `hno` int(10) NOT NULL,
  `furl` text NOT NULL,
  PRIMARY KEY (`fno`),
  KEY `hno` (`hno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `is_voted`
--

DROP TABLE IF EXISTS `is_voted`;
CREATE TABLE `is_voted` (
  `sno` char(10) NOT NULL DEFAULT '',
  `rpno` int(11) NOT NULL DEFAULT '0',
  `up_down` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`sno`,`rpno`),
  KEY `rpno` (`rpno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Triggers `is_voted`
--
DROP TRIGGER IF EXISTS `Up_downPlus2`;
DELIMITER //
CREATE TRIGGER `Up_downPlus2` AFTER INSERT ON `is_voted`
 FOR EACH ROW begin
update reply set upno=(select count(*) from is_voted where rpno = new.rpno and up_down=1) where rpno = new.rpno ;

end
//
DELIMITER ;
DROP TRIGGER IF EXISTS `Up_down_Plus3`;
DELIMITER //
CREATE TRIGGER `Up_down_Plus3` BEFORE DELETE ON `is_voted`
 FOR EACH ROW begin update reply set upno=(select count(*) from is_voted where rpno = old.rpno and up_down=1) where rpno = old.rpno ; end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_no` varchar(10) NOT NULL,
  `actor_name` varchar(16) DEFAULT NULL,
  `actor_url` text,
  `position_no` int(10) NOT NULL,
  `position_name` varchar(255) DEFAULT NULL,
  `position_url` text,
  `m_time` datetime NOT NULL,
  `m_type` int(3) NOT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `message_user`
--

DROP TABLE IF EXISTS `message_user`;
CREATE TABLE `message_user` (
  `m_id` int(11) NOT NULL DEFAULT '0',
  `m_uid` varchar(10) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`m_id`,`m_uid`,`is_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mn`
--

DROP TABLE IF EXISTS `mn`;
CREATE TABLE `mn` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `cno` varchar(16) DEFAULT NULL,
  `c_url` text,
  `ob_url` text,
  `mntime` datetime DEFAULT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `mn_user`
--

DROP TABLE IF EXISTS `mn_user`;
CREATE TABLE `mn_user` (
  `mid` int(11) DEFAULT NULL,
  `sno` char(10) DEFAULT NULL,
  `is_usersread` tinyint(1) DEFAULT '0',
  KEY `sno` (`sno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ms`
--

DROP TABLE IF EXISTS `ms`;
CREATE TABLE `ms` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `msdate` datetime DEFAULT NULL,
  `msd_url` text,
  `sdno` int(11) DEFAULT NULL,
  `msname` varchar(255) DEFAULT NULL,
  `sch_url` text,
  `mstime` datetime DEFAULT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `ms_user`
--

DROP TABLE IF EXISTS `ms_user`;
CREATE TABLE `ms_user` (
  `mid` int(11) DEFAULT NULL,
  `userid` char(10) DEFAULT NULL,
  `is_usersread` tinyint(1) DEFAULT '0',
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `n_no` int(10) NOT NULL AUTO_INCREMENT,
  `n_sno` varchar(10) NOT NULL,
  `n_sname` varchar(16) DEFAULT NULL,
  `n_surl` text,
  `n_cno` varchar(16) NOT NULL,
  `n_cname` varchar(255) DEFAULT NULL,
  `n_curl` text,
  `n_content` text,
  `n_contenturl` text,
  `n_time` datetime DEFAULT NULL,
  `n_type` int(2) DEFAULT NULL,
  `n_contentid` int(11) DEFAULT NULL,
  PRIMARY KEY (`n_no`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=128 ;

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `nno` int(11) NOT NULL AUTO_INCREMENT,
  `ntime` datetime DEFAULT NULL,
  `ncontent` text,
  `cno` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`nno`),
  KEY `cno` (`cno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `qno` int(11) NOT NULL AUTO_INCREMENT,
  `qtitle` varchar(255) DEFAULT NULL,
  `cno` varchar(16) NOT NULL,
  `attnum` smallint(5) DEFAULT '0',
  `raise_sno` char(10) NOT NULL,
  `raise_time` datetime DEFAULT NULL,
  `rplynum` decimal(5,0) DEFAULT '0',
  `content` text,
  `stu_tea` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`qno`),
  KEY `cno` (`cno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

--
-- Table structure for table `q_attention`
--

DROP TABLE IF EXISTS `q_attention`;
CREATE TABLE `q_attention` (
  `sno` char(10) NOT NULL DEFAULT '',
  `qno` int(11) NOT NULL DEFAULT '0',
  `att_time` datetime DEFAULT NULL,
  PRIMARY KEY (`sno`,`qno`),
  KEY `qno` (`qno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Triggers `q_attention`
--
DROP TRIGGER IF EXISTS `AttnumPlus`;
DELIMITER //
CREATE TRIGGER `AttnumPlus` AFTER INSERT ON `q_attention`
 FOR EACH ROW begin
update question set attnum=(select count(*) from q_attention where qno=new.qno) where qno=new.qno;
end
//
DELIMITER ;
DROP TRIGGER IF EXISTS `AttnumSubstract`;
DELIMITER //
CREATE TRIGGER `AttnumSubstract` AFTER DELETE ON `q_attention`
 FOR EACH ROW begin
update question set attnum=(select count(*) from q_attention where qno=old.qno) where qno=old.qno ;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `regverify`
--

DROP TABLE IF EXISTS `regverify`;
CREATE TABLE `regverify` (
  `sno` char(10) NOT NULL,
  `sname` varchar(16) NOT NULL,
  `sex` enum('男','女') DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `smajor` varchar(255) DEFAULT NULL,
  `sgrade` decimal(4,0) DEFAULT NULL,
  `spsw` varchar(41) NOT NULL,
  `snotes` text,
  `email` varchar(255) DEFAULT NULL,
  `verifycode` varchar(41) DEFAULT NULL,
  `isverified` enum('1','0') DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `remark`
--

DROP TABLE IF EXISTS `remark`;
CREATE TABLE `remark` (
  `rmno` int(11) NOT NULL AUTO_INCREMENT,
  `rpno` int(11) DEFAULT NULL,
  `rmcontent` text,
  `rm_sno` char(10) DEFAULT NULL,
  `rmtime` datetime DEFAULT NULL,
  `stu_tea` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`rmno`),
  KEY `rpno` (`rpno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=126 ;

--
-- Triggers `remark`
--
DROP TRIGGER IF EXISTS `RmknumPlus`;
DELIMITER //
CREATE TRIGGER `RmknumPlus` AFTER INSERT ON `remark`
 FOR EACH ROW begin
update reply set rmknum=(select count(*) from remark where rpno=new.rpno) where rpno=new.rpno;
end
//
DELIMITER ;
DROP TRIGGER IF EXISTS `RmknumSubstract`;
DELIMITER //
CREATE TRIGGER `RmknumSubstract` AFTER DELETE ON `remark`
 FOR EACH ROW begin
update reply set rmknum=(select count(*) from remark where rpno=old.rpno) where rpno=old.rpno;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `qno` int(11) DEFAULT NULL,
  `rpcontent` text,
  `rp_sno` char(10) DEFAULT NULL,
  `rpno` int(11) NOT NULL AUTO_INCREMENT,
  `thsnum` int(11) DEFAULT '0',
  `rmknum` int(11) DEFAULT '0',
  `upno` int(11) DEFAULT '0',
  `downno` int(11) DEFAULT '0',
  `rplytime` datetime DEFAULT NULL,
  `weight` int(11) DEFAULT '0',
  `stu_tea` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`rpno`),
  KEY `qno` (`qno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=147 ;

--
-- Triggers `reply`
--
DROP TRIGGER IF EXISTS `RplynumPlus`;
DELIMITER //
CREATE TRIGGER `RplynumPlus` AFTER INSERT ON `reply`
 FOR EACH ROW begin 
update question set rplynum=(select count(*) from reply where qno=new.qno) where qno=new.qno;
end
//
DELIMITER ;
DROP TRIGGER IF EXISTS `RplynumSubstract`;
DELIMITER //
CREATE TRIGGER `RplynumSubstract` AFTER DELETE ON `reply`
 FOR EACH ROW begin 
update question set rplynum=(select count(*) from reply where qno=old.qno) where qno=old.qno;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sch`
--

DROP TABLE IF EXISTS `sch`;
CREATE TABLE `sch` (
  `sdno` int(11) NOT NULL AUTO_INCREMENT,
  `rname` varchar(255) DEFAULT NULL,
  `rnotes` text,
  `rdeadline` datetime DEFAULT NULL,
  PRIMARY KEY (`sdno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `sno` char(10) NOT NULL,
  `sname` varchar(16) NOT NULL,
  `sex` enum('男','女') DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `smajor` varchar(255) DEFAULT NULL,
  `sgrade` decimal(4,0) DEFAULT NULL,
  `spsw` varchar(41) NOT NULL,
  `snotes` text,
  PRIMARY KEY (`sno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stu_course`
--

DROP TABLE IF EXISTS `stu_course`;
CREATE TABLE `stu_course` (
  `sno` char(10) NOT NULL DEFAULT '',
  `cno` varchar(16) NOT NULL DEFAULT '',
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`sno`,`cno`),
  KEY `cno` (`cno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stu_grp`
--

DROP TABLE IF EXISTS `stu_grp`;
CREATE TABLE `stu_grp` (
  `sno` char(10) NOT NULL,
  `gno` smallint(5) NOT NULL,
  PRIMARY KEY (`sno`,`gno`),
  KEY `gno` (`gno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Triggers `stu_grp`
--
DROP TRIGGER IF EXISTS `GnumPlus`;
DELIMITER //
CREATE TRIGGER `GnumPlus` AFTER INSERT ON `stu_grp`
 FOR EACH ROW begin
update groups set gnum=(select count(*) from stu_grp where gno = new.gno) where gno = new.gno;
end
//
DELIMITER ;
DROP TRIGGER IF EXISTS `GnumSubstract`;
DELIMITER //
CREATE TRIGGER `GnumSubstract` AFTER DELETE ON `stu_grp`
 FOR EACH ROW begin
update groups set gnum=(select count(*) from stu_grp where gno = old.gno) where gno = old.gno;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stu_homework`
--

DROP TABLE IF EXISTS `stu_homework`;
CREATE TABLE `stu_homework` (
  `fno` int(10) NOT NULL AUTO_INCREMENT,
  `sno` char(10) NOT NULL,
  `hno` int(10) NOT NULL DEFAULT '0',
  `fname` varchar(255) DEFAULT NULL,
  `ftime` datetime DEFAULT NULL,
  `furl` text,
  PRIMARY KEY (`fno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1063 ;

-- --------------------------------------------------------

--
-- Table structure for table `stu_sch`
--

DROP TABLE IF EXISTS `stu_sch`;
CREATE TABLE `stu_sch` (
  `sno` char(10) NOT NULL DEFAULT '',
  `sdno` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sno`,`sdno`),
  KEY `sdno` (`sdno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `tno` char(10) NOT NULL,
  `tname` varchar(16) NOT NULL,
  `sex` enum('男','女') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `office` text,
  `tschool` varchar(255) DEFAULT NULL,
  `tpsw` varchar(41) NOT NULL,
  `tnotes` text,
  PRIMARY KEY (`tno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
