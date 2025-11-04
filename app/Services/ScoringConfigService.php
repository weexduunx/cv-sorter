<?php

namespace App\Services;

class ScoringConfigService
{
    /**
     * Configurations de pondération par secteur
     */
    private $sectorConfigs = [
        'default' => [
            'lexical' => 0.50,
            'frequency' => 0.30,
            'experience' => 0.20,
            'description' => 'Configuration équilibrée pour tous secteurs'
        ],

        'technical' => [
            'lexical' => 0.60,
            'frequency' => 0.25,
            'experience' => 0.15,
            'description' => 'Pour métiers techniques (IT, ingénierie, science)',
            'keywords' => ['développeur', 'ingénieur', 'programmeur', 'technique', 'technology', 'software', 'developer', 'engineer']
        ],

        'senior_management' => [
            'lexical' => 0.35,
            'frequency' => 0.25,
            'experience' => 0.40,
            'description' => 'Pour postes seniors et management',
            'keywords' => ['directeur', 'manager', 'responsable', 'chef', 'senior', 'director', 'head', 'lead', 'principal']
        ],

        'creative' => [
            'lexical' => 0.45,
            'frequency' => 0.40,
            'experience' => 0.15,
            'description' => 'Pour métiers créatifs et communication',
            'keywords' => ['designer', 'créatif', 'graphique', 'communication', 'marketing', 'artistic', 'creative', 'design']
        ],

        'healthcare' => [
            'lexical' => 0.55,
            'frequency' => 0.30,
            'experience' => 0.15,
            'description' => 'Pour métiers de la santé',
            'keywords' => ['médecin', 'infirmier', 'soignant', 'santé', 'médical', 'doctor', 'nurse', 'medical', 'healthcare']
        ],

        'education' => [
            'lexical' => 0.50,
            'frequency' => 0.35,
            'experience' => 0.15,
            'description' => 'Pour métiers de l\'éducation',
            'keywords' => ['enseignant', 'professeur', 'formateur', 'éducation', 'teacher', 'professor', 'education', 'training']
        ],

        'sales' => [
            'lexical' => 0.45,
            'frequency' => 0.30,
            'experience' => 0.25,
            'description' => 'Pour métiers commerciaux et vente',
            'keywords' => ['commercial', 'vente', 'vendeur', 'business', 'sales', 'account', 'client']
        ],

        'finance' => [
            'lexical' => 0.55,
            'frequency' => 0.25,
            'experience' => 0.20,
            'description' => 'Pour métiers financiers et comptables',
            'keywords' => ['comptable', 'financier', 'audit', 'finance', 'accounting', 'financial', 'controller']
        ],

        'legal' => [
            'lexical' => 0.60,
            'frequency' => 0.25,
            'experience' => 0.15,
            'description' => 'Pour métiers juridiques',
            'keywords' => ['avocat', 'juridique', 'droit', 'legal', 'lawyer', 'attorney', 'law']
        ],

        'operations' => [
            'lexical' => 0.40,
            'frequency' => 0.35,
            'experience' => 0.25,
            'description' => 'Pour métiers opérationnels et logistique',
            'keywords' => ['logistique', 'production', 'opérations', 'supply', 'operations', 'logistics', 'manufacturing']
        ],

        'hr' => [
            'lexical' => 0.45,
            'frequency' => 0.35,
            'experience' => 0.20,
            'description' => 'Pour métiers RH et recrutement',
            'keywords' => ['ressources humaines', 'recrutement', 'rh', 'human resources', 'recruitment', 'talent']
        ],

        'consulting' => [
            'lexical' => 0.50,
            'frequency' => 0.30,
            'experience' => 0.20,
            'description' => 'Pour métiers de conseil',
            'keywords' => ['consultant', 'conseil', 'advisory', 'consulting', 'strategy']
        ]
    ];

    /**
     * Détecte automatiquement le secteur d'une fiche de poste
     */
    public function detectSector($jobText)
    {
        $jobText = strtolower($jobText);
        $sectorScores = [];

        foreach ($this->sectorConfigs as $sector => $config) {
            if ($sector === 'default' || !isset($config['keywords'])) {
                continue;
            }

            $score = 0;
            foreach ($config['keywords'] as $keyword) {
                $keyword = strtolower($keyword);
                $count = substr_count($jobText, $keyword);
                $score += $count;

                // Bonus si le mot-clé apparaît dans le titre/début
                $firstPart = substr($jobText, 0, 200);
                if (strpos($firstPart, $keyword) !== false) {
                    $score += 2;
                }
            }

            if ($score > 0) {
                $sectorScores[$sector] = $score;
            }
        }

        // Retourner le secteur avec le score le plus élevé, ou 'default'
        if (empty($sectorScores)) {
            return 'default';
        }

        return array_keys($sectorScores, max($sectorScores))[0];
    }

    /**
     * Obtient la configuration de pondération pour un secteur
     */
    public function getScoringWeights($sector = null, $jobText = null)
    {
        // Auto-détection si pas de secteur spécifié
        if ($sector === null && $jobText !== null) {
            $sector = $this->detectSector($jobText);
        }

        $sector = $sector ?: 'default';

        if (!isset($this->sectorConfigs[$sector])) {
            $sector = 'default';
        }

        return $this->sectorConfigs[$sector];
    }

    /**
     * Obtient toutes les configurations disponibles
     */
    public function getAllConfigs()
    {
        return $this->sectorConfigs;
    }

    /**
     * Ajoute ou modifie une configuration de secteur
     */
    public function setSectorConfig($sector, $config)
    {
        // Validation des pondérations
        $totalWeight = ($config['lexical'] ?? 0) + ($config['frequency'] ?? 0) + ($config['experience'] ?? 0);

        if (abs($totalWeight - 1.0) > 0.01) {
            throw new \InvalidArgumentException('La somme des pondérations doit être égale à 1.0');
        }

        $this->sectorConfigs[$sector] = $config;
    }

    /**
     * Calcule le score final avec les pondérations du secteur
     */
    public function calculateWeightedScore($lexicalScore, $frequencyScore, $experienceScore, $sector = null, $jobText = null)
    {
        $weights = $this->getScoringWeights($sector, $jobText);

        // Les scores sont déjà sur leur échelle respective (50, 30, 20)
        // On applique juste les pondérations
        $totalScore = ($lexicalScore * $weights['lexical']) +
                     ($frequencyScore * $weights['frequency']) +
                     ($experienceScore * $weights['experience']);

        return min(round($totalScore, 2), 100);
    }

    /**
     * Obtient des recommandations d'amélioration basées sur le secteur
     */
    public function getSectorRecommendations($sector, $scores)
    {
        $config = $this->getScoringWeights($sector);
        $recommendations = [];

        // Analyser les points faibles
        $normalizedScores = [
            'lexical' => $scores['lexical'] / 50,
            'frequency' => $scores['frequency'] / 30,
            'experience' => $scores['experience'] / 20
        ];

        foreach ($normalizedScores as $component => $score) {
            if ($score < 0.6) { // Score faible
                $weight = $config[$component];
                $impact = $weight * $score;

                switch ($component) {
                    case 'lexical':
                        $recommendations[] = [
                            'type' => 'lexical',
                            'message' => 'Améliorer la correspondance des mots-clés techniques',
                            'priority' => $weight > 0.5 ? 'high' : 'medium',
                            'impact' => $impact
                        ];
                        break;

                    case 'frequency':
                        $recommendations[] = [
                            'type' => 'frequency',
                            'message' => 'Enrichir le vocabulaire métier et les termes spécialisés',
                            'priority' => $weight > 0.3 ? 'high' : 'medium',
                            'impact' => $impact
                        ];
                        break;

                    case 'experience':
                        $recommendations[] = [
                            'type' => 'experience',
                            'message' => 'L\'expérience professionnelle ne correspond pas aux exigences',
                            'priority' => $weight > 0.3 ? 'high' : 'low',
                            'impact' => $impact
                        ];
                        break;
                }
            }
        }

        // Trier par impact décroissant
        usort($recommendations, function($a, $b) {
            return $b['impact'] <=> $a['impact'];
        });

        return $recommendations;
    }

    /**
     * Génère un rapport d'analyse sectorielle
     */
    public function generateSectorAnalysisReport($jobText, $scores)
    {
        $detectedSector = $this->detectSector($jobText);
        $config = $this->getScoringWeights($detectedSector);
        $recommendations = $this->getSectorRecommendations($detectedSector, $scores);

        return [
            'detected_sector' => $detectedSector,
            'sector_description' => $config['description'],
            'weights_used' => [
                'lexical' => $config['lexical'],
                'frequency' => $config['frequency'],
                'experience' => $config['experience']
            ],
            'score_breakdown' => [
                'lexical' => [
                    'raw' => $scores['lexical'],
                    'weighted' => $scores['lexical'] * $config['lexical'],
                    'weight' => $config['lexical']
                ],
                'frequency' => [
                    'raw' => $scores['frequency'],
                    'weighted' => $scores['frequency'] * $config['frequency'],
                    'weight' => $config['frequency']
                ],
                'experience' => [
                    'raw' => $scores['experience'],
                    'weighted' => $scores['experience'] * $config['experience'],
                    'weight' => $config['experience']
                ]
            ],
            'recommendations' => $recommendations,
            'sector_keywords_found' => $this->findSectorKeywords($jobText, $detectedSector)
        ];
    }

    /**
     * Trouve les mots-clés sectoriels dans le texte
     */
    private function findSectorKeywords($text, $sector)
    {
        if (!isset($this->sectorConfigs[$sector]['keywords'])) {
            return [];
        }

        $text = strtolower($text);
        $found = [];

        foreach ($this->sectorConfigs[$sector]['keywords'] as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $found[] = $keyword;
            }
        }

        return $found;
    }
}