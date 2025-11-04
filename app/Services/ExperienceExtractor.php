<?php

namespace App\Services;

class ExperienceExtractor
{
    private $experiencePatterns = [
        // Français - Patterns directs
        '/(\d+)\+?\s*(ans?|années?)\s*(?:d[\'e]|de)\s*expérience/i',
        '/expérience\s*(?:de\s*)?(\d+)\+?\s*(ans?|années?)/i',
        '/(\d+)\+?\s*(ans?|années?)\s*(?:dans|en)\s*(?:le\s*)?(?:domaine|secteur|métier)/i',
        '/plus\s*de\s*(\d+)\s*(ans?|années?)\s*(?:d[\'e]|de)\s*expérience/i',
        '/minimum\s*(\d+)\s*(ans?|années?)\s*(?:d[\'e]|de)\s*expérience/i',
        '/au\s*moins\s*(\d+)\s*(ans?|années?)\s*(?:d[\'e]|de)\s*expérience/i',
        '/(\d+)\s*à\s*(\d+)\s*(ans?|années?)\s*(?:d[\'e]|de)\s*expérience/i',

        // Français - Patterns avec contexte métier
        '/(\d+)\+?\s*(ans?|années?)\s*comme\s*/i',
        '/(\d+)\+?\s*(ans?|années?)\s*en\s*tant\s*que\s*/i',
        '/depuis\s*(\d+)\s*(ans?|années?)/i',
        '/(\d+)\+?\s*(ans?|années?)\s*(?:de|en)\s*\w+/i',

        // Anglais - Patterns directs
        '/(\d+)\+?\s*years?\s*(?:of\s*)?experience/i',
        '/experience\s*(?:of\s*)?(\d+)\+?\s*years?/i',
        '/(\d+)\+?\s*years?\s*(?:in|working\s*in|working\s*as)/i',
        '/minimum\s*(\d+)\s*years?\s*(?:of\s*)?experience/i',
        '/at\s*least\s*(\d+)\s*years?\s*(?:of\s*)?experience/i',
        '/(\d+)\s*to\s*(\d+)\s*years?\s*(?:of\s*)?experience/i',
        '/over\s*(\d+)\s*years?\s*(?:of\s*)?experience/i',
        '/more\s*than\s*(\d+)\s*years?\s*(?:of\s*)?experience/i',

        // Anglais - Patterns avec contexte
        '/(\d+)\+?\s*years?\s*as\s*(?:a\s*|an\s*)?/i',
        '/(\d+)\+?\s*years?\s*working\s*(?:as\s*|in\s*|with)/i',
        '/for\s*(\d+)\s*years?/i',

        // Patterns spéciaux
        '/junior\s*\((\d+)\s*ans?\)/i',
        '/senior\s*\((\d+)\s*ans?\)/i',
        '/confirmé\s*\((\d+)\s*ans?\)/i',

        // Patterns de plage
        '/(\d+)\s*[-–]\s*(\d+)\s*(ans?|années?|years?)/i',
    ];

    private $seniorityLevels = [
        'junior' => ['junior', 'débutant', 'entry level', 'trainee', 'stagiaire'],
        'confirmed' => ['confirmé', 'expérimenté', 'confirmed', 'experienced'],
        'senior' => ['senior', 'expert', 'lead', 'principal', 'chef', 'responsable']
    ];

    /**
     * Extrait l'expérience requise d'une fiche de poste
     */
    public function extractRequiredExperience($text)
    {
        $experiences = [];

        foreach ($this->experiencePatterns as $pattern) {
            preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (isset($match[1])) {
                    $years = intval($match[1]);

                    // Pour les plages (ex: 3 à 5 ans)
                    if (isset($match[2]) && is_numeric($match[2])) {
                        $years = intval($match[2]); // Prendre le maximum de la plage
                    }

                    if ($years > 0 && $years <= 50) { // Validation
                        $experiences[] = $years;
                    }
                }
            }
        }

        // Analyser aussi les niveaux de séniorité
        $seniorityExperience = $this->extractSeniorityLevel($text);
        if ($seniorityExperience > 0) {
            $experiences[] = $seniorityExperience;
        }

        return !empty($experiences) ? max($experiences) : 0;
    }

    /**
     * Extrait l'expérience d'un CV
     */
    public function extractCandidateExperience($text)
    {
        $experiences = [];

        // Patterns plus permissifs pour les CV
        $cvPatterns = array_merge($this->experiencePatterns, [
            // Patterns CV spécifiques
            '/(\d{4})\s*[-–]\s*(\d{4})/i', // Années de travail
            '/(\d{4})\s*[-–]\s*(?:présent|maintenant|today|now)/i',
            '/(\d+)\s*mois\s*(?:d[\'e]|de)\s*expérience/i',
        ]);

        foreach ($cvPatterns as $pattern) {
            preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (isset($match[1])) {
                    // Cas des années (2019-2023)
                    if (strlen($match[1]) === 4 && isset($match[2])) {
                        $startYear = intval($match[1]);
                        $endYear = is_numeric($match[2]) ? intval($match[2]) : date('Y');
                        $years = $endYear - $startYear;

                        if ($years > 0 && $years <= 50) {
                            $experiences[] = $years;
                        }
                    } else {
                        // Cas des nombres d'années directs
                        $years = intval($match[1]);
                        if ($years > 0 && $years <= 50) {
                            $experiences[] = $years;
                        }
                    }
                }
            }
        }

        // Analyser les expériences par poste
        $positionExperience = $this->extractExperienceFromPositions($text);
        if ($positionExperience > 0) {
            $experiences[] = $positionExperience;
        }

        return !empty($experiences) ? max($experiences) : 0;
    }

    /**
     * Calcule le score d'expérience selon l'algorithme du README (sur 20 points)
     */
    public function calculateExperienceScore($requiredExperience, $candidateExperience)
    {
        if ($requiredExperience === 0) {
            // Si pas d'expérience requise, bonus par défaut
            return 15;
        }

        if ($candidateExperience === 0) {
            // Pas d'expérience mentionnée dans le CV
            return 0;
        }

        // Selon la grille du README (score sur 20 points)
        if ($candidateExperience >= $requiredExperience) {
            return 20; // Score maximum
        } elseif ($candidateExperience >= $requiredExperience * 0.8) {
            // 80-99% de l'expérience requise
            return 16 + (($candidateExperience / $requiredExperience - 0.8) * 20);
        } elseif ($candidateExperience >= $requiredExperience * 0.5) {
            // 50-79% de l'expérience requise
            return 8 + (($candidateExperience / $requiredExperience - 0.5) * 27);
        } else {
            // Moins de 50% de l'expérience requise
            return ($candidateExperience / $requiredExperience) * 16;
        }
    }

    /**
     * Extrait l'expérience basée sur les niveaux de séniorité
     */
    private function extractSeniorityLevel($text)
    {
        $text = strtolower($text);

        foreach ($this->seniorityLevels as $level => $terms) {
            foreach ($terms as $term) {
                if (strpos($text, $term) !== false) {
                    switch ($level) {
                        case 'junior':
                            return 2; // 0-2 ans
                        case 'confirmed':
                            return 5; // 3-5 ans
                        case 'senior':
                            return 8; // 5+ ans
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Analyse les postes occupés pour estimer l'expérience totale
     */
    private function extractExperienceFromPositions($text)
    {
        // Rechercher des patterns de postes avec dates
        $positionPatterns = [
            '/(\d{4})\s*[-–]\s*(\d{4})/i',
            '/(\d{4})\s*[-–]\s*(?:présent|maintenant|aujourd\'hui|today|now|current)/i',
            '/(?:depuis|from|since)\s*(\d{4})/i',
        ];

        $positions = [];

        foreach ($positionPatterns as $pattern) {
            preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (isset($match[1])) {
                    $startYear = intval($match[1]);
                    $endYear = isset($match[2]) && is_numeric($match[2])
                        ? intval($match[2])
                        : date('Y');

                    $duration = $endYear - $startYear;
                    if ($duration > 0 && $duration <= 50) {
                        $positions[] = $duration;
                    }
                }
            }
        }

        // Estimer l'expérience totale (pas forcément la somme si chevauchements)
        if (!empty($positions)) {
            // Prendre la plus longue expérience continue
            return max($positions);
        }

        return 0;
    }

    /**
     * Détecte des mentions d'expérience dans différents contextes
     */
    public function extractExperienceContexts($text)
    {
        $contexts = [];

        $contextPatterns = [
            'management' => '/(\d+)\s*(?:ans?|années?|years?)\s*(?:de\s*)?(?:management|gestion\s*d\'équipe|encadrement)/i',
            'project_management' => '/(\d+)\s*(?:ans?|années?|years?)\s*(?:de\s*)?(?:gestion\s*de\s*projets?|project\s*management)/i',
            'sales' => '/(\d+)\s*(?:ans?|années?|years?)\s*(?:de\s*)?(?:vente|commercial|sales)/i',
            'development' => '/(\d+)\s*(?:ans?|années?|years?)\s*(?:de\s*)?(?:développement|programming|coding)/i',
            'teaching' => '/(\d+)\s*(?:ans?|années?|years?)\s*(?:d\'|de\s*)?(?:enseignement|formation|teaching)/i',
        ];

        foreach ($contextPatterns as $context => $pattern) {
            preg_match_all($pattern, $text, $matches);
            if (!empty($matches[1])) {
                $contexts[$context] = max(array_map('intval', $matches[1]));
            }
        }

        return $contexts;
    }
}