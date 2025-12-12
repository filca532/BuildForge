<?php

namespace BuildForge\Scraping;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use BuildForge\DTO\CharacterDTO;
use BuildForge\DTO\SkillDTO;

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

    /**
     * @return CharacterDTO[]
     */
    public function getCharacters(): array
    {
        $characters = [];
        $targetNames = ['Gustave', 'Lune', 'Sciel', 'Maelle', 'Verso', 'Monoco'];

        // 1. Fetch Class Descriptions from the Main "Characters" Page Table
        // The user wants the specific descriptions from the table on /wiki/Characters
        $descriptionsMap = $this->getDescriptionsFromListing();

        foreach ($targetNames as $name) {
            try {
                // 2. Fetch Image via PageImages API (Keep high-res images)
                $imageUrl = null;
                $imgResponse = $this->client->get('/api.php', [
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

                $characters[] = new CharacterDTO(
                    name: $name,
                    imageUrl: $imageUrl,
                    description: $description
                );

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
            $res = $this->client->get('/api.php', [
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
                            // Normalize name match (sometimes "Gustave" vs "Gustave ")
                            // We know our target names are simple
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
                echo "Fetching Skills page...\n";
                // Using API for Skills page too? No, keep it simple for now as per "Characters table" request.
                // Reverting to direct fetch for skills or we can use Parse API as well.
                // Let's us Parse API for consistency.
                $response = $this->client->get('/api.php', [
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

        // Find H2 with character name (API parse usually returns H2 for section headers)
        $crawler->filter('h2')->each(function (Crawler $node) use ($characterName, &$headerNode) {
            if (stripos($node->text(), $characterName) !== false) {
                $headerNode = $node->getNode(0);
            }
        });

        if (!$headerNode) {
            return [];
        }

        // Iterate siblings
        $currentNode = $headerNode->nextSibling;
        while ($currentNode) {
            if ($currentNode->nodeName === 'h2') {
                break;
            }

            if ($currentNode->nodeName === 'ul') {
                $listCrawler = new Crawler($currentNode);
                $listCrawler->filter('li')->each(function (Crawler $li) use (&$skills) {
                    $text = $li->text();
                    $parts = explode(':', $text, 2);
                    $name = trim($parts[0]);
                    $desc = isset($parts[1]) ? trim($parts[1]) : "Description to be updated.";

                    if (!empty($name)) {
                        $skills[] = new SkillDTO(
                            name: $name,
                            description: $desc,
                            damage: rand(20, 150),
                            cost: rand(1, 5) . " AP",
                            type: (rand(0, 1) ? 'Active' : 'Passive')
                        );
                    }
                });
            }
            $currentNode = $currentNode->nextSibling;
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
        $skills[] = new SkillDTO(
            name: "Defensive Stance",
            description: "$characterName prepares for an incoming attack.",
            damage: 0,
            cost: "1 AP",
            type: "Passive"
        );
        $skills[] = new SkillDTO(
            name: "Ultimate Art",
            description: "A devastating move that defines $characterName's style.",
            damage: 150,
            cost: "4 AP",
            type: "Active"
        );
        return $skills;
    }
}
