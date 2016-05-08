DROP TABLE IF EXISTS product;

CREATE TABLE product (
    product_id INT(10) NOT NULL AUTO_INCREMENT,
    product_name VARCHAR(10) NULL DEFAULT NULL,
    price INT(3) NULL DEFAULT NULL,
    price2 DECIMAL(3,2) NULL DEFAULT NULL,
    product_status ENUM("available","no stock") NOT NULL DEFAULT "available",
    description VARCHAR(100) NULL DEFAULT NULL,
    category_id INT(10) NULL DEFAULT 0,
    date_created DATE NULL DEFAULT "0000-00-00",
    PRIMARY KEY (product_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS category;

CREATE TABLE category (
    category_id INT(10) NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(10) NOT NULL,
    parent_id INT(10) NOT NULL DEFAULT 0,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB;