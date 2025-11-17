CREATE TABLE IF NOT EXISTS wcf1_deleted_unconfirmed_user_log (
	logID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID INT(10) NOT NULL,
	username VARCHAR(255) NOT NULL DEFAULT '',
	email VARCHAR(255) NOT NULL DEFAULT '',
	registrationDate INT(10) NOT NULL DEFAULT 0,
	deletionDate INT(10) NOT NULL DEFAULT 0,
	INDEX (deletionDate),
	INDEX (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

