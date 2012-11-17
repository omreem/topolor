CREATE TABLE `topolor`.`tpl_user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL,
  `password` VARCHAR(128) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `activkey` VARCHAR(128) NOT NULL DEFAULT '',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisit_at` TIMESTAMP NULL,
  `superuser` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `superuser` (`superuser`)
) ENGINE=InnoDB;

CREATE TABLE `topolor`.`tpl_profile` (
  `user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lastname` VARCHAR(50) NOT NULL DEFAULT '',
  `firstname` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_profile_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `topolor`.`tpl_profile_field` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `varname` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `field_type` VARCHAR(50) NOT NULL,
  `field_size` VARCHAR(15) NOT NULL DEFAULT '0',
  `field_size_min` VARCHAR(15) NOT NULL DEFAULT '0',
  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `match` VARCHAR(255) NOT NULL DEFAULT '',
  `range` VARCHAR(255) NOT NULL DEFAULT '',
  `error_message` VARCHAR(255) NOT NULL DEFAULT '',
  `other_validator` VARCHAR(5000) NOT NULL DEFAULT '',
  `default` VARCHAR(255) NOT NULL DEFAULT '',
  `widget` VARCHAR(255) NOT NULL DEFAULT '',
  `widgetparams` VARCHAR(5000) NOT NULL DEFAULT '',
  `position` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`,`widget`,`visible`)
) ENGINE=InnoDB;

INSERT INTO `tpl_user` (`id`, `username`, `password`, `email`, `activkey`, `superuser`, `status`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'webmaster@example.com', '9a24eff8c15a6a141ece27eb6947da0f', 1, 1),
(2, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 'demo@example.com', '099f825543f7850cc038b90aaff39fac', 0, 1);

INSERT INTO `tpl_profile` (`user_id`, `lastname`, `firstname`) VALUES
(1, 'Admin', 'Administrator'),
(2, 'Demo', 'Demo');

INSERT INTO `tpl_profile_field` (`id`, `varname`, `title`, `field_type`, `field_size`, `field_size_min`, `required`, `match`, `range`, `error_message`, `other_validator`, `default`, `widget`, `widgetparams`, `position`, `visible`) VALUES
(1, 'lastname', 'Last Name', 'VARCHAR', 50, 3, 1, '', '', 'Incorrect Last Name (length between 3 and 50 characters).', '', '', '', '', 1, 3),
(2, 'firstname', 'First Name', 'VARCHAR', 50, 3, 1, '', '', 'Incorrect First Name (length between 3 and 50 characters).', '', '', '', '', 0, 3);

-- -----------------------------------------------------
-- Table `topolor`.`tpl_concept`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_concept` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(256) NOT NULL,
  `description` TEXT NOT NULL,
  `tags` TEXT NULL,
  `root` INT(10) UNSIGNED DEFAULT NULL,
  `lft` INT(10) UNSIGNED NOT NULL,
  `rgt` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`) ,
  KEY `concept_author_id` (`author_id`),
  KEY `root` (`root`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`),
  CONSTRAINT `concept_author_id_fk`
    FOREIGN KEY (`author_id` )
    REFERENCES `topolor`.`tpl_user` (`id` )
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_resource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_resource` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(256) NOT NULL,
  `description` TEXT NULL,
  `type` TINYINT(1) UNSIGNED NOT NULL,
  `url` VARCHAR(512) NOT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `resource_author_id` (`author_id`),
  CONSTRAINT `resource_author_id_fk`
    FOREIGN KEY (`author_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_concept_resource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_concept_resource` (
  `concept_id` INT(10) UNSIGNED NOT NULL,
  `resource_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`concept_id`, `resource_id`),
  KEY `cr_concept_id` (`concept_id`),
  KEY `cr_resource_id` (`resource_id`),
  CONSTRAINT `cr_concept_id_fk`
	FOREIGN KEY (`concept_id`)
	REFERENCES `tpl_concept` (`id`),
  CONSTRAINT `cr_resource_id`
    FOREIGN KEY (`resource_id`)
	REFERENCES `tpl_resource` (`id`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_concept_comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_concept_comment` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `concept_id` INT(10) UNSIGNED NOT NULL ,
  `learner_id` INT(10) UNSIGNED NOT NULL ,
  `description` TEXT NOT NULL ,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cc_learner_id` (`learner_id`),
  KEY `cc_concept_id` (`concept_id`),
  CONSTRAINT `cc_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `cc_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_learner_concept`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_learner_concept` (
  `learner_id` INT(10) UNSIGNED NOT NULL,
  `concept_id` INT(10) UNSIGNED NOT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastaction_at` TIMESTAMP NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1: in-progress; 2: completed',
  PRIMARY KEY (`learner_id`, `concept_id`),
  KEY `lc_learner_id` (`learner_id`),
  KEY `lc_concept_id` (`concept_id`),
  CONSTRAINT `lc_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`),
  CONSTRAINT `lc_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_quiz`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_quiz` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `learner_id` INT(10) UNSIGNED NOT NULL,
  `concept_id` INT(10) UNSIGNED NOT NULL,
  `score` VARCHAR(5) NULL COMMENT ' x/y ',
  `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0: quiz; 1: pre-test; 2: mid-test; 3: final-test',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `done_at` TIMESTAMP NULL,
  `lastaccess_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`) ,
  KEY `quiz_learner_id` (`learner_id`),
  KEY `quiz_concept_id` (`concept_id`),
  CONSTRAINT `quiz_learner_id_fk`
    FOREIGN KEY (`learner_id` )
    REFERENCES `topolor`.`tpl_user` (`id` ),
  CONSTRAINT `quiz_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_question` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT(10) UNSIGNED NOT NULL,
  `concept_id` INT(10) UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `correct_answer` VARCHAR(1) NOT NULL COMMENT 'A; B; C; D.',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `question_author_id` (`author_id`),
  KEY `question_concept_id` (`concept_id`),
  CONSTRAINT `question_author_id_fk`
    FOREIGN KEY (`author_id`)
    REFERENCES `topolor`.`tpl_user` (`id`),
  CONSTRAINT `question_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_question_option`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_question_option` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` INT(10) UNSIGNED NOT NULL,
  `opt` VARCHAR(1) NOT NULL COMMENT 'A; B; C; D.',
  `val` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `option_question_id` (`question_id`),
  CONSTRAINT `option_question_id_fk`
    FOREIGN KEY (`question_id`)
    REFERENCES `topolor`.`tpl_question` (`id`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_quiz_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_quiz_question` (
  `quiz_id` INT(10) UNSIGNED NOT NULL,
  `question_id` INT(10) UNSIGNED NOT NULL,
  `position` TINYINT(1) UNSIGNED NOT NULL COMMENT 'the order of a question in the quiz' ,
  `answer` VARCHAR(1) NULL COMMENT 'A; B; C; D.',
  PRIMARY KEY (`quiz_id`, `question_id`),
  KEY `qq_quiz_id` (`quiz_id`),
  KEY `qq_question_id` (`question_id`),
  CONSTRAINT `qq_quiz_id_fk`
    FOREIGN KEY (`quiz_id`)
    REFERENCES `topolor`.`tpl_quiz` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `qq_question_id_fk`
    FOREIGN KEY (`question_id`)
    REFERENCES `topolor`.`tpl_question` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_ask`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_ask` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `learner_id` INT(10) UNSIGNED NOT NULL,
  `title` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `concept_id` int(10) UNSIGNED DEFAULT NULL,
  `tags` TEXT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `ask_learner_id` (`learner_id`),
  KEY `ask_concept_id` (`concept_id`),
  CONSTRAINT `ask_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `ask_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_answer` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `learner_id` INT(10) UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `ask_id` INT(10) UNSIGNED NOT NULL ,
  `is_best` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `useful` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `useless` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`) ,
  KEY `answer_learner_id` (`learner_id`),
  KEY `answer_ask_id` (`ask_id`),
  CONSTRAINT `answer_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `answer_ask_id_fk`
    FOREIGN KEY (`ask_id`)
    REFERENCES `topolor`.`tpl_ask` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_note`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_note` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `learner_id` INT(10) UNSIGNED NOT NULL ,
  `title` TEXT NOT NULL ,
  `description` TEXT NOT NULL ,
  `concept_id` INT(10) UNSIGNED DEFAULT NULL,
  `tags` TEXT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `note_learner_id` (`learner_id`),
  KEY `note_concept_id` (`concept_id`),
  CONSTRAINT `note_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `note_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id` )
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_todo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `topolor`.`tpl_todo` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `learner_id` INT(10) UNSIGNED NOT NULL,
  `title` TEXT NOT NULL,
  `description` TEXT NULL,
  `concept_id` INT(10) UNSIGNED DEFAULT NULL,
  `tags` TEXT NULL,
  `category` INT(1) NOT NULL DEFAULT 1 COMMENT '1: schedule; 2: delegate',
  `status` INT(1) NOT NULL DEFAULT 0 COMMENT '0: undone; 1: done; 2: cancel',
  `start_at` TIMESTAMP NULL,
  `end_at` TIMESTAMP NULL,
  `done_at` TIMESTAMP NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `todo_learner_id` (`learner_id`),
  KEY `todo_concept_id` (`concept_id`),
  CONSTRAINT `todo_learner_id_fk`
    FOREIGN KEY (`learner_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `todo_concept_id_fk`
    FOREIGN KEY (`concept_id`)
    REFERENCES `topolor`.`tpl_concept` (`id` )
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_metadata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_tag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `of` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `frequency` INT(10) NOT NULL DEFAULT 1,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `metadata_user_id` (`user_id`),
  CONSTRAINT `metadata_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id` )
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_feed`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `topolor`.`tpl_feed` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `of` VARCHAR(255) NULL,
  `of_id` INT(10) UNSIGNED NULL,
  `from_id` INT(10) UNSIGNED NULL,
  `description` TEXT NOT NULL,
  `favorite` INT(10) NOT NULL DEFAULT 0,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `feed_user_id` (`user_id`),
  KEY `feed_from_id` (`from_id`),
  CONSTRAINT `feed_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id` )
    ON DELETE CASCADE,
  CONSTRAINT `feed_from_id_fk`
    FOREIGN KEY (`from_id`)
    REFERENCES `topolor`.`tpl_feed` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_feed_comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_feed_comment` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `feed_id` INT(10) UNSIGNED NOT NULL ,
  `description` TEXT NOT NULL ,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fc_user_id` (`user_id`),
  KEY `fc_feed_id` (`feed_id`),
  CONSTRAINT `fc_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fc_feed_id_fk`
    FOREIGN KEY (`feed_id`)
    REFERENCES `topolor`.`tpl_feed` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_favorite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_favorite` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `of` VARCHAR(255) NOT NULL,
  `of_id` INT(10) UNSIGNED NOT NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `favorite_user_id` (`user_id`),
  CONSTRAINT `favorite_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_message`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_message` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT 'message from user',
  `to_user_id` INT(10) UNSIGNED NOT NULL COMMENT 'message to user',
  `to_message_id` INT(10) UNSIGNED NULL COMMENT 'first message started',
  `description` TEXT NOT NULL,
  `status` INT(1) NOT NULL DEFAULT 0 COMMENT '0: unread; 1: read; 2: deleted',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `message_from_user_id` (`user_id`),
  KEY `message_to_user_id` (`to_user_id`),
  CONSTRAINT `message_from_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `message_to_user_id_fk`
    FOREIGN KEY (`to_user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `message_to_message_id_fk`
    FOREIGN KEY (`to_message_id`)
    REFERENCES `topolor`.`tpl_message` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `topolor`.`tpl_monitor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `topolor`.`tpl_monitor` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `controllor` VARCHAR(40) NOT NULL,
  `action` VARCHAR(40) NOT NULL,
  `type` VARCHAR(4) NULL,
  `request_key` VARCHAR(255) NULL,
  `request_value` VARCHAR(255) NULL,
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `monitor_user_id` (`user_id`),
  CONSTRAINT `monitor_user_id_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `topolor`.`tpl_user` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;