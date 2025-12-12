<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;

$descriptions = [
    'Lune' => "Lune is a member of Expedition 33 in Clair Obscur: Expedition 33. The daughter of prominent researchers, Lune has a deep thirst for knowledge and has sacrificed everything to complete her parents’ work. Her one goal is to unravel the mystery of the Paintress. Entrusted with charting a path for the Expedition, Lune feels keenly the weight of responsibility and the stakes involved. She cannot and will not allow the Expedition to fail.",

    'Sciel' => "Sciel is a member of Expedition 33 in Clair Obscur: Expedition 33. A farmer turned teacher, Sciel is warm and outgoing, enjoying life day by day. She is at ease with death, having long accepted the brutality of their world. But her teasing smile masks a dark and painful past. Though deeply committed to the Expedition, she is untroubled by the spectre of failure. They will do all in their power, and it will either be enough, or it won't.",

    'Maelle' => "Maelle is a main protagonist, one of the members of Expedition 33 and the youngest of their group, going 9 years before her gommage. Orphaned at age 3, Maelle has never felt at home in Lumière. She has difficulty connecting with and trusting others, but has come to appreciate her foster brother Gustave. At 16 years of age, Maelle is significantly younger than the other Expeditioners. Unlike them, she views the Expedition as her chance to explore the world beyond Lumière and finally forge her own destiny.",

    'Verso' => "Verso is a main protagonist in Clair Obscur: Expedition 33. A mirror of Verso Dessendre painted by the Paintress in the world of the Canvas, he is the estranged son of Renoir and the brother of Alicia and Clea. Verso is an outsider of unknown origins who closely tracks the Expedition.",

    'Monoco' => "Monoco is an older gestral that joins to help out Expedition 33 in Clair Obscur: Expedition 33. Monoco is a Gestral, a group of friendly beings who enjoy the thrill of battle and view competition as a form of meditation. As one of the few Gestrals who speak the human language, Monoco has adopted a scholarly demeanour which belies a bloodthirsty spirit. Though Gestrals are untouched by the Paintress, the prospect of combat entices Monoco to join the Expedition."
];

$pdo = Database::getInstance()->getConnection();

foreach ($descriptions as $name => $desc) {
    echo "Updating $name...\n";
    $stmt = $pdo->prepare("UPDATE characters SET description = ? WHERE name = ?");
    $stmt->execute([$desc, $name]);
}
echo "All descriptions updated to exact Wiki text.\n";
