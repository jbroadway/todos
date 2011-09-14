create table `todo` (
	`id` integer primary key,
	`text` char(128) not null,
	`done` int not null,
	`order` int not null
);

create index `todo_order` on `todo` (`order`);
