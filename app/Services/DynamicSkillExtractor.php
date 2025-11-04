<?php

namespace App\Services;

class DynamicSkillExtractor
{
    private $contextPatterns = [
        // Compétences techniques explicites
        'technical_explicit' => [
            '/maîtrise\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/compétences?\s+(?:en\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/connaissance\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/utilisation\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/expertise\s+(?:en\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/expérience\s+(?:avec|en|de)\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',

            // Anglais
            '/experience\s+(?:with|in)\s+([a-z\s\-\.]+)/i',
            '/knowledge\s+(?:of|in)\s+([a-z\s\-\.]+)/i',
            '/proficient\s+(?:in|with)\s+([a-z\s\-\.]+)/i',
            '/skilled\s+(?:in|with)\s+([a-z\s\-\.]+)/i',
            '/expertise\s+(?:in|with)\s+([a-z\s\-\.]+)/i'
        ],

        // Formations et certifications
        'education' => [
            '/diplôme\s+(?:en\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/formation\s+(?:en\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/certification\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/certifié\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',

            // Anglais
            '/degree\s+(?:in\s+)?([a-z\s\-\.]+)/i',
            '/certified\s+(?:in\s+)?([a-z\s\-\.]+)/i',
            '/certificate\s+(?:in\s+)?([a-z\s\-\.]+)/i'
        ],

        // Tâches et responsabilités
        'responsibilities' => [
            '/responsable\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/gestion\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/pilotage\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/coordination\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/animation\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/encadrement\s+(?:de\s+)?([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',

            // Anglais
            '/responsible\s+for\s+([a-z\s\-\.]+)/i',
            '/manage\s+([a-z\s\-\.]+)/i',
            '/lead\s+([a-z\s\-\.]+)/i',
            '/coordinate\s+([a-z\s\-\.]+)/i'
        ],

        // Outils et logiciels
        'tools' => [
            '/(?:sous|avec|sur)\s+([A-Z][a-zA-Z0-9\s\-\.]{2,20})/i',
            '/logiciel\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/outil\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',
            '/plateforme\s+([a-záàâäçéèêëïîôöùûüÿ\s\-\.]+)/i',

            // Anglais
            '/using\s+([a-z\s\-\.]+)/i',
            '/software\s+([a-z\s\-\.]+)/i',
            '/platform\s+([a-z\s\-\.]+)/i'
        ]
    ];

    /**
     * Extrait dynamiquement les compétences importantes de la fiche de poste
     */
    public function extractSkillsFromJobPosting($text)
    {
        $extractedSkills = [];

        foreach ($this->contextPatterns as $category => $patterns) {
            foreach ($patterns as $pattern) {
                preg_match_all($pattern, $text, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $skill) {
                        $cleanSkill = $this->cleanSkill($skill);
                        if ($this->isValidSkill($cleanSkill)) {
                            $extractedSkills[$category][] = $cleanSkill;
                        }
                    }
                }
            }
        }

        // Déduplication et nettoyage
        foreach ($extractedSkills as $category => $skills) {
            $extractedSkills[$category] = array_unique($skills);
        }

        return $extractedSkills;
    }

    /**
     * Extrait les mots-clés importants avec analyse de fréquence contextuelle
     */
    public function extractImportantTerms($text, $limit = 30)
    {
        // Normalisation
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Tokenisation
        $words = preg_split('/\s+/', $text);

        // Stop words étendus
        $stopWords = $this->getStopWords();

        // Filtrage
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        // Analyse contextuelle : donner plus de poids aux mots près des mots-clés importants
        $contextWords = ['expérience', 'maîtrise', 'compétence', 'connaissance', 'expertise',
                        'responsable', 'gestion', 'formation', 'diplôme', 'certified', 'experience'];

        $weightedWords = [];
        $wordPositions = array_flip($words);

        foreach ($words as $index => $word) {
            $weight = 1;

            // Bonus si le mot est près d'un mot de contexte
            foreach ($contextWords as $contextWord) {
                if (isset($wordPositions[$contextWord])) {
                    $distance = abs($index - $wordPositions[$contextWord]);
                    if ($distance <= 3) {
                        $weight += (4 - $distance) * 0.5;
                    }
                }
            }

            $weightedWords[$word] = ($weightedWords[$word] ?? 0) + $weight;
        }

        // Tri par poids décroissant
        arsort($weightedWords);

        return array_slice(array_keys($weightedWords), 0, $limit);
    }

    /**
     * Détecte les synonymes et variantes d'un terme
     */
    public function findSynonyms($term)
    {
        $synonymMap = [
            // Formations
            'bac' => ['baccalauréat', 'bac+0', 'niveau bac'],
            'licence' => ['bac+3', 'bachelor', 'niveau licence'],
            'master' => ['bac+5', 'mastère', 'niveau master'],
            'doctorat' => ['phd', 'thèse', 'bac+8'],

            // Expérience
            'expérience' => ['expérimenté', 'vécu', 'pratique', 'experience'],
            'compétence' => ['compétent', 'maîtrise', 'expertise', 'skill'],
            'connaissance' => ['connaître', 'savoir', 'knowledge'],

            // Management
            'manager' => ['management', 'gestion', 'encadrement', 'manage'],
            'responsable' => ['responsabilité', 'pilotage', 'coordination'],
            'équipe' => ['équipes', 'team', 'groupe'],

            // Secteurs
            'développeur' => ['dev', 'developer', 'programmeur', 'développement'],
            'commercial' => ['vendeur', 'business', 'sales', 'vente'],
            'comptable' => ['comptabilité', 'accounting', 'financier'],
            'infirmier' => ['infirmière', 'soins', 'nursing'],
            'enseignant' => ['enseignante', 'professeur', 'prof', 'teacher'],
            'ingénieur' => ['ingénierie', 'engineer', 'engineering'],

            // Général
            'projet' => ['projets', 'project', 'mission', 'réalisation'],
            'client' => ['clients', 'customer', 'clientèle'],
            'qualité' => ['quality', 'qualitatif', 'qse']
        ];

        $term = strtolower(trim($term));

        // Recherche directe
        if (isset($synonymMap[$term])) {
            return array_merge([$term], $synonymMap[$term]);
        }

        // Recherche inverse
        foreach ($synonymMap as $key => $synonyms) {
            if (in_array($term, $synonyms)) {
                return array_merge([$key], $synonyms);
            }
        }

        return [$term];
    }

    /**
     * Calcule un score TF-IDF simplifiée entre deux textes (sur 30 points)
     */
    public function calculateTFIDFScore($jobText, $resumeText)
    {
        $jobTerms = $this->extractImportantTerms($jobText, 20);

        if (empty($jobTerms)) {
            return 0;
        }

        // Préparer le texte du CV pour la recherche
        $resumeWords = array_count_values(
            array_filter(
                preg_split('/\s+/', strtolower($resumeText)),
                function($word) { return strlen($word) > 2; }
            )
        );

        $score = 0;
        $totalWeight = 0;

        foreach ($jobTerms as $index => $term) {
            // Poids décroissant selon l'importance du terme dans la fiche
            $termWeight = max(0.1, (20 - $index) / 20);
            $totalWeight += $termWeight;

            // Rechercher le terme et ses synonymes dans le CV
            $synonyms = $this->findSynonyms($term);

            foreach ($synonyms as $synonym) {
                $synonym = strtolower($synonym);

                // Recherche exacte
                if (isset($resumeWords[$synonym])) {
                    $frequency = $resumeWords[$synonym];
                    // Bonus pour fréquence multiple (plafonné)
                    $frequencyBonus = min(1.5, 1 + ($frequency - 1) * 0.1);
                    $score += $termWeight * $frequencyBonus;
                    break;
                }

                // Recherche de sous-chaîne pour mots composés
                foreach ($resumeWords as $resumeWord => $freq) {
                    if (strlen($synonym) > 4 &&
                        (strpos($resumeWord, $synonym) !== false || strpos($synonym, $resumeWord) !== false)) {
                        $score += $termWeight * 0.7; // Score réduit pour correspondance partielle
                        break 2;
                    }
                }
            }
        }

        return $totalWeight > 0 ? min(30, ($score / $totalWeight) * 30) : 0;
    }

    private function cleanSkill($skill)
    {
        $skill = trim($skill);
        $skill = preg_replace('/\s+/', ' ', $skill);

        // Retirer les mots de liaison à la fin
        $skill = preg_replace('/\s+(et|ou|de|du|des|le|la|les)$/i', '', $skill);

        return $skill;
    }

    private function isValidSkill($skill)
    {
        $skill = trim($skill);

        // Trop court ou trop long
        if (strlen($skill) < 3 || strlen($skill) > 50) {
            return false;
        }

        // Mots vides
        $invalidTerms = [
            'très', 'bien', 'bon', 'bonne', 'excellent', 'forte', 'good', 'great', 'excellent',
            'niveau', 'ans', 'année', 'années', 'year', 'years', 'minimum', 'maximum'
        ];

        return !in_array(strtolower($skill), $invalidTerms);
    }

    private function getStopWords()
    {
        return [
            // Français
            'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'ou', 'mais', 'donc', 'car',
            'pour', 'dans', 'sur', 'avec', 'sans', 'sous', 'entre', 'vers', 'par', 'selon',
            'être', 'avoir', 'faire', 'dire', 'aller', 'voir', 'savoir', 'pouvoir', 'vouloir',
            'venir', 'falloir', 'devoir', 'croire', 'trouver', 'donner', 'prendre', 'parler',
            'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'combien',
            'ce', 'cette', 'ces', 'cet', 'celui', 'celle', 'ceux', 'celles', 'son', 'sa', 'ses',
            'notre', 'nos', 'votre', 'vos', 'leur', 'leurs', 'mon', 'ma', 'mes', 'ton', 'ta', 'tes',
            'nous', 'vous', 'ils', 'elles', 'elle', 'lui', 'eux', 'tout', 'tous', 'toute', 'toutes',
            'autre', 'autres', 'même', 'mêmes', 'plus', 'moins', 'très', 'bien', 'mal', 'mieux',
            'aussi', 'encore', 'déjà', 'jamais', 'toujours', 'souvent', 'parfois', 'quelquefois',
            'alors', 'ainsi', 'donc', 'puis', 'ensuite', 'enfin', 'après', 'avant', 'pendant',
            'depuis', 'jusqu', 'vers', 'contre', 'malgré', 'pendant', 'durant', 'parmi',

            // Anglais
            'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from',
            'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having',
            'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must',
            'can', 'shall', 'about', 'into', 'through', 'during', 'before', 'after', 'above',
            'below', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further', 'then',
            'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both',
            'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not',
            'only', 'own', 'same', 'so', 'than', 'too', 'very', 'can', 'just', 'should', 'now'
        ];
    }
}