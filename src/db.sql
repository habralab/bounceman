create table `mbox` (
    `id` int unsigned not null auto_increment,
    `name` varchar(255) not null,
    `host` varchar(255) not null,
    `secure` boolean not null,
    `username` varchar(255) not null,
    `password` varchar(255) not null,
    `finbox` varchar(255) not null,
    `ftrash` varchar(255) not null,
    `fjunk` varchar(255) not null,
    `is_active` boolean not null,
    primary key (`id`)
) engine = innodb charset = utf8mb3;

create table `bounce` (
    `id` int unsigned not null auto_increment,
    `mbox_id` int unsigned not null,
    `delivery_date` int unsigned not null,
    `envelope_to` varchar(255) not null,
    `recipient` varchar(255) not null,
    `error_text` text not null,
    primary key (`id`),
    foreign key (`mbox_id`) references `mbox` (`id`) on delete restrict
) engine = innodb charset = utf8mb3;

create table `log` (
    `id` int unsigned not null auto_increment,
    `mbox_id` int unsigned not null,
    `ctime` int unsigned not null,
    `duration` int unsigned not null,
    `processed` int unsigned not null,
    `success` int unsigned not null,
    `fail` int unsigned not null,
    `skip` int unsigned not null,
    primary key (`id`),
    foreign key (`mbox_id`) references `mbox` (`id`) on delete restrict
) engine = innodb charset = utf8mb3;
