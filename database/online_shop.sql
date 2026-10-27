CREATE DATABASE IF NOT EXISTS `online_shop`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `online_shop`;

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(80) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(1000) NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `isbn` VARCHAR(20) NOT NULL,
    `publisher` VARCHAR(150) NULL,
    `publication_year` SMALLINT UNSIGNED NULL,
    `description` VARCHAR(2000) NULL,
    `price` DECIMAL(12, 2) NOT NULL DEFAULT 0,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_books_isbn` (`isbn`),
    KEY `idx_books_category_id` (`category_id`),
    CONSTRAINT `fk_books_categories`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

UPDATE `categories`
SET `name` = 'Khoa học',
    `description` = 'Sách khoa học, công nghệ và khám phá.'
WHERE `name` = 'Điện tử';

UPDATE `categories`
SET `name` = 'Lịch sử',
    `description` = 'Sách lịch sử, địa lý và văn hóa.'
WHERE `name` = 'Gia dụng';

INSERT INTO `admins` (`username`, `password_hash`, `full_name`, `is_active`)
VALUES (
    'admin',
    '$2y$10$8/yeHjOLSHzBCUBTL.5JcOA2/racbIDkhgE.pwhKeEwBOwz2tavNS',
    'Quản trị viên',
    1
)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

INSERT INTO `categories` (`name`, `description`, `status`)
VALUES
    ('Văn học', 'Tiểu thuyết, truyện ngắn và tác phẩm văn học.', 'active'),
    ('Thiếu nhi', 'Truyện tranh và sách dành cho thiếu nhi.', 'active'),
    ('Kỹ năng sống', 'Sách kỹ năng, phát triển bản thân và nghề nghiệp.', 'active'),
    ('Khoa học', 'Sách khoa học, công nghệ và khám phá.', 'active'),
    ('Lịch sử', 'Sách lịch sử, địa lý và văn hóa.', 'active'),
    ('Giáo dục', 'Sách giáo khoa, tham khảo và học ngoại ngữ.', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `books`
    (`category_id`, `title`, `author`, `isbn`, `publisher`, `publication_year`, `description`, `price`, `stock`, `status`)
SELECT c.`id`, sample.`title`, sample.`author`, sample.`isbn`, sample.`publisher`,
       sample.`publication_year`, sample.`description`, sample.`price`, sample.`stock`, 'active'
FROM (
    SELECT 'Dế Mèn Phiêu Lưu Ký' AS `title`, 'Tô Hoài' AS `author`, 'BOOK-0001' AS `isbn`,
           'Kim Đồng' AS `publisher`, 1941 AS `publication_year`,
           'Tác phẩm văn học thiếu nhi kinh điển.' AS `description`, 85000 AS `price`, 20 AS `stock`,
           'Thiếu nhi' AS `category`
    UNION ALL
    SELECT 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 'BOOK-0002',
           'Trẻ', 2010, 'Truyện dài về tuổi thơ và tình cảm gia đình.', 150000, 15, 'Văn học'
    UNION ALL
    SELECT 'Đắc Nhân Tâm', 'Dale Carnegie', 'BOOK-0003',
           'NXB Tổng hợp TP.HCM', 1936, 'Sách về nghệ thuật giao tiếp và ứng xử.', 110000, 12, 'Kỹ năng sống'
) AS sample
INNER JOIN `categories` c ON c.`name` = sample.`category`
WHERE NOT EXISTS (
    SELECT 1 FROM `books` b WHERE b.`isbn` = sample.`isbn`
);

INSERT INTO `book_stock_movements`
    (`book_id`, `admin_id`, `movement_type`, `quantity`, `reference_key`, `notes`, `created_at`)
SELECT b.`id`, NULL, 'initial', b.`stock`, CONCAT('initial:', b.`isbn`),
       'Ghi nhận tồn kho khi khởi tạo hệ thống.', NOW()
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
