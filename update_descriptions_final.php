<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;

$descriptions = [
    'Lune' => "Lune is a member of Expedition 33 in Clair Obscur: Expedition 33. The daughter of prominent researchers, Lune was raised to unravel the mystery of the Paintress. An obsessive scholar and mage, she is Expedition 33’s chief magic user, dedicating every waking moment to finding a way to break the cycle. She is entrusted with charting a path for the Expedition and feels a strong sense of responsibility, determined not to let it fail.",
    'Sciel' => "Sciel is a character in Clair Obscur: Expedition 33, a turn-based RPG set in a world inspired by Belle Époque France. She is described as a warm and outgoing farmer turned teacher, who has accepted the brutality of her world with a smile that conceals a dark past. Despite her cheerful demeanor, she is at ease with death, viewing it as a friend. She joins Expedition 33 to help stop the Paintress from continuing to paint death.",
    'Maelle' => "Maelle is a main protagonist and the youngest member of Expedition 33 in Clair Obscur: Expedition 33. Growing up as an orphan in Lumière, she was fascinated by the romance of the Expeditions and eagerly sought her place among them. Though the reality of their suicide mission weighs heavily on her, she brings a youthful determination to the group.",
    'Verso' => "Verso is a mysterious figure tracking the Expedition in Clair Obscur: Expedition 33. His motives are unclear, but his command over magic suggests a connection to the Paintress herself. He operates from the shadows, watching the Expedition's every move. He is a perfectionist in combat, unlocking power through flawless execution.",
    'Monoco' => "Monoco is a lone wanderer found in the wastes in Clair Obscur: Expedition 33. Described as a Gestral, a friendly species that views battle as a form of meditation. Despite his scholarly demeanor, he possesses a bloodthirsty spirit and is drawn to join Expedition 33 due to the thrill of combat. He brings knowledge of the world outside Lumière that may prove vital to the Expedition's success."
];

$pdo = Database::getInstance()->getConnection();

foreach ($descriptions as $name => $desc) {
    if (strpos($desc, 'Placeholder') === false) {
        $stmt = $pdo->prepare("UPDATE characters SET description = ? WHERE name = ?");
        $stmt->execute([$desc, $name]);
        echo "Updated $name description.\n";
    }
}
