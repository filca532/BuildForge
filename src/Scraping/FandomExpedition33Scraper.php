<?php

namespace BuildForge\Scraping;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use BuildForge\DTO\CharacterDTO;
use BuildForge\DTO\SkillDTO;
use BuildForge\DTO\WeaponDTO;

class FandomExpedition33Scraper implements ScraperInterface
{
    private Client $client;
    private const BASE_URL = 'https://clair-obscur.fandom.com';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'BuildForge/1.0'
            ]
        ]);
    }

    private function getWithRetry(string $path, array $options = []): ?\Psr\Http\Message\ResponseInterface
    {
        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                return $this->client->get($path, $options);
            } catch (\Exception $e) {
                // If 503 or 429, wait and retry
                $code = $e->getCode();
                if ($code == 503 || $code == 429) {
                    $attempt++;
                    $sleep = 2 * $attempt; // 2s, 4s, 6s...
                    echo "   [Scraper] API Overload ($code). Retrying in {$sleep}s...\n";
                    sleep($sleep);
                    continue;
                }
                // Other errors, throw them
                throw $e;
            }
        }
        throw new \Exception("API request failed after $maxRetries retries.");
    }

    /**
     * @return CharacterDTO[]
     */
    public function getCharacters(): array
    {
        $characters = [];
        // Only playable characters - excluding NPCs (The Paintress, Sophie, Curator, Esquie, Renoir)
        $targetNames = ['Gustave', 'Lune', 'Sciel', 'Maelle', 'Verso', 'Monoco'];
        // Extended list just in case, but keep focus on main ones

        // 1. Fetch Class Descriptions from the Main "Characters" Page Table
        $descriptionsMap = $this->getDescriptionsFromListing();

        foreach ($targetNames as $name) {
            sleep(1); // Standard polite delay
            try {
                // 2. Fetch Image via PageImages API (Keep high-res images)
                $imageUrl = null;
                $imgResponse = $this->getWithRetry('/api.php', [
                    'query' => [
                        'action' => 'query',
                        'titles' => $name,
                        'prop' => 'pageimages',
                        'piprop' => 'original',
                        'format' => 'json'
                    ]
                ]);
                $imgJson = json_decode((string) $imgResponse->getBody(), true);
                if (isset($imgJson['query']['pages'])) {
                    foreach ($imgJson['query']['pages'] as $page) {
                        if (isset($page['original']['source'])) {
                            $imageUrl = $page['original']['source'];
                        }
                    }
                }

                // Final fallback if API returns no image
                if (!$imageUrl) {
                    $imageUrl = 'https://clair-obscur.fandom.com/wiki/Special:FilePath/' . $name . '.png';
                }

                // 3. Use Description from Table (or fallback)
                $description = $descriptionsMap[$name] ?? "Description unavailable.";

                if (isset($descriptionsMap[$name])) {
                    $characters[] = new CharacterDTO(
                        name: $name,
                        imageUrl: $imageUrl,
                        description: $description
                    );
                } else {
                    // Only add if found in map or critical chars
                    if (in_array($name, ['Gustave', 'Lune', 'Sciel', 'Maelle'])) {
                        $characters[] = new CharacterDTO(
                            name: $name,
                            imageUrl: $imageUrl,
                            description: $description
                        );
                    }
                }

            } catch (\Exception $e) {
                echo "Error scraping $name via API: " . $e->getMessage() . "\n";
            }
        }

        return $characters;
    }

    private function getDescriptionsFromListing(): array
    {
        $map = [];
        try {
            $res = $this->getWithRetry('/api.php', [
                'query' => [
                    'action' => 'parse',
                    'page' => 'Characters',
                    'prop' => 'text',
                    'format' => 'json'
                ]
            ]);
            $json = json_decode((string) $res->getBody(), true);

            if (isset($json['parse']['text']['*'])) {
                $crawler = new Crawler($json['parse']['text']['*']);

                // Inspect the first table
                $crawler->filter('table')->first()->filter('tr')->each(function (Crawler $row) use (&$map) {
                    $cells = $row->filter('td');
                    if ($cells->count() >= 6) {
                        // Col 1 (Index 1) = Name (usually inside a link or just text)
                        // Col 5 (Index 5) = Description
                        $nameCell = $cells->eq(1);
                        $descCell = $cells->eq(5);

                        $name = trim($nameCell->text());
                        $desc = trim($descCell->text());

                        // Clean up newlines or extra spaces
                        $desc = preg_replace('/\s+/', ' ', $desc);

                        if (!empty($name) && !empty($desc)) {
                            $map[$name] = $desc;
                            echo "   [Scraper] Found table desc for $name.\n";
                        }
                    }
                });
            }
        } catch (\Exception $e) {
            echo "   [Scraper] Error fetching listing descriptions: " . $e->getMessage() . "\n";
        }
        return $map;
    }

    private ?Crawler $skillsPageCrawler = null;

    /**
     * @return SkillDTO[]
     */
    public function getSkills(string $characterName): array
    {
        // Try scraping real skills first (Plan A)
        $skills = $this->scrapeSkillsFromPage($characterName);

        // Plan B: If no skills found, generate thematic placeholder skills
        if (empty($skills)) {
            echo "  ! No skills scraped for $characterName. Generating placeholders (Plan B)...\n";
            $skills = $this->generatePlaceholderSkills($characterName);
        }

        return $skills;
    }

    private function scrapeSkillsFromPage(string $characterName): array
    {
        if ($this->skillsPageCrawler === null) {
            try {
                // Fetch Skills page via API Parse
                $response = $this->getWithRetry('/api.php', [
                    'query' => [
                        'action' => 'parse',
                        'page' => 'Skills',
                        'prop' => 'text',
                        'format' => 'json'
                    ]
                ]);
                $json = json_decode((string) $response->getBody(), true);
                if (isset($json['parse']['text']['*'])) {
                    $this->skillsPageCrawler = new Crawler($json['parse']['text']['*']);
                }
            } catch (\Exception $e) {
                echo "Warning: Could not fetch Skills page. " . $e->getMessage() . "\n";
                return [];
            }
        }

        if (!$this->skillsPageCrawler)
            return [];

        $skills = [];
        $crawler = $this->skillsPageCrawler;
        $headerNode = null;

        // Find H2 with character name 
        $crawler->filter('h2')->each(function (Crawler $node) use ($characterName, &$headerNode) {
            $text = $node->text();
            // Loose match to handle [edit] or spaces
            if (stripos($text, $characterName) !== false) {
                $headerNode = $node->getNode(0);
            }
        });

        if (!$headerNode) {
            return [];
        }

        // Look for the next siblings until we find a TABLE or another H2
        $currentNode = $headerNode->nextSibling;
        $steps = 0;
        while ($currentNode && $steps < 20) { // Limit steps to avoid infinite loops
            $nodeName = strtolower($currentNode->nodeName);

            if ($nodeName === 'h2') {
                break; // Next character section
            }

            if ($nodeName === 'table') {
                $tableCrawler = new Crawler($currentNode);

                // Parse rows (skip header row)
                $tableCrawler->filter('tr')->each(function (Crawler $row, $i) use (&$skills) {
                    if ($i === 0)
                        return; // Header

                    $cells = $row->filter('td');

                    if ($cells->count() >= 3) {
                        // Detected Shift: 
                        // Col 0: Checkbox
                        // Col 1: Icon
                        // Col 2: Name
                        // Col 3: Desc
                        // Col 4: AP
                        // Col 5: Additional Info / Prereq

                        $iconUrl = null;
                        // Check Index 1 for Icon (img)
                        if ($cells->count() > 1) {
                            $iconCell = $cells->eq(1);
                            $iconImg = $iconCell->filter('img');
                            if ($iconImg->count() > 0) {
                                $iconUrl = $iconImg->attr('data-src') ?? $iconImg->attr('src');
                            } else {
                                // Try finding inside 'a' tag
                                $anyImg = $iconCell->filterXPath('.//img');
                                if ($anyImg->count() > 0) {
                                    $iconUrl = $anyImg->attr('data-src') ?? $anyImg->attr('src');
                                }
                            }
                        }

                        if ($iconUrl) {
                            $iconUrl = preg_replace('/\/revision\/latest.*/', '', $iconUrl);
                        }

                        // Name at Index 2
                        $name = ($cells->count() > 2) ? trim($cells->eq(2)->text()) : '';

                        // Desc at Index 3
                        $descriptionHtml = ($cells->count() > 3) ? $cells->eq(3)->html() : '';
                        $description = trim(strip_tags($descriptionHtml, '<img><b><i><strong><em><br><p>'));

                        // Cost at Index 4
                        $cost = "";
                        if ($cells->count() > 4) {
                            $cost = trim($cells->eq(4)->text());
                        }

                        // Additional Info (Prerequisite / Notes) at Index 5
                        $additionalInfo = "";
                        if ($cells->count() > 5) {
                            $additionalInfoHtml = $cells->eq(5)->html();
                            // Allow images and basic formatting
                            $additionalInfo = trim(strip_tags($additionalInfoHtml, '<img><b><i><strong><em><br><p>'));
                        }

                        if (!empty($name)) { // relaxed check
                            $description = preg_replace('/\s+/', ' ', $description);

                            // Heuristics
                            $type = "Active";
                            if (stripos($description, 'passive') !== false || stripos($name, 'passive') !== false) {
                                $type = "Passive";
                            }

                            $damage = 0;
                            if (preg_match('/(\d+)\s*damage/i', strip_tags($description), $matches)) {
                                $damage = (int) $matches[1];
                            }

                            $skills[] = new SkillDTO(
                                name: $name,
                                description: $description,
                                iconPath: $iconUrl,
                                damage: $damage,
                                cost: $cost ?: "0 AP",
                                type: $type,
                                additionalInfo: $additionalInfo
                            );
                        }
                    }
                });

                if (!empty($skills))
                    break;
            }
            $currentNode = $currentNode->nextSibling;
            $steps++;
        }

        return $skills;
    }

    private function generatePlaceholderSkills(string $characterName): array
    {
        $skills = [];
        $skills[] = new SkillDTO(
            name: "$characterName Strike",
            description: "A signature attack by $characterName.",
            damage: 50,
            cost: "2 AP",
            type: "Active"
        );
        return $skills;
    }

    /**
     * Scrape weapons from the Weapons wiki page
     * @return WeaponDTO[]
     */
    public function getWeapons(): array
    {
        $weapons = [];

        try {
            $response = $this->getWithRetry('/api.php', [
                'query' => [
                    'action' => 'parse',
                    'page' => 'Weapons',
                    'prop' => 'text',
                    'format' => 'json'
                ]
            ]);
            $json = json_decode((string) $response->getBody(), true);

            if (!isset($json['parse']['text']['*'])) {
                return [];
            }

            // Character sections mapping
            $characterSections = [
                'Gustave/Verso' => ['Gustave', 'Verso'],
                'Lune' => ['Lune'],
                'Maelle' => ['Maelle'],
                'Sciel' => ['Sciel'],
                'Monoco' => ['Monoco']
            ];

            // Load the HTML into DOMDocument for XPath support
            $dom = new \DOMDocument();
            @$dom->loadHTML($json['parse']['text']['*'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $xpath = new \DOMXPath($dom);

            // Find each H3 and get the following UL
            $h3Nodes = $xpath->query('//h3');

            foreach ($h3Nodes as $h3) {
                $headerText = $h3->textContent;

                foreach ($characterSections as $sectionName => $characters) {
                    if (stripos($headerText, $sectionName) !== false) {
                        echo "   [Scraper] Found weapons section for $sectionName\n";

                        // Use XPath to find the FIRST following sibling TABLE element (wiki uses tables, not UL)
                        $tableNodes = $xpath->query('following-sibling::table[1]', $h3);

                        if ($tableNodes->length > 0) {
                            $table = $tableNodes->item(0);
                            // Find all rows in the table
                            $rows = $xpath->query('.//tbody/tr', $table);

                            echo "   [Scraper] Found " . $rows->length . " rows in table\n";

                            foreach ($rows as $row) {
                                // Get all td cells in this row
                                $cells = $xpath->query('.//td', $row);

                                if ($cells->length < 4)
                                    continue; // Skip header or malformed rows

                                // Column mapping (based on wiki structure):
                                // 0: Icon, 1: Name, 2: Power, 3: Element, 4: Vit, 5: Def, 6: Agi, 7: Luck


                                // Get Weapon Icon (cell 0)
                                $weaponIconUrl = null;
                                if ($cells->length > 0) {
                                    $iconCell = $cells->item(0);
                                    $iconImg = $xpath->query('.//img', $iconCell);
                                    if ($iconImg->length > 0) {
                                        $imgNode = $iconImg->item(0);
                                        $weaponIconUrl = $imgNode->getAttribute('data-src');
                                        if (!$weaponIconUrl) {
                                            $weaponIconUrl = $imgNode->getAttribute('src');
                                        }

                                        // Handle lazy loading data-src or data: placeholders
                                        if (strpos($weaponIconUrl, 'data:') === 0) {
                                            $weaponIconUrl = $imgNode->getAttribute('data-src');
                                        }

                                        if ($weaponIconUrl && strpos($weaponIconUrl, '//') === 0) {
                                            $weaponIconUrl = 'https:' . $weaponIconUrl;
                                        }

                                        // REMOVE SCALING PARAMETERS FOR HIGH QUALITY
                                        // Example: .../revision/latest/scale-to-width-down/12?cb=...
                                        // Becomes: .../revision/latest?cb=...
                                        $weaponIconUrl = preg_replace('/\/scale-to-width-down\/\d+/', '', $weaponIconUrl);
                                    }
                                }

                                // Get weapon name from cell 1 (the link)
                                $nameCell = $cells->item(1);
                                $nameLink = $xpath->query('.//a', $nameCell);
                                if ($nameLink->length === 0)
                                    continue;

                                $weaponName = trim($nameLink->item(0)->textContent);
                                $href = $nameLink->item(0)->getAttribute('href');

                                if (empty($weaponName) || stripos($weaponName, 'Edit') !== false)
                                    continue;

                                // Get Power (cell 2) - remove commas from numbers
                                $power = null;
                                if ($cells->length > 2) {
                                    $powerText = trim($cells->item(2)->textContent);
                                    $power = (int) str_replace([',', '.'], '', $powerText);
                                    if ($power === 0)
                                        $power = null;
                                }

                                // Get Element (cell 3) - text and icon
                                $element = null;
                                $elementIconUrl = null;
                                if ($cells->length > 3) {
                                    $elemCell = $cells->item(3);
                                    $element = trim($elemCell->textContent);

                                    // Try to get element icon (skip data: URIs which are placeholders)
                                    $elemImg = $xpath->query('.//img', $elemCell);
                                    if ($elemImg->length > 0) {
                                        $srcCandidate = $elemImg->item(0)->getAttribute('src');
                                        // Skip base64 data URIs (lazy loading placeholders)
                                        if (strpos($srcCandidate, 'data:') !== 0) {
                                            $elementIconUrl = $srcCandidate;
                                            if (strpos($elementIconUrl, '//') === 0) {
                                                $elementIconUrl = 'https:' . $elementIconUrl;
                                            }
                                        }
                                    }
                                }

                                // Get Scaling (cells 4-7: Vit, Def, Agi, Luck)
                                $scaling = [];
                                $scalingNames = ['Vit', 'Def', 'Agi', 'Luck'];
                                for ($i = 0; $i < 4; $i++) {
                                    $cellIndex = 4 + $i;
                                    if ($cells->length > $cellIndex) {
                                        $grade = trim($cells->item($cellIndex)->textContent);
                                        $scaling[$scalingNames[$i]] = !empty($grade) ? $grade : null;
                                    }
                                }

                                // Clean up href
                                $href = str_replace(['\"', "\'"], '', $href);
                                $wikiUrl = self::BASE_URL . $href;

                                // Prepare elements array
                                $elements = [];
                                if ($element) {
                                    $elements[] = [
                                        'name' => $element,
                                        'icon' => $elementIconUrl
                                    ];
                                }

                                $weapons[] = new WeaponDTO(
                                    name: $weaponName,
                                    description: null,
                                    imageUrl: $weaponIconUrl,
                                    usableBy: $characters,
                                    attack: $power,
                                    elements: $elements,
                                    scaling: $scaling
                                );
                            }
                        } else {
                            echo "   [Scraper] No TABLE found after H3 for $sectionName\n";
                        }
                        break; // Move to next H3
                    }
                }
            }

        } catch (\Exception $e) {
            echo "Error scraping weapons: " . $e->getMessage() . "\n";
        }

        echo "   [Scraper] Found " . count($weapons) . " weapons total\n";
        return $weapons;
    }

    private function getWeaponImage(string $weaponName): ?string
    {
        try {
            sleep(1); // Polite delay
            $response = $this->getWithRetry('/api.php', [
                'query' => [
                    'action' => 'query',
                    'titles' => $weaponName,
                    'prop' => 'pageimages',
                    'piprop' => 'original',
                    'format' => 'json'
                ]
            ]);
            $json = json_decode((string) $response->getBody(), true);

            if (isset($json['query']['pages'])) {
                foreach ($json['query']['pages'] as $page) {
                    if (isset($page['original']['source'])) {
                        return $page['original']['source'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail, return null
        }

        return null;
    }
}

