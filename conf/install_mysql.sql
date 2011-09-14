create table `todo` (
	`id` int not null auto_increment primary key,
	`text` char(128) not null,
	`done` int not null,
	`order` int not null,
	index (`order`)
);
