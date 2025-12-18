-- Database Structure for Nexus RPG
-- Universal RPG Builder

CREATE DATABASE IF NOT EXISTS nexus_rpg CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexus_rpg;

-- Drop tables if they exist to start fresh (order matters for foreign keys)
DROP TABLE IF EXISTS build_accessories;
DROP TABLE IF EXISTS build_weapons;
DROP TABLE IF EXISTS build_skills;
DROP TABLE IF EXISTS character_accessories;
DROP TABLE IF EXISTS character_weapons;
DROP TABLE IF EXISTS accessories;
DROP TABLE IF EXISTS accessory_categories;
DROP TABLE IF EXISTS weapons;
DROP TABLE IF EXISTS weapon_types;
DROP TABLE IF EXISTS elements;
DROP TABLE IF EXISTS builds;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS characters;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS games;

-- 1. Games Table (Hub for multiple supported games)
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE, -- e.g. "expedition-33", "baldurs-gate-3"
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Characters Table (Linked to a Game)
CREATE TABLE characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_char_per_game (game_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Skills Table
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NULL, -- Link to character if specific
    game_id INT NOT NULL, -- Link to game for general skills
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon_path VARCHAR(255),
    damage INT DEFAULT 0,
    cost VARCHAR(50),
    type VARCHAR(50),
    additional_info TEXT, -- For Prerequisites/Effects/Notes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Builds Table
CREATE TABLE builds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    character_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Build Skills
CREATE TABLE build_skills (
    build_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (build_id, skill_id),
    FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Elements Table (Fire, Void, Physical, Ice, Lightning, Earth, Light, etc.)
CREATE TABLE elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,       -- e.g. "Fire", "Void", "Physical"
    icon_url VARCHAR(255) NULL,      -- Local path to element icon
    color VARCHAR(7) NULL,           -- Hex color for UI styling e.g. "#FF5722"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_element_per_game (game_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Weapon Types Table (Swords, Guns, Staves, etc. - per game)
CREATE TABLE weapon_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_weapon_type_per_game (game_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Weapons Table (Enhanced with element FK and JSON scaling)
CREATE TABLE weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    weapon_type_id INT NULL,
    element_id INT NULL,             -- FK to elements table
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    attack INT NULL,                 -- Power stat from wiki
    scaling JSON NULL,               -- {"Vit": "B", "Def": "A", "Agi": null, "Luck": "S"}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (weapon_type_id) REFERENCES weapon_types(id) ON DELETE SET NULL,
    FOREIGN KEY (element_id) REFERENCES elements(id) ON DELETE SET NULL,
    UNIQUE KEY unique_weapon_per_game (game_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Character-Weapons Junction Table (Many-to-Many for shared weapons like Gustave/Verso)
CREATE TABLE character_weapons (
    character_id INT NOT NULL,
    weapon_id INT NOT NULL,
    PRIMARY KEY (character_id, weapon_id),
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    FOREIGN KEY (weapon_id) REFERENCES weapons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Accessory Categories Table (Universal: Pictos, Summons, Talismans, Rings, etc.)
CREATE TABLE accessory_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,     -- e.g. "Pictos" (E33), "Spirit Ashes" (Elden Ring), "Materia" (FF7)
    slug VARCHAR(100) NOT NULL,     -- e.g. "pictos", "spirit-ashes", "materia"
    description TEXT,
    icon_url VARCHAR(255),
    max_equippable INT DEFAULT 1,   -- How many can be equipped at once
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_per_game (game_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Accessories Table (Universal items: Pictos, Summons, Talismans, etc.)
CREATE TABLE accessories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    category_id INT NOT NULL,       -- Links to accessory_categories
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    wiki_url VARCHAR(255),
    effect TEXT,                    -- Main effect description
    cost VARCHAR(50) NULL,          -- FP cost, MP cost, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES accessory_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_accessory_per_game (game_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Character-Accessories Junction Table
CREATE TABLE character_accessories (
    character_id INT NOT NULL,
    accessory_id INT NOT NULL,
    PRIMARY KEY (character_id, accessory_id),
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Build Weapons (for saved builds)
CREATE TABLE build_weapons (
    build_id INT NOT NULL,
    weapon_id INT NOT NULL,
    slot INT DEFAULT 1,             -- Weapon slot number
    PRIMARY KEY (build_id, weapon_id),
    FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE,
    FOREIGN KEY (weapon_id) REFERENCES weapons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Build Accessories (for saved builds)
CREATE TABLE build_accessories (
    build_id INT NOT NULL,
    accessory_id INT NOT NULL,
    slot INT DEFAULT 1,             -- Accessory slot number
    PRIMARY KEY (build_id, accessory_id),
    FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Data
INSERT INTO games (name, slug, description) VALUES ('Clair Obscur: Expedition 33', 'expedition-33', 'A reactive turn-based RPG inspired by Belle Époque masterpieces.');

-- Initial Accessory Categories for E33
INSERT INTO accessory_categories (game_id, name, slug, description, max_equippable) VALUES 
(1, 'Pictos', 'pictos', 'Mystical paintings that grant special abilities and passive effects.', 4);