BEGIN TRANSACTION;
CREATE TABLE "users" (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`email`	TEXT NOT NULL UNIQUE,
	`password`	TEXT NOT NULL,
	`firstName`	TEXT NOT NULL,
	`lastName`	TEXT NOT NULL,
	`role`	TEXT NOT NULL,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT
);
CREATE TABLE `user_tokens` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`userId`	INTEGER NOT NULL,
	`type`	TEXT NOT NULL,
	`token`	TEXT NOT NULL,
	`expiration`	TEXT,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT,
	FOREIGN KEY(`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
CREATE TABLE "secrets" (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	TEXT NOT NULL,
	`description`	TEXT,
	`notes`	TEXT,
	`category`	TEXT NOT NULL,
	`username`	TEXT,
	`password`	TEXT,
	`email`	TEXT,
	`website`	TEXT,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT,
	`userId`	INTEGER NOT NULL,
	FOREIGN KEY(`userId`) REFERENCES `users`(`id`)
);
CREATE UNIQUE INDEX `user_tokens_id_unique` ON `user_tokens` (
	`id`	ASC
);
CREATE UNIQUE INDEX `secrets_id_unique` ON `secrets` (
	`id`	ASC
)























;
COMMIT;
