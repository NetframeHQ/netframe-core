-- ----------------------------------------------------------------------------
-- MySQL Workbench Migration
-- Migrated Schemata: netframe5
-- Source Schemata: netframe5
-- Created: Mon Nov  9 10:26:52 2020
-- Workbench Version: 6.3.8
-- ----------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- Schema netframe5
-- ----------------------------------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `netframe5` DEFAULT CHARACTER SET utf8 ;

-- ----------------------------------------------------------------------------
-- Table netframe5.app_settings
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`app_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(245) NOT NULL,
  `option` TEXT NULL,
  `autoload` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gdpr_agrement` TINYINT NOT NULL DEFAULT 0,
  `modal_gdpr` TINYINT NOT NULL DEFAULT 1,
  `active` INT(1) NOT NULL DEFAULT 1,
  `visitor` TINYINT NOT NULL DEFAULT 0,
  `ip` VARCHAR(45) NULL,
  `lang` VARCHAR(6) NOT NULL,
  `slug` VARCHAR(80) NOT NULL,
  `gender` ENUM('man','woman') NULL,
  `firstname` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(250) NOT NULL,
  `pays` VARCHAR(45) NULL,
  `nationality` VARCHAR(45) NULL,
  `codepostal` VARCHAR(45) NULL,
  `city` VARCHAR(45) NULL,
  `function` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `desk_informations` VARCHAR(255) NULL,
  `latitude` DECIMAL(18,12) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `location` VARCHAR(255) NULL,
  `spoken_languages` VARCHAR(150) NULL,
  `description` TEXT NULL,
  `training` TEXT NULL,
  `password` VARCHAR(64) NOT NULL,
  `password_token` VARCHAR(255) NULL DEFAULT NULL,
  `password_timeout` DATETIME NULL DEFAULT NULL,
  `profile_media_id` BIGINT(20) NULL,
  `date_birth` DATE NULL,
  `online` TINYINT(1) NULL DEFAULT 0,
  `like` INT NOT NULL DEFAULT 0,
  `confidentiality` TINYINT NOT NULL DEFAULT 1,
  `last_connexion` DATETIME NULL,
  `activated` TINYINT(1) NULL DEFAULT 0,
  `check_rights` TINYINT NOT NULL DEFAULT 0,
  `email_tuteur` VARCHAR(255) NULL,
  `activation_tuteur` INT NULL DEFAULT 0,
  `statut` VARCHAR(45) NULL COMMENT 'Humeur du statut',
  `remember_token` VARCHAR(255) NULL,
  `last_action_date` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT INDEX `name` (`name` ASC),
  FULLTEXT INDEX `firstname` (`firstname` ASC),
  FULLTEXT INDEX `description` (`description` ASC))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.projects
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`projects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` INT(1) NOT NULL DEFAULT 1,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `auto_subscribe` TINYINT NOT NULL DEFAULT 0,
  `auto_member` INT NOT NULL DEFAULT 0,
  `owner_id` BIGINT NOT NULL,
  `owner_type` VARCHAR(55) NOT NULL,
  `title` VARCHAR(255) NULL,
  `free_join` TINYINT NOT NULL DEFAULT 1,
  `slug` VARCHAR(100) NULL,
  `profile_media_id` BIGINT NULL,
  `description` TEXT NULL,
  `location` VARCHAR(150) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `latitude` DECIMAL(18,12) NULL,
  `hits` INT NULL DEFAULT 0,
  `confidentiality` INT NOT NULL DEFAULT 0,
  `with_personnal_folder` TINYINT NOT NULL DEFAULT 0,
  `share` BIGINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_projects_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `title` (`title` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  INDEX `fk_projects_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_projects_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.ref_langs
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`ref_langs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso_639_1` VARCHAR(10) NULL,
  `iso_639_2` VARCHAR(10) NULL,
  `lang` VARCHAR(10) NULL,
  `name` VARCHAR(45) NOT NULL,
  `active` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.meta_profiles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`meta_profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(245) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_meta_profiles_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_meta_profiles_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.events
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT CHARACTER SET 'utf8mb4' NULL,
  `phone` VARCHAR(45) NULL,
  `name_contact` VARCHAR(45) NULL,
  `mail_contact` VARCHAR(45) NULL,
  `latitude` DECIMAL(18,12) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `location` VARCHAR(255) NULL,
  `date` DATE NOT NULL,
  `time` TIME NULL,
  `date_end` DATE NULL,
  `time_end` TIME NULL,
  `start_date` DATETIME NULL,
  `end_date` DATETIME NULL,
  `confidentiality` INT NOT NULL,
  `participants` BIGINT NOT NULL DEFAULT 0,
  `author_id` BIGINT NULL,
  `author_type` VARCHAR(50) NULL,
  `disable_comments` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_events_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  FULLTEXT INDEX `title` (`title` ASC),
  INDEX `fk_events_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_events_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.user_parameters
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`user_parameters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `parameter_name` VARCHAR(45) NULL,
  `parameter_value` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_params_profiles_users1_idx` (`users_id` ASC),
  INDEX `fk_params_users_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_params_profiles_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_params_users_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.galeries
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`galeries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(45) NULL,
  `slug` VARCHAR(45) NULL,
  `description` VARCHAR(245) NULL,
  `share` VARCHAR(45) NULL,
  `type` VARCHAR(45) NOT NULL COMMENT 'type de book ou de playlist ex gallerie, book projet...',
  `book` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_galeries_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_galeries_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.ltm_translations
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`ltm_translations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` INT(11) NOT NULL DEFAULT '0',
  `locale` VARCHAR(255) CHARACTER SET 'utf8mb4' NOT NULL,
  `group` VARCHAR(255) CHARACTER SET 'utf8mb4' NOT NULL,
  `key` VARCHAR(255) CHARACTER SET 'utf8mb4' NOT NULL,
  `value` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3805
DEFAULT CHARACTER SET = utf8mb4;

-- ----------------------------------------------------------------------------
-- Table netframe5.medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`medias` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `read_only` TINYINT NOT NULL DEFAULT 0,
  `linked` TINYINT NOT NULL DEFAULT 1,
  `under_workflow` TINYINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `share` BIGINT NOT NULL DEFAULT 0,
  `language` VARCHAR(10) NULL,
  `name` VARCHAR(245) NOT NULL,
  `access_rights` VARCHAR(9) NOT NULL DEFAULT 'rw-rw-r--',
  `description` TEXT NULL,
  `latitude` DECIMAL(12,8) NULL,
  `longitude` DECIMAL(12,8) NULL,
  `meta_title` VARCHAR(245) NULL,
  `meta_author` VARCHAR(255) NULL,
  `meta_alt` VARCHAR(255) NULL,
  `type` INT NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NULL,
  `feed_path` VARCHAR(255) NULL,
  `thumb_path` VARCHAR(255) NULL,
  `feed_width` INT NOT NULL DEFAULT 0,
  `feed_height` INT NOT NULL DEFAULT 0,
  `date` DATE NOT NULL,
  `confidentiality` INT NULL,
  `platform` VARCHAR(50) NULL,
  `mime_type` VARCHAR(45) NULL,
  `file_size` BIGINT NOT NULL DEFAULT 0,
  `encoded` INT(1) NULL DEFAULT 0,
  `startEncode` DATETIME NULL,
  `endEncode` DATETIME NULL,
  `keep_files` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_medias_users1_idx` (`users_id` ASC),
  INDEX `fk_medias_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_medias_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.news_feeds
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`news_feeds` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `confidentiality` TINYINT NOT NULL DEFAULT 1,
  `author_id` BIGINT NOT NULL,
  `author_type` VARCHAR(50) NOT NULL,
  `true_author_id` BIGINT NOT NULL,
  `true_author_type` VARCHAR(50) NOT NULL,
  `post_id` BIGINT NOT NULL,
  `post_type` VARCHAR(21) NOT NULL COMMENT 'news, media, galerie, evenement, galerie, book',
  `invisible_owner` INT NOT NULL DEFAULT 0,
  `private_profile` INT NOT NULL DEFAULT 0,
  `share` INT NOT NULL DEFAULT 0 COMMENT 'from content provided by a share link',
  `like` INT UNSIGNED NOT NULL DEFAULT 0,
  `pintop` INT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `users_id` (`users_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `fk_news_feeds_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_news_feeds_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.friends
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`friends` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `friends_id` BIGINT UNSIGNED NOT NULL,
  `blacklist` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Colonne permettant de savoir qui est blacklisté',
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Status définit si le contact a accepté ou non l\'invitation',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_users_id_idx` (`users_id` ASC),
  INDEX `fk_friends_id_idx` (`friends_id` ASC),
  INDEX `fk_friends_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_friends_users_id`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_friends_friends_id`
    FOREIGN KEY (`friends_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_friends_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.projects_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`projects_has_medias` (
  `projects_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `favorite` INT(1) NOT NULL DEFAULT 0,
  `profile_image` INT(1) NOT NULL DEFAULT 0,
  INDEX `fk_projects_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_projects_has_medias_projects1_idx` (`projects_id` ASC),
  INDEX `fk_projects_has_medias_medias_folders1_idx` (`medias_folders_id` ASC),
  CONSTRAINT `fk_projects_has_medias_projects1`
    FOREIGN KEY (`projects_id`)
    REFERENCES `netframe5`.`projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_has_medias_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.accountents
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`accountents` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) NULL DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(64) NOT NULL DEFAULT '',
  `remember_token` VARCHAR(100) NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `password_token` VARCHAR(60) NULL DEFAULT NULL,
  `password_timeout` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 35
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.accountents_has_instances
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`accountents_has_instances` (
  `accountents_id` BIGINT(20) UNSIGNED NOT NULL,
  `instances_id` BIGINT(19) UNSIGNED NOT NULL,
  PRIMARY KEY (`accountents_id`, `instances_id`),
  INDEX `fk_accountents_has_instances_instances1_idx` (`instances_id` ASC),
  INDEX `fk_accountents_has_instances_accountents1_idx` (`accountents_id` ASC),
  CONSTRAINT `fk_accountents_has_instances_accountents1`
    FOREIGN KEY (`accountents_id`)
    REFERENCES `netframe5`.`accountents` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accountents_has_instances_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.admins
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`admins` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) NULL DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(64) NOT NULL DEFAULT '',
  `remember_token` VARCHAR(100) NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.galerie_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`galerie_has_medias` (
  `galeries_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  INDEX `fk_books_playlists_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_books_playlists_has_medias_books_playlists1_idx` (`galeries_id` ASC),
  CONSTRAINT `fk_books_playlists_has_medias_books_playlists1`
    FOREIGN KEY (`galeries_id`)
    REFERENCES `netframe5`.`galeries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_books_playlists_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.houses
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`houses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `auto_subscribe` TINYINT NOT NULL DEFAULT 0,
  `auto_member` INT NOT NULL DEFAULT 0,
  `owner_id` BIGINT NOT NULL,
  `owner_type` VARCHAR(55) NOT NULL,
  `active` INT(1) NOT NULL DEFAULT 1,
  `profile_media_id` BIGINT NULL,
  `name` VARCHAR(255) NOT NULL,
  `free_join` TINYINT NOT NULL DEFAULT 1,
  `slug` VARCHAR(80) NOT NULL,
  `description` TEXT NULL,
  `latitude` DECIMAL(18,12) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `location` VARCHAR(255) NULL,
  `share` BIGINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `confidentiality` TINYINT NOT NULL DEFAULT 1,
  `with_personnal_folder` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_houses_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `name` (`name` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  INDEX `fk_houses_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_houses_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_has_medias` (
  `users_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `favorite` INT(1) NOT NULL DEFAULT 0,
  `profile_image` INT(1) NOT NULL DEFAULT 0,
  INDEX `fk_users_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_users_has_medias_users1_idx` (`users_id` ASC),
  INDEX `fk_users_has_medias_medias_folders1_idx` (`medias_folders_id` ASC),
  CONSTRAINT `fk_users_has_medias_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_medias_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.likes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`likes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `liker_id` BIGINT UNSIGNED NOT NULL,
  `liker_type` VARCHAR(50) NULL DEFAULT NULL,
  `liked_id` BIGINT NOT NULL COMMENT 'l\'id du type d\'utilisateur qui a liké en tant que ... autre qu\'utilisateur.',
  `liked_type` VARCHAR(50) NULL DEFAULT NULL,
  `emojis_id` INT UNSIGNED NULL DEFAULT 414,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_likes_users1_idx` (`users_id` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `liked_id` (`liker_id` ASC),
  INDEX `liked_type` (`liker_type` ASC),
  INDEX `fk_likes_instances1_idx` (`instances_id` ASC),
  INDEX `fk_likes_emojis1_idx` (`emojis_id` ASC),
  CONSTRAINT `fk_likes_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_likes_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_likes_emojis1`
    FOREIGN KEY (`emojis_id`)
    REFERENCES `netframe5`.`emojis` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.colab_docs
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`colab_docs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL,
  `content` LONGTEXT  CHARACTER SET 'utf8mb4'NULL DEFAULT NULL,
  `users_id` BIGINT(19) UNSIGNED NOT NULL,
  `instances_id` BIGINT(19) UNSIGNED NOT NULL,
  `users` TEXT NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_colab_docs_users1` (`users_id` ASC),
  INDEX `fk_colab_docs_instances1` (`instances_id` ASC),
  CONSTRAINT `fk_colab_docs_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_colab_docs_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.comments
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT CHARACTER SET 'utf8mb4' NOT NULL,
  `author_id` BIGINT NOT NULL COMMENT 'se reporter à la description du champ équivalent de la table likes',
  `author_type` VARCHAR(45) NOT NULL COMMENT 'talent, angel, user, communaute, house',
  `users_id` BIGINT UNSIGNED NOT NULL,
  `post_id` BIGINT NOT NULL,
  `post_type` VARCHAR(45) NOT NULL COMMENT 'event, news',
  `comments_id` BIGINT UNSIGNED NULL,
  `level` INT NOT NULL DEFAULT 1,
  `like` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_shares_users1_idx` (`users_id` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `fk_comments_instances1_idx` (`instances_id` ASC),
  INDEX `fk_comments_comments1_idx` (`comments_id` ASC),
  CONSTRAINT `fk_comments_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comments_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comments_comments1`
    FOREIGN KEY (`comments_id`)
    REFERENCES `netframe5`.`comments` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.events_has_friends
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`events_has_friends` (
  `users_id` BIGINT UNSIGNED NOT NULL,
  `events_id` BIGINT UNSIGNED NOT NULL,
  `status` TINYINT(3) NOT NULL DEFAULT 0 COMMENT 'participe, participe pas, participe peut etre',
  INDEX `fk_events_has_friends_users1_idx` (`users_id` ASC),
  INDEX `fk_events_has_friends_events1_idx` (`events_id` ASC),
  CONSTRAINT `fk_events_has_friends_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_has_friends_events1`
    FOREIGN KEY (`events_id`)
    REFERENCES `netframe5`.`events` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.events_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`events_has_medias` (
  `events_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `favorite` INT NOT NULL DEFAULT 0,
  INDEX `fk_events_has_medias_events1_idx` (`events_id` ASC),
  INDEX `fk_events_has_medias_medias1_idx` (`medias_id` ASC),
  CONSTRAINT `fk_events_has_medias_events1`
    FOREIGN KEY (`events_id`)
    REFERENCES `netframe5`.`events` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.notifications
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `author_id` BIGINT NOT NULL,
  `author_type` VARCHAR(45) NOT NULL COMMENT 'user, talent, angel, projet, house, community',
  `type` VARCHAR(45) NOT NULL COMMENT 'Type de notifiaction ex: projet, demande en ami, message reçu, nouvel event...',
  `user_from` BIGINT NOT NULL,
  `parameter` TEXT NOT NULL COMMENT 'Stockage de donnée au format json, construction d\'un arbre afin de retrouver plus facilement les éléments de notifications concerné quel user, quel projet',
  `read` TINYINT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_notifications_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_notifications_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.groups
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.friends_has_groups
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`friends_has_groups` (
  `friends_id` BIGINT UNSIGNED NOT NULL,
  `groups_id` BIGINT UNSIGNED NOT NULL,
  INDEX `fk_friends_has_groups_groups1_idx` (`groups_id` ASC),
  INDEX `fk_friends_has_groups_friends1_idx` (`friends_id` ASC),
  CONSTRAINT `fk_friends_has_groups_friends1`
    FOREIGN KEY (`friends_id`)
    REFERENCES `netframe5`.`friends` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_friends_has_groups_groups1`
    FOREIGN KEY (`groups_id`)
    REFERENCES `netframe5`.`groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.news
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`news` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `author_id` BIGINT NOT NULL COMMENT 'id profile type',
  `author_type` VARCHAR(50) NOT NULL COMMENT 'Type de profile:- projet- angel- talent- maison- user--> relation app_setting',
  `disable_comments` TINYINT NOT NULL DEFAULT 0,
  `language` VARCHAR(5) NOT NULL,
  `content` TEXT CHARACTER SET 'utf8mb4' NULL COMMENT 'nullable si media rattache',
  `statut` VARCHAR(45) NULL COMMENT 'Humeur du statut',
  `confidentiality` TINYINT(2) NOT NULL DEFAULT 0 COMMENT 'champ de getion de la confidentialité\n0 = public\n1 = privé',
  `media_id` INT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_news_users1_idx` (`users_id` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `fk_news_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_news_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_news_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.houses_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`houses_has_users` (
  `houses_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `fk_houses_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_houses_has_users_houses1_idx` (`houses_id` ASC),
  INDEX `fk_houses_has_users_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_houses_has_users_houses1`
    FOREIGN KEY (`houses_id`)
    REFERENCES `netframe5`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.positions_history
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`positions_history` (
  `id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `location` VARCHAR(150) NULL,
  `latitude` DECIMAL(12,8) NULL,
  `longitude` DECIMAL(12,8) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_positions_history_users1_idx` (`users_id` ASC),
  INDEX `fk_positions_history_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_positions_history_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_positions_history_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.houses_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`houses_has_medias` (
  `houses_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `favorite` INT(1) NOT NULL DEFAULT 0,
  `profile_image` INT(1) NOT NULL DEFAULT 0,
  INDEX `fk_houses_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_houses_has_medias_houses1_idx` (`houses_id` ASC),
  INDEX `fk_houses_has_medias_medias_folders1_idx` (`medias_folders_id` ASC),
  CONSTRAINT `fk_houses_has_medias_houses1`
    FOREIGN KEY (`houses_id`)
    REFERENCES `netframe5`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_medias_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.playlists
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`playlists` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `author_id` BIGINT NOT NULL,
  `author_type` VARCHAR(45) NOT NULL COMMENT 'user, talent, angel, hosute, community, projet',
  `disable_comments` TINYINT NOT NULL DEFAULT 0,
  `content` TEXT NULL,
  `slug` VARCHAR(80) NULL,
  `name` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `instant_playlist` INT NULL DEFAULT 0,
  `confidentiality` INT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_playlists_users1_idx` (`users_id` ASC),
  INDEX `fk_playlists_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_playlists_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_playlists_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.playlists_items
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`playlists_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `playlists_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NULL,
  `profile_id` BIGINT NOT NULL,
  `profile_type` VARCHAR(45) NOT NULL COMMENT 'talent, angel, maison, projet',
  `read_owner` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_playlist_items_playlists1_idx` (`playlists_id` ASC),
  INDEX `fk_playlists_items_users1_idx` (`users_id` ASC),
  INDEX `profile_id` (`profile_id` ASC),
  INDEX `profile_type` (`profile_type` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `fk_playlists_items_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_playlist_items_playlists1`
    FOREIGN KEY (`playlists_id`)
    REFERENCES `netframe5`.`playlists` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_playlists_items_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_playlists_items_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.community
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`community` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `auto_subscribe` TINYINT NOT NULL DEFAULT 0,
  `auto_member` INT NOT NULL DEFAULT 0,
  `owner_id` BIGINT NOT NULL,
  `owner_type` VARCHAR(55) NOT NULL,
  `active` INT(1) NOT NULL DEFAULT 1,
  `profile_media` VARCHAR(255) NULL,
  `name` VARCHAR(255) NOT NULL,
  `location` VARCHAR(150) NULL,
  `slug` VARCHAR(80) NOT NULL,
  `free_join` TINYINT NOT NULL DEFAULT 1,
  `profile_media_id` BIGINT NULL,
  `description` TEXT NULL,
  `latitude` DECIMAL(18,12) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `share` BIGINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `confidentiality` TINYINT NOT NULL DEFAULT 1,
  `with_personnal_folder` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_community_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `name` (`name` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  INDEX `fk_community_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_community_users10`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.community_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`community_has_users` (
  `community_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `fk_community_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_community_has_users_community1_idx` (`community_id` ASC),
  INDEX `fk_community_has_users_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_community_has_users_community1`
    FOREIGN KEY (`community_id`)
    REFERENCES `netframe5`.`community` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_has_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.community_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`community_has_medias` (
  `community_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `favorite` INT(1) NOT NULL DEFAULT 0,
  `profile_image` INT(1) NOT NULL DEFAULT 0,
  INDEX `fk_community_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_community_has_medias_community1_idx` (`community_id` ASC),
  INDEX `fk_community_has_medias_medias_folders1_idx` (`medias_folders_id` ASC),
  CONSTRAINT `fk_community_has_medias_community1`
    FOREIGN KEY (`community_id`)
    REFERENCES `netframe5`.`community` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_community_has_medias_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.roles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL COMMENT 'owner, administrator, contributor, moderator, participant (pour identification project has profil via la table profils_has_project)',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.subscriptions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`subscriptions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `profile_id` BIGINT UNSIGNED NOT NULL,
  `profile_type` VARCHAR(50) NULL DEFAULT NULL,
  `level` TINYINT NOT NULL DEFAULT 1,
  `confidentiality` INT NOT NULL DEFAULT 1 COMMENT '0 : private, 1 : public',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_likes_users1_idx` (`users_id` ASC),
  INDEX `profile_type` (`profile_type` ASC),
  INDEX `profile_id` (`profile_id` ASC),
  INDEX `fk_subscriptions_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_subscriptions_users10`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriptions_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.suggestions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`suggestions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_foreign` BIGINT NOT NULL,
  `type_foreign` VARCHAR(45) NOT NULL COMMENT 'user, talent, angel, projet, house, community',
  `type` VARCHAR(45) NOT NULL COMMENT 'Type de suggestion ex: projet, demande en ami, message reçu, nouvel event...',
  `parameter` TEXT NOT NULL COMMENT 'Stockage de donnée au format json, construction d\'un arbre afin de retrouver plus facilement les éléments de suggestions concerné quel user, quel projet',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.offers
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`offers` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `author_id` BIGINT NOT NULL,
  `author_type` VARCHAR(50) NULL DEFAULT NULL,
  `disable_comments` TINYINT NOT NULL DEFAULT 0,
  `name` VARCHAR(150) NOT NULL,
  `content` TEXT CHARACTER SET 'utf8mb4' NULL,
  `offer_type` VARCHAR(50) NOT NULL,
  `location` VARCHAR(150) NULL,
  `latitude` DECIMAL(18,12) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `start_at` DATE NULL,
  `stop_at` DATE NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_offers_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `content` (`content` ASC),
  FULLTEXT INDEX `name` (`name` ASC),
  INDEX `fk_offers_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_offers_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_offers_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.offers_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`offers_has_medias` (
  `offers_id` BIGINT NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  INDEX `fk_offers_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_offers_has_medias_offers1_idx` (`offers_id` ASC),
  CONSTRAINT `fk_offers_has_medias_offers1`
    FOREIGN KEY (`offers_id`)
    REFERENCES `netframe5`.`offers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_offers_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.trackings
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`trackings` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `users_id` VARCHAR(45) NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `users_type` VARCHAR(45) NOT NULL COMMENT 'user, talent, angel, partner, community, house',
  `language` VARCHAR(5) NOT NULL,
  `true_language` VARCHAR(10) NULL,
  `ip` VARCHAR(20) NULL,
  `location` VARCHAR(150) NULL COMMENT 'pays - region - ville',
  `url` VARCHAR(255) NULL,
  `method` VARCHAR(15) NOT NULL,
  `referer` VARCHAR(255) NULL,
  `user_agent` VARCHAR(255) NULL,
  `computed` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_trackings_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_trackings_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.sessions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`sessions` (
  `id` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `last_activity` INT(11) NOT NULL,
  `user_id` INT(11) NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  UNIQUE INDEX `sessions_id_unique` (`id` ASC))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.profils_has_projects
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`profils_has_projects` (
  `project_id` BIGINT UNSIGNED NOT NULL,
  `profils_has_project_id` BIGINT NOT NULL,
  `profils_has_project_type` VARCHAR(50) NULL DEFAULT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `roles_id` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `fk_profils_has_projects_users1_idx` (`users_id` ASC),
  INDEX `fk_profils_has_projects_roles1_idx` (`roles_id` ASC),
  INDEX `fk_profils_has_projects_projects1_idx` (`project_id` ASC),
  CONSTRAINT `fk_profils_has_projects_projects1`
    FOREIGN KEY (`project_id`)
    REFERENCES `netframe5`.`projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profils_has_projects_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profils_has_projects_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.news_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`news_has_medias` (
  `news_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`news_id`, `medias_id`),
  INDEX `fk_news_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_news_has_medias_news1_idx` (`news_id` ASC),
  CONSTRAINT `fk_news_has_medias_news1`
    FOREIGN KEY (`news_id`)
    REFERENCES `netframe5`.`news` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_news_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.ref_countries
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`ref_countries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `iso` VARCHAR(3) NULL,
  `lang` VARCHAR(10) NULL,
  `name` VARCHAR(100) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.chat_settings
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`chat_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `settings` TEXT NULL,
  PRIMARY KEY (`id`, `users_id`, `instances_id`),
  INDEX `fk_chat_settings_users1_idx` (`users_id` ASC),
  INDEX `fk_chat_settings_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_chat_settings_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_settings_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.messages_mail
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`messages_mail` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `messages_mail_group_id` BIGINT UNSIGNED NOT NULL,
  `parent_message_id` BIGINT NULL DEFAULT 0,
  `sender_id` BIGINT NULL,
  `sender_type` VARCHAR(45) NULL,
  `receiver_id` BIGINT NULL,
  `receiver_type` VARCHAR(45) NULL,
  `content` TEXT NOT NULL,
  `read` INT NOT NULL DEFAULT 0,
  `offers_id` BIGINT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_messages_mail_users1_idx` (`users_id` ASC),
  INDEX `fk_messages_mail_messages_mail_group1_idx` (`messages_mail_group_id` ASC),
  INDEX `fk_messages_mail_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_messages_mail_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_messages_mail_messages_mail_group1`
    FOREIGN KEY (`messages_mail_group_id`)
    REFERENCES `netframe5`.`messages_mail_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_messages_mail_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.netframe_actions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`netframe_actions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `type_action` VARCHAR(45) NULL COMMENT 'follow, new_profile, like, new_friend, favorite_media, ',
  `expert_action` TINYINT NOT NULL DEFAULT 0,
  `author_id` BIGINT NULL,
  `author_type` VARCHAR(50) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_netframe_actions_users1_idx` (`users_id` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `fk_netframe_actions_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_netframe_actions_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_netframe_actions_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.messages_mail_group
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`messages_mail_group` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `sender_id` BIGINT NULL,
  `sender_type` VARCHAR(45) NULL,
  `receiver_id` BIGINT NULL,
  `receiver_type` VARCHAR(45) NULL,
  `type` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_messages_mail_group_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_messages_mail_group_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.shares
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`shares` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `author_id` BIGINT NOT NULL,
  `author_type` VARCHAR(50) NULL DEFAULT NULL,
  `disable_comments` TINYINT NOT NULL DEFAULT 0,
  `post_id` BIGINT NOT NULL,
  `post_type` VARCHAR(45) NOT NULL,
  `news_feed_id` BIGINT NULL,
  `media_id` BIGINT NULL,
  `language` VARCHAR(5) NULL,
  `content` TEXT CHARACTER SET 'utf8mb4' NULL,
  `parameters` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_shares_users1_idx` (`users_id` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `fk_shares_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_shares_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shares_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.visits
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`visits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `visit_id` BIGINT NOT NULL COMMENT 'l\'id du type d\'utilisateur qui a liké en tant que ... autre qu\'utilisateur.',
  `visit_type` VARCHAR(45) NOT NULL COMMENT 'talents, projet, house, community, angel, partner, user',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_visits_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_visits_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.tags
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`tags` (
  `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT(19) UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `lang` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_references_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `name` (`name` ASC),
  INDEX `fk_tags_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_tags_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.report_abuses
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`report_abuses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id_property` BIGINT NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `news_feeds_id` BIGINT UNSIGNED NOT NULL,
  `post_id` BIGINT NOT NULL,
  `post_type` VARCHAR(45) NOT NULL,
  `type_abuse` VARCHAR(255) NOT NULL,
  `number` INT(11) NOT NULL,
  `validate` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_report_abuses_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_report_abuses_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_has_report_abuses
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_has_report_abuses` (
  `users_id` BIGINT UNSIGNED NOT NULL,
  `report_abuses_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`users_id`, `report_abuses_id`),
  INDEX `fk_users_has_report_abuses_report_abuses1_idx` (`report_abuses_id` ASC),
  INDEX `fk_users_has_report_abuses_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_users_has_report_abuses_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_report_abuses_report_abuses1`
    FOREIGN KEY (`report_abuses_id`)
    REFERENCES `netframe5`.`report_abuses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.bookmarks
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`bookmarks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `projects_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `order` INT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_bookmarks_projects1_idx` (`projects_id` ASC),
  INDEX `fk_bookmarks_users1_idx` (`users_id` ASC),
  INDEX `fk_bookmarks_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_bookmarks_projects1`
    FOREIGN KEY (`projects_id`)
    REFERENCES `netframe5`.`projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bookmarks_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bookmarks_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.buzz
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`buzz` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `profile_id` BIGINT NOT NULL,
  `profile_type` VARCHAR(45) NOT NULL,
  `total_score` DECIMAL(5,2) NULL DEFAULT 0,
  `year_score` DECIMAL(5,2) NULL DEFAULT 0,
  `month_score` DECIMAL(5,2) NULL DEFAULT 0,
  `week_score` DECIMAL(5,2) NULL DEFAULT 0,
  `day_score` DECIMAL(5,2) NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_buzz_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_buzz_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.visitors
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`visitors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(250) NULL,
  `nb_connect` VARCHAR(45) NULL,
  `mail_sent` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.tracking_reports
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`tracking_reports` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `libelle` VARCHAR(150) NULL,
  `period_type` ENUM('daily','weekly','monthly') NULL,
  `value` DECIMAL(10,2) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_tracking_reports_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_tracking_reports_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.bounce_emails
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`bounce_emails` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_references
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_references` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `users_id_create` BIGINT(19) UNSIGNED NOT NULL,
  `tags_id` BIGINT(19) UNSIGNED NOT NULL,
  `like` BIGINT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `instances_id`, `tags_id`),
  INDEX `fk_users_references_users1_idx` (`users_id` ASC),
  INDEX `fk_users_references_users2_idx` (`users_id_create` ASC),
  INDEX `fk_users_references_tags1_idx` (`tags_id` ASC),
  INDEX `fk_users_references_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_users_references_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_references_users2`
    FOREIGN KEY (`users_id_create`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_references_tags1`
    FOREIGN KEY (`tags_id`)
    REFERENCES `netframe5`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_references_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.taggables
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`taggables` (
  `tag_id` BIGINT UNSIGNED NOT NULL,
  `taggable_type` VARCHAR(50) NOT NULL,
  `taggable_id` BIGINT NOT NULL,
  INDEX `fk_taggables_tags1_idx` (`tag_id` ASC),
  PRIMARY KEY (`tag_id`, `taggable_type`, `taggable_id`),
  CONSTRAINT `fk_taggables_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `netframe5`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.interests
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`interests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `tags_id` BIGINT(19) UNSIGNED NOT NULL,
  `weight` DECIMAL(15,5) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `instances_id`, `tags_id`),
  INDEX `fk_interests_users2_idx` (`users_id` ASC),
  INDEX `fk_interests_tags1_idx` (`tags_id` ASC),
  INDEX `fk_interests_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_interests_users2`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_interests_tags1`
    FOREIGN KEY (`tags_id`)
    REFERENCES `netframe5`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_interests_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.instances
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`instances` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `begin_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_has_instances
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_has_instances` (
  `users_id` BIGINT UNSIGNED NOT NULL,
  `access_granted` INT NOT NULL DEFAULT 1,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`users_id`, `instances_id`),
  INDEX `fk_users_has_instances_instances1_idx` (`instances_id` ASC),
  INDEX `fk_users_has_instances_users1_idx` (`users_id` ASC),
  INDEX `fk_users_has_instances_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_users_has_instances_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_instances_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_instances_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.instance_parameters
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`instance_parameters` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `parameter_name` VARCHAR(45) NOT NULL,
  `parameter_value` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_instance_parameters_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_instance_parameters_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.billing_count
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`billing_count` (
  `id` BIGINT(20) NOT NULL,
  `value` BIGINT(20) NOT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.billing_infos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`billing_infos` (
  `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  `instances_id` BIGINT(19) UNSIGNED NOT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_billing_infos_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_billing_infos_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.billing_lines
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`billing_lines` (
  `idbilling_line` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nb_users` INT(11) NULL DEFAULT NULL,
  `tva` DECIMAL(10,2) NOT NULL COMMENT 'En pourcentage',
  `created_at` DATETIME NOT NULL,
  `amountUnit` DECIMAL(10,3) NOT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `billings_id` BIGINT(19) UNSIGNED NOT NULL,
  `billings_instances_id` BIGINT(19) UNSIGNED NOT NULL,
  PRIMARY KEY (`idbilling_line`, `billings_id`, `billings_instances_id`),
  INDEX `fk_billing_lines_billings1_idx` (`billings_id` ASC, `billings_instances_id` ASC),
  CONSTRAINT `fk_billing_lines_billings1`
    FOREIGN KEY (`billings_id` , `billings_instances_id`)
    REFERENCES `netframe5`.`billings` (`id` , `instances_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 78
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.billings
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`billings` (
  `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
  `paid` TINYINT(1) NOT NULL DEFAULT '0',
  `total` DECIMAL(18,2) NOT NULL,
  `number` BIGINT(20) NOT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `instances_id` BIGINT(19) UNSIGNED NOT NULL,
  `last_attempt` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_billings_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_billings_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 109
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.boarding
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`boarding` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT NULL,
  `lang` VARCHAR(45) NOT NULL DEFAULT 'fr',
  `email` VARCHAR(255) NOT NULL,
  `send_notif` INT NOT NULL DEFAULT 0,
  `boarding_key` VARCHAR(255) NULL,
  `slug` VARCHAR(75) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.instance_metrics
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`instance_metrics` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `metric_name` VARCHAR(45) NULL,
  `metric_value` DECIMAL(15,5) NULL,
  `metric_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_instances_metrics_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_instances_metrics_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.channels
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`channels` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `profile_id` BIGINT NOT NULL,
  `profile_type` VARCHAR(50) NOT NULL,
  `default_channel` TINYINT NOT NULL DEFAULT 0,
  `auto_subscribe` TINYINT NOT NULL DEFAULT 0,
  `personnal` TINYINT NOT NULL DEFAULT 0,
  `live_members` INT NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `confidentiality` TINYINT NOT NULL DEFAULT 1,
  `free_join` TINYINT NOT NULL DEFAULT 0,
  `active` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_channels_users1_idx` (`users_id` ASC),
  INDEX `fk_channels_instances1_idx` (`instances_id` ASC),
  FULLTEXT INDEX `name` (`name` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  CONSTRAINT `fk_channels_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.channels_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`channels_has_users` (
  `channels_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`channels_id`, `users_id`),
  INDEX `fk_channels_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_channels_has_users_channels1_idx` (`channels_id` ASC),
  INDEX `fk_channels_has_users_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_channels_has_users_channels1`
    FOREIGN KEY (`channels_id`)
    REFERENCES `netframe5`.`channels` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_has_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.channels_has_news_feeds
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`channels_has_news_feeds` (
  `channels_id` BIGINT UNSIGNED NOT NULL,
  `news_feeds_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `read` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`channels_id`, `news_feeds_id`, `users_id`),
  INDEX `fk_channels_has_news_feeds_news_feeds1_idx` (`news_feeds_id` ASC),
  INDEX `fk_channels_has_news_feeds_channels1_idx` (`channels_id` ASC),
  INDEX `fk_channels_has_news_feeds_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_channels_has_news_feeds_channels1`
    FOREIGN KEY (`channels_id`)
    REFERENCES `netframe5`.`channels` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_has_news_feeds_news_feeds1`
    FOREIGN KEY (`news_feeds_id`)
    REFERENCES `netframe5`.`news_feeds` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_has_news_feeds_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.projects_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`projects_has_users` (
  `projects_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`projects_id`, `users_id`),
  INDEX `fk_projects_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_projects_has_users_projects1_idx` (`projects_id` ASC),
  INDEX `fk_projects_has_users_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_projects_has_users_projects1`
    FOREIGN KEY (`projects_id`)
    REFERENCES `netframe5`.`projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_has_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.user_auth_logger
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`user_auth_logger` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `users_id` BIGINT(19) UNSIGNED NOT NULL,
  `instances_id` BIGINT(19) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `instances_id`),
  INDEX `fk_user_auth_logger_users1_idx` (`users_id` ASC),
  INDEX `fk_user_auth_logger_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_user_auth_logger_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_auth_logger_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 32
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.user_notifications
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`user_notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `device` VARCHAR(255) NULL,
  `notification_identifier` VARCHAR(255) NULL,
  `frequency` VARCHAR(10) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `users_id`),
  INDEX `fk_user_notifications_users1_idx` (`users_id` ASC),
  INDEX `fk_user_notifications_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_user_notifications_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_notifications_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.apps
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`apps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `default_active` TINYINT NOT NULL DEFAULT 1,
  `self_subscribe` INT NOT NULL DEFAULT 1,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(45) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.instances_has_apps
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`instances_has_apps` (
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `apps_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`instances_id`, `apps_id`),
  INDEX `fk_instances_has_apps_apps1_idx` (`apps_id` ASC),
  INDEX `fk_instances_has_apps_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_instances_has_apps_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_instances_has_apps_apps1`
    FOREIGN KEY (`apps_id`)
    REFERENCES `netframe5`.`apps` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.medias_folders
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`medias_folders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `public_folder` TINYINT NOT NULL DEFAULT 0,
  `personnal_folder` TINYINT NOT NULL,
  `personnal_user_folder` BIGINT UNSIGNED NULL,
  `profile_id` BIGINT NULL,
  `profile_type` VARCHAR(50) NULL,
  `name` VARCHAR(255) NOT NULL,
  `default_folder` TINYINT NOT NULL DEFAULT 0 COMMENT 'Created by system and not updatable or deletable by users',
  `access_rights` VARCHAR(9) NOT NULL DEFAULT 'rw-rw-r--' COMMENT 'write permission defined by owner',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `instances_id`),
  INDEX `fk_medias_folders_instances1_idx` (`instances_id` ASC),
  INDEX `fk_medias_folders_users1_idx` (`users_id` ASC),
  INDEX `fk_medias_folders_medias_folders1_idx` (`medias_folders_id` ASC),
  INDEX `fk_medias_folders_users2_idx` (`personnal_user_folder` ASC),
  CONSTRAINT `fk_medias_folders_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_folders_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_folders_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_folders_users2`
    FOREIGN KEY (`personnal_user_folder`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.channels_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`channels_has_medias` (
  `channels_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`channels_id`, `medias_id`),
  INDEX `fk_channels_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_channels_has_medias_channels1_idx` (`channels_id` ASC),
  CONSTRAINT `fk_channels_has_medias_channels1`
    FOREIGN KEY (`channels_id`)
    REFERENCES `netframe5`.`channels` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_channels_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.drives
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`drives` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` INT(1) NULL DEFAULT NULL,
  `refresh_token` TEXT NULL DEFAULT NULL,
  `access_token` TEXT NULL DEFAULT NULL,
  `path` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `medias_folders_id` BIGINT(19) UNSIGNED NULL DEFAULT NULL,
  `medias_folders_users_id` BIGINT(19) UNSIGNED NOT NULL,
  `medias_folders_instances_id` BIGINT(19) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `medias_folders_users_id`, `medias_folders_instances_id`),
  INDEX `fk_drives_medias_folders1_idx` (`medias_folders_id` ASC, `medias_folders_users_id` ASC, `medias_folders_instances_id` ASC),
  CONSTRAINT `fk_drives_medias_folders1`
    FOREIGN KEY (`medias_folders_id` , `medias_folders_users_id` , `medias_folders_instances_id`)
    REFERENCES `netframe5`.`medias_folders` (`id` , `users_id` , `instances_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = utf8;

-- ----------------------------------------------------------------------------
-- Table netframe5.emojis
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`emojis` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `emojis_groups_id` INT UNSIGNED NOT NULL,
  `order` INT NULL,
  `name` VARCHAR(255) CHARACTER SET 'utf8mb4' NOT NULL,
  `value` VARCHAR(45) CHARACTER SET 'utf8mb4' NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_emojis_emojis_groups1_idx` (`emojis_groups_id` ASC),
  CONSTRAINT `fk_emojis_emojis_groups1`
    FOREIGN KEY (`emojis_groups_id`)
    REFERENCES `netframe5`.`emojis_groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.emojis_groups
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`emojis_groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order` INT NULL,
  `name` VARCHAR(45) CHARACTER SET 'utf8mb4' NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.device_fcm_token
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`device_fcm_token` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_uuid` VARCHAR(100) NULL,
  `fcm_token` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.notif_mails
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`notif_mails` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` INT NULL,
  `notif_type` VARCHAR(45) NULL,
  `notif_count` INT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.links
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`links` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` TEXT NOT NULL,
  `final_url` TEXT NOT NULL,
  `screenshot_path` VARCHAR(255) NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.linkables
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`linkables` (
  `link_id` BIGINT UNSIGNED NOT NULL,
  `linkable_id` BIGINT NOT NULL,
  `linkable_type` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`link_id`, `linkable_id`, `linkable_type`),
  CONSTRAINT `fk_table1_links1`
    FOREIGN KEY (`link_id`)
    REFERENCES `netframe5`.`links` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.user_mobile_device
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`user_mobile_device` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `duuid` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `users_id`),
  INDEX `fk_user_mobile_device_users1_idx` (`users_id` ASC),
  INDEX `fk_user_mobile_device_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_user_mobile_device_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_mobile_device_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.medias_archives
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`medias_archives` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1,
  `read_only` TINYINT NOT NULL DEFAULT 0,
  `linked` TINYINT NOT NULL DEFAULT 1,
  `under_workflow` TINYINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `share` BIGINT NOT NULL DEFAULT 0,
  `language` VARCHAR(10) NULL,
  `name` VARCHAR(245) NOT NULL,
  `access_rights` VARCHAR(9) NOT NULL DEFAULT 'rw-rw-r--',
  `description` TEXT NULL,
  `latitude` DECIMAL(12,8) NULL,
  `longitude` DECIMAL(12,8) NULL,
  `meta_title` VARCHAR(245) NULL,
  `meta_author` VARCHAR(255) NULL,
  `meta_alt` VARCHAR(255) NULL,
  `type` INT NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NULL,
  `feed_path` VARCHAR(255) NULL,
  `thumb_path` VARCHAR(255) NULL,
  `feed_width` INT NOT NULL DEFAULT 0,
  `feed_height` INT NOT NULL DEFAULT 0,
  `date` DATE NOT NULL,
  `confidentiality` INT NULL,
  `platform` VARCHAR(50) NULL,
  `mime_type` VARCHAR(45) NULL,
  `file_size` BIGINT NOT NULL DEFAULT 0,
  `encoded` INT(1) NULL DEFAULT 0,
  `startEncode` DATETIME NULL,
  `endEncode` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`, `medias_id`),
  INDEX `fk_medias_users1_idx` (`users_id` ASC),
  INDEX `fk_medias_instances1_idx` (`instances_id` ASC),
  INDEX `fk_medias_archives_medias1_idx` (`medias_id` ASC),
  CONSTRAINT `fk_medias_users10`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_instances10`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medias_archives_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.workflow_actions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`workflow_actions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_type` VARCHAR(45) NULL,
  `action_type` VARCHAR(45) NULL COMMENT 'key for action, \n- manage action in code\n- provide translations',
  `display_order` INT NULL DEFAULT 0,
  `notif_slug` VARCHAR(45) NULL,
  `group_action` TINYINT NOT NULL DEFAULT 0,
  `action_view` VARCHAR(45) NULL COMMENT 'in directory workflows/actions',
  `active` TINYINT NOT NULL DEFAULT 1,
  `is_final_action` TINYINT NOT NULL DEFAULT 0,
  `created_at` VARCHAR(45) NULL,
  `updated_at` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Store possible actions depending input objects\n\n@TODO : store possible chain\n\nmove script in other table (containing chain)\nand make n:m relation table with possible orders and final step\n\n';

-- ----------------------------------------------------------------------------
-- Table netframe5.workflows
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`workflows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `wf_datas` TEXT NULL COMMENT 'store datas of workflow (ex : files ids in json if validate files)',
  `finished` TINYINT NOT NULL DEFAULT 0,
  `type` VARCHAR(150) NOT NULL COMMENT 'validate_file\nvalidate_post\npublish_post_date\n...',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_workflows_users1_idx` (`users_id` ASC),
  INDEX `fk_workflows_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_workflows_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_workflows_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'attach users, comments action script (move to folder, publish post...)\n';

-- ----------------------------------------------------------------------------
-- Table netframe5.workflow_details_actions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`workflow_details_actions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `workflows_id` BIGINT UNSIGNED NOT NULL,
  `workflow_actions_id` INT UNSIGNED NOT NULL,
  `action_parameters` TEXT NULL COMMENT 'store action datas (user choosen or publication directory...)',
  `action_order` INT NOT NULL,
  `action_result` TEXT NULL,
  `action_validate` TINYINT NOT NULL DEFAULT 0,
  `action_date` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `workflows_id`, `workflow_actions_id`),
  INDEX `fk_table1_workflows1_idx` (`workflows_id` ASC),
  INDEX `fk_table1_workflow_actions1_idx` (`workflow_actions_id` ASC),
  INDEX `fk_workflow_details_actions_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_table1_workflow_actions1`
    FOREIGN KEY (`workflow_actions_id`)
    REFERENCES `netframe5`.`workflow_actions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_workflow_details_actions_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.tables_tasks
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`tables_tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(45) NULL,
  `author_id` BIGINT NOT NULL COMMENT 'id profile type',
  `author_type` VARCHAR(50) NOT NULL COMMENT 'Type de profile:- projet- angel- talent- maison- user--> relation app_setting',
  `default_task` TINYINT NOT NULL DEFAULT 0,
  `cols` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `tables_templates_id` BIGINT UNSIGNED NULL,
  `deadline` DATETIME NULL,
  `has_medias` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_news_users1_idx` (`users_id` ASC),
  INDEX `users_id` (`users_id` ASC),
  INDEX `author_id` (`author_id` ASC),
  INDEX `author_type` (`author_type` ASC),
  INDEX `fk_news_instances1_idx` (`instances_id` ASC),
  INDEX `fk_tables_tasks_tables_templates1_idx` (`tables_templates_id` ASC),
  CONSTRAINT `fk_news_instances10`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_news_users10`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tables_tasks_tables_templates1`
    FOREIGN KEY (`tables_templates_id`)
    REFERENCES `netframe5`.`tables_templates` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.tables_templates
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`tables_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(45) NULL,
  `cols` TEXT NULL,
  `default_template` TINYINT(4) NOT NULL DEFAULT 0,
  `default_profile_template` TINYINT NOT NULL DEFAULT 0,
  `language` VARCHAR(2) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `type` VARCHAR(45) NULL,
  `linked` TINYINT(1) NOT NULL DEFAULT '1',
  `has_medias` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_news_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_news_instances100`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.tables_rows
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`tables_rows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NULL,
  `cols` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `tables_tasks_id` BIGINT UNSIGNED NOT NULL,
  `parent` BIGINT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `deadline` DATETIME NOT NULL,
  `workflows_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `archived` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_tasks_tables_tasks1_idx` (`tables_tasks_id` ASC),
  INDEX `fk_tables_rows_tables_rows1_idx` (`parent` ASC),
  INDEX `fk_tables_rows_workflows1_idx` (`workflows_id` ASC),
  INDEX `fk_tables_rows_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_tables_rows_tables_rows1`
    FOREIGN KEY (`parent`)
    REFERENCES `netframe5`.`tables_rows` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tables_rows_workflows1`
    FOREIGN KEY (`workflows_id`)
    REFERENCES `netframe5`.`workflows` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tasks_tables_tasks1`
    FOREIGN KEY (`tables_tasks_id`)
    REFERENCES `netframe5`.`tables_tasks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tables_rows_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.profiles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `profiles_types_id` BIGINT UNSIGNED NOT NULL,
  `auto_subscribe` TINYINT NOT NULL DEFAULT 0,
  `owner_id` BIGINT NOT NULL,
  `owner_type` VARCHAR(55) NOT NULL,
  `active` INT(1) NOT NULL DEFAULT 1,
  `profile_media_id` BIGINT NULL,
  `name` VARCHAR(255) NULL,
  `free_join` TINYINT NOT NULL DEFAULT 1,
  `slug` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `location` VARCHAR(150) NULL,
  `longitude` DECIMAL(18,12) NULL,
  `latitude` DECIMAL(18,12) NULL,
  `confidentiality` INT NOT NULL DEFAULT 0,
  `share` BIGINT NOT NULL DEFAULT 0,
  `like` BIGINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_profiles_users1_idx` (`users_id` ASC),
  FULLTEXT INDEX `title` (`name` ASC),
  FULLTEXT INDEX `description` (`description` ASC),
  INDEX `fk_profiles_instances1_idx` (`instances_id` ASC),
  INDEX `fk_profiles_profiles_types1_idx` (`profiles_types_id` ASC),
  CONSTRAINT `fk_profiles_users10`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_instances10`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_profiles_types1`
    FOREIGN KEY (`profiles_types_id`)
    REFERENCES `netframe5`.`profiles_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.profiles_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`profiles_has_users` (
  `profiles_id` BIGINT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`profiles_id`, `users_id`),
  INDEX `fk_profiles_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_profiles_has_users_profiles1_idx` (`profiles_id` ASC),
  INDEX `fk_profiles_has_users_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_profiles_has_users_profiles1`
    FOREIGN KEY (`profiles_id`)
    REFERENCES `netframe5`.`profiles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_has_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.profiles_has_medias
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`profiles_has_medias` (
  `profiles_id` BIGINT UNSIGNED NOT NULL,
  `medias_id` BIGINT UNSIGNED NOT NULL,
  `medias_folders_id` BIGINT UNSIGNED NULL,
  `profile_image` INT(1) NOT NULL DEFAULT 0,
  `favorite` INT(1) NOT NULL DEFAULT 0,
  INDEX `fk_profiles_has_medias_medias1_idx` (`medias_id` ASC),
  INDEX `fk_profiles_has_medias_profiles1_idx` (`profiles_id` ASC),
  INDEX `fk_profiles_has_medias_medias_folders1_idx` (`medias_folders_id` ASC),
  CONSTRAINT `fk_profiles_has_medias_profiles1`
    FOREIGN KEY (`profiles_id`)
    REFERENCES `netframe5`.`profiles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_has_medias_medias1`
    FOREIGN KEY (`medias_id`)
    REFERENCES `netframe5`.`medias` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profiles_has_medias_medias_folders1`
    FOREIGN KEY (`medias_folders_id`)
    REFERENCES `netframe5`.`medias_folders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.profiles_types
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`profiles_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `default_svg_icon` VARCHAR(45) NULL,
  `order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `instances_id`),
  INDEX `fk_profiles_types_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_profiles_types_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.migrate_profiles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`migrate_profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `new_profile_id` BIGINT NOT NULL,
  `profile_id` BIGINT NOT NULL,
  `profile_type` VARCHAR(100) NOT NULL,
  `mig_users` TINYINT NOT NULL DEFAULT 0,
  `mig_medias` TINYINT NOT NULL DEFAULT 0,
  `mig_channels` TINYINT NOT NULL DEFAULT 0,
  `mig_newsfeed` TINYINT NOT NULL DEFAULT 0,
  `mig_news` TINYINT NOT NULL DEFAULT 0,
  `mig_likes` TINYINT NOT NULL DEFAULT 0,
  `mig_shares` TINYINT NOT NULL DEFAULT 0,
  `mig_events` TINYINT NOT NULL DEFAULT 0,
  `mig_offers` TINYINT NOT NULL DEFAULT 0,
  `mig_buzz` TINYINT NOT NULL DEFAULT 0,
  `mig_tags` TINYINT NOT NULL DEFAULT 0,
  `mig_playlists` TINYINT NOT NULL DEFAULT 0,
  `mig_comments` TINYINT NOT NULL DEFAULT 0,
  `mig_subscriptions` TINYINT NOT NULL DEFAULT 0,
  `mig_nfactions` TINYINT NOT NULL DEFAULT 0,
  `mig_msggroups` TINYINT NOT NULL DEFAULT 0,
  `mig_messages` TINYINT NOT NULL DEFAULT 0,
  `mig_mediasfolders` TINYINT NOT NULL DEFAULT 0,
  `mig_notifs` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_groups
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instances_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_groups_instances1_idx` (`instances_id` ASC),
  CONSTRAINT `fk_users_groups_instances1`
    FOREIGN KEY (`instances_id`)
    REFERENCES `netframe5`.`instances` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.users_groups_has_users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`users_groups_has_users` (
  `users_groups_id` INT UNSIGNED NOT NULL,
  `users_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`users_groups_id`, `users_id`),
  INDEX `fk_users_groups_has_users_users1_idx` (`users_id` ASC),
  INDEX `fk_users_groups_has_users_users_groups1_idx` (`users_groups_id` ASC),
  CONSTRAINT `fk_users_groups_has_users_users_groups1`
    FOREIGN KEY (`users_groups_id`)
    REFERENCES `netframe5`.`users_groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.groups_profiles
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`groups_profiles` (
  `users_group_id` INT UNSIGNED NOT NULL,
  `groups_profile_id` BIGINT NOT NULL,
  `groups_profile_type` VARCHAR(50) NOT NULL,
  `roles_id` INT UNSIGNED NOT NULL,
  INDEX `fk_table1_users_groups1_idx` (`users_group_id` ASC),
  INDEX `fk_groups_profiles_roles1_idx` (`roles_id` ASC),
  CONSTRAINT `fk_table1_users_groups1`
    FOREIGN KEY (`users_group_id`)
    REFERENCES `netframe5`.`users_groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_groups_profiles_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `netframe5`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.channels_externals_access
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`channels_externals_access` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `channels_id` BIGINT UNSIGNED NOT NULL,
  `slug` VARCHAR(45) NOT NULL,
  `token` VARCHAR(150) NOT NULL,
  `lastname` VARCHAR(255) NULL,
  `firstname` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `start_at` DATETIME NOT NULL,
  `expire_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `channels_id`),
  INDEX `fk_channels_externals_access_channels1_idx` (`channels_id` ASC),
  CONSTRAINT `fk_channels_externals_access_channels1`
    FOREIGN KEY (`channels_id`)
    REFERENCES `netframe5`.`channels` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Table netframe5.views
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `netframe5`.`views` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT UNSIGNED NOT NULL,
  `post_id` BIGINT NULL,
  `post_type` VARCHAR(155) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_views_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_views_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `netframe5`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
SET FOREIGN_KEY_CHECKS = 1;
