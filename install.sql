CREATE TABLE wcf1_deinestrainreviews_auto_delete_unconfirmed_users_log (
    logID INT(10) NOT NULL AUTO_INCREMENT,
    executionTime INT(10) NOT NULL,
    usersDeletedCount INT(10) DEFAULT NULL,
    userID INT(10) DEFAULT NULL,
    username VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (logID),
    KEY executionTime (executionTime),
    KEY userID (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

