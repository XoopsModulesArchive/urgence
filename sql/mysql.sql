CREATE TABLE `urgence` (
    `id`           TINYINT(4)   NOT NULL AUTO_INCREMENT,
    `uid`          MEDIUMINT(8) NOT NULL DEFAULT 0,
    `content`      VARCHAR(30)  NOT NULL DEFAULT '',
    `phone_number` VARCHAR(30)  NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
    ENGINE = ISAM;
