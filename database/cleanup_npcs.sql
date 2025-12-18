-- Cleanup script to remove NPC characters that were incorrectly imported
-- NPCs: The Paintress, Sophie, Curator, Esquie, Renoir

USE nexus_rpg;

-- First delete skills associated with NPC characters
DELETE FROM skills WHERE character_id IN (
    SELECT id FROM characters WHERE name IN ('The Paintress', 'Sophie', 'Curator', 'Esquie', 'Renoir')
);

-- Then delete the NPC characters themselves
DELETE FROM characters WHERE name IN ('The Paintress', 'Sophie', 'Curator', 'Esquie', 'Renoir');

-- Verification: Show remaining characters
SELECT id, name FROM characters;
