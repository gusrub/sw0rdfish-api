BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS `website_credential_secrets` (
	`id`	INTEGER NOT NULL,
	`url`	TEXT NOT NULL,
	`username`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`id`) REFERENCES `secrets`(`id`) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `users` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`email`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	`firstName`	TEXT NOT NULL,
	`lastName`	TEXT NOT NULL,
	`role`	TEXT NOT NULL,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT
);
CREATE TABLE IF NOT EXISTS `user_tokens` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`userId`	INTEGER NOT NULL,
	`type`	TEXT NOT NULL,
	`token`	TEXT NOT NULL,
	`expiration`	TEXT,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT,
	FOREIGN KEY(`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `secrets` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	TEXT NOT NULL,
	`description`	TEXT,
	`notes`	TEXT,
	`category`	TEXT NOT NULL,
	`userId`	INTEGER NOT NULL,
	`createdDate`	TEXT NOT NULL,
	`updatedDate`	TEXT,
	FOREIGN KEY(`userId`) REFERENCES `users`(`id`)
);
CREATE TABLE IF NOT EXISTS `generic_secrets` (
	`id`	INTEGER NOT NULL,
	`secret`	TEXT NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`id`) REFERENCES `secrets`(`id`) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `email_secrets` (
	`id`	INTEGER NOT NULL,
	`email`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	`url`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`id`) REFERENCES `secrets`(`id`) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `credit_card_secrets` (
	`id`	INTEGER NOT NULL,
	`cardholder`	TEXT NOT NULL,
	`number`	TEXT NOT NULL,
	`expirationYear`	TEXT NOT NULL,
	`expirationMonth`	TEXT NOT NULL,
	`csc`	TEXT NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`id`) REFERENCES `secrets`(`id`) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `bank_account_secrets` (
	`id`	INTEGER NOT NULL,
	`accountNumber`	TEXT NOT NULL,
	`routingNumber`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`id`) REFERENCES `secrets`(`id`) ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS `website_credential_secrets_id_unique` ON `website_credential_secrets` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `users_id_unique` ON `users` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `users_email_unique` ON `users` (
	`email`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `user_tokens_id_unique` ON `user_tokens` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `secrets_id_unique` ON `secrets` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `generic_secrets_id_unique` ON `generic_secrets` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `email_secrets_id_unique` ON `email_secrets` (
	`id`	ASC
);
CREATE UNIQUE INDEX IF NOT EXISTS `bank_account_secrets_id_unique` ON `bank_account_secrets` (
	`id`	ASC
);
COMMIT;
