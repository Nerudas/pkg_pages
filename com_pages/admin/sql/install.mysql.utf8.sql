CREATE TABLE IF NOT EXISTS `#__pages` (
  `id`          INT(11)          NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `content`     LONGTEXT         NOT NULL DEFAULT '',
  `css`         LONGTEXT         NOT NULL DEFAULT '',
  `js`          LONGTEXT         NOT NULL DEFAULT '',
  `state`       TINYINT(3)       NOT NULL DEFAULT '0',
  `attribs`     TEXT             NOT NULL DEFAULT '',
  `hits`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 0;