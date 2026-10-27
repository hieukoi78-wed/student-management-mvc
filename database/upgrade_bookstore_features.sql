USE `online_shop`;

UPDATE `categories` AS old_category
LEFT JOIN `categories` AS existing_category
    ON existing_category.`name` = 'Khoa học'
    AND existing_category.`id` <> old_category.`id`
SET old_category.`name` = IF(existing_category.`id` IS NULL, 'Khoa học', 'Điện tử (dữ liệu cũ)'),
    old_category.`status` = IF(existing_category.`id` IS NULL, old_category.`status`, 'inactive')
WHERE old_category.`name` = 'Điện tử';

UPDATE `categories` AS old_category
LEFT JOIN `categories` AS existing_category
    ON existing_category.`name` = 'Lịch sử'
    AND existing_category.`id` <> old_category.`id`
SET old_category.`name` = IF(existing_category.`id` IS NULL, 'Lịch sử', 'Gia dụng (dữ liệu cũ)'),
    old_category.`status` = IF(existing_category.`id` IS NULL, old_category.`status`, 'inactive')
WHERE old_category.`name` = 'Gia dụng';

CREATE TABLE IF NOT EXISTS `members` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(80) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_members_username` (`username`),
    UNIQUE KEY `uq_members_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `book_stock_movements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `book_id` INT UNSIGNED NOT NULL,
    `admin_id` INT UNSIGNED NULL,
    `movement_type` ENUM('initial', 'received', 'sold') NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `reference_key` VARCHAR(100) NULL,
    `notes` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_stock_reference` (`reference_key`),
    KEY `idx_stock_book_created` (`book_id`, `created_at`),
    CONSTRAINT `fk_stock_books`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_stock_admins`
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sales_orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(40) NOT NULL,
    `admin_id` INT UNSIGNED NOT NULL,
    `customer_name` VARCHAR(120) NULL,
    `total_amount` DECIMAL(14, 2) NOT NULL,
    `status` ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed',
    `sold_at` DATETIME NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_sales_order_number` (`order_number`),
    KEY `idx_sales_status_sold_at` (`status`, `sold_at`),
    CONSTRAINT `fk_sales_admins`
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sales_order_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `book_id` INT UNSIGNED NOT NULL,
    `book_title` VARCHAR(200) NOT NULL,
    `isbn` VARCHAR(20) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `unit_price` DECIMAL(12, 2) NOT NULL,
    `line_total` DECIMAL(14, 2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sales_item_book` (`book_id`),
    CONSTRAINT `fk_sales_items_orders`
        FOREIGN KEY (`order_id`) REFERENCES `sales_orders` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_sales_items_books`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `chat_messages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `room_type` ENUM('lobby', 'admin_direct') NOT NULL,
    `member_id` INT UNSIGNED NULL,
    `sender_type` ENUM('admin', 'member') NOT NULL,
    `sender_id` INT UNSIGNED NOT NULL,
    `message` VARCHAR(2000) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_chat_room_messages` (`room_type`, `member_id`, `id`),
    CONSTRAINT `fk_chat_direct_member`
        FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `book_stock_movements`
    (`book_id`, `admin_id`, `movement_type`, `quantity`, `reference_key`, `notes`, `created_at`)
SELECT b.`id`, NULL, 'initial', b.`stock`, CONCAT('initial:', b.`isbn`),
       'Ghi nhận tồn kho khi nâng cấp hệ thống.', NOW()
FROM `books` b
WHERE NOT EXISTS (
    SELECT 1 FROM `book_stock_movements` m
    WHERE m.`reference_key` = CONCAT('initial:', b.`isbn`)
);

INSERT INTO `members` (`username`, `email`, `password_hash`, `full_name`, `is_active`)
VALUES (
    'thanhvien1',
    'thanhvien1@example.test',
    '$2y$10$8/yeHjOLSHzBCUBTL.5JcOA2/racbIDkhgE.pwhKeEwBOwz2tavNS',
    'Thành viên mẫu',
    1
)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);
