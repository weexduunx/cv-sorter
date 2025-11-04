<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Services\SkillsAnalyzer;

class ResumeMatchingService
{
    private $skillsAnalyzer;

    public function __construct()
    {
        $this->skillsAnalyzer = new SkillsAnalyzer();
    }
    /**
     * Dictionnaire de synonymes pour améliorer la correspondance
     */
    private $synonyms = [
        'développeur' => ['dev', 'developer', 'programmeur', 'codeur'],
        'expérience' => ['expérimenté', 'pratique', 'vécu'],
        'compétence' => ['compétent', 'maîtrise', 'expertise', 'savoir'],
        'manager' => ['management', 'gestion', 'encadrement', 'direction'],
        'commercial' => ['vendeur', 'business', 'sales', 'vente'],
        'formation' => ['diplôme', 'étude', 'cursus', 'parcours'],
        'projet' => ['mission', 'réalisation', 'travail'],
    ];

    /**
     * Stop words multilingues
     */
    private $stopWords = [
        // Français
        'le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'mais', 'donc', 'car',
        'pour', 'dans', 'sur', 'avec', 'sans', 'sous', 'vers', 'chez', 'par',
        'être', 'avoir', 'faire', 'dire', 'pouvoir', 'devoir', 'aller', 'voir',
        'être', 'étant', 'été', 'ayant', 'eu', 'fait', 'faisant',
        'nous', 'vous', 'ils', 'elles', 'leur', 'leurs', 'son', 'sa', 'ses',
        'ce', 'cette', 'ces', 'cet', 'celui', 'celle', 'ceux', 'celles',
        'tout', 'tous', 'toute', 'toutes', 'autre', 'autres', 'même', 'mêmes',
        'quel', 'quelle', 'quels', 'quelles', 'qui', 'que', 'quoi', 'dont',
        'plus', 'moins', 'très', 'bien', 'aussi', 'encore', 'déjà',

        // Anglais
        'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with',
        'by', 'from', 'up', 'about', 'into', 'through', 'during', 'before',
        'after', 'above', 'below', 'between', 'under', 'again', 'further',
        'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how',
        'all', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such',
        'only', 'own', 'same', 'than', 'too', 'very', 'can', 'will', 'just',
        'should', 'now', 'is', 'are', 'was', 'were', 'been', 'being', 'have',
        'has', 'had', 'having', 'do', 'does', 'did', 'doing', 'would', 'could'
    ];

    public function extractTextFromFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        try {
            if ($extension === 'pdf') {
                return $this->extractFromPdf($filePath);
            } elseif (in_array($extension, ['doc', 'docx'])) {
                return $this->extractFromWord($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Erreur extraction texte: ' . $e->getMessage());
            return '';
        }

        return '';
    }

    private function extractFromPdf($filePath)
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile(storage_path('app/' . $filePath));
        $text = $pdf->getText();

        // Nettoyer et corriger l'encodage UTF-8
        return $this->cleanTextEncoding($text);
    }

    private function extractFromWord($filePath)
    {
        $phpWord = IOFactory::load(storage_path('app/' . $filePath));
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            $text .= $this->extractTextFromElements($section->getElements());
        }

        // Nettoyer et corriger l'encodage UTF-8
        return $this->cleanTextEncoding($text);
    }

    private function extractTextFromElements($elements)
    {
        $text = '';
        foreach ($elements as $element) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText() . ' ';
            } elseif (method_exists($element, 'getElements')) {
                $text .= $this->extractTextFromElements($element->getElements());
            }
        }
        return $text;
    }

    /**
     * Calcul principal basé sur l'analyse des compétences réelles
     *
     * @param string $resumeText Texte extrait du CV
     * @param object $jobPosting Objet JobPosting avec extracted_text
     * @return array [score, keywords, experience, ...]
     */
    public function calculateMatch($resumeText, $jobPosting)
    {
        $resumeText = trim($resumeText);
        $jobText = trim($jobPosting->extracted_text ?: $jobPosting->description);

        if (empty($resumeText) || empty($jobText)) {
            return [
                'score' => 0,
                'keywords' => [],
                'experience' => 0,
                'detected_sector' => 'unknown',
                'score_breakdown' => [
                    'skills_techniques' => 0,
                    'soft_skills' => 0,
                    'missions' => 0,
                    'certifications' => 0,
                    'langues' => 0,
                    'experience' => 0
                ],
                'candidate_experience' => 0,
                'required_experience' => 0,
                'analysis_data' => []
            ];
        }

        try {
            // 1. Extraction des compétences de la fiche de poste
            $jobSkills = $this->skillsAnalyzer->extractSkills($jobText);

            // 2. Extraction des compétences du CV
            $resumeSkills = $this->skillsAnalyzer->extractSkills($resumeText);

            // 3. Calcul du score basé sur les compétences (100 points total)
            $skillsMatch = $this->skillsAnalyzer->calculateSkillsMatch($jobSkills, $resumeSkills);

            // 4. Score d'expérience (intégré séparément)
            $jobExperience = $this->extractExperience($jobText);
            $resumeExperience = $this->extractExperience($resumeText);
            $experienceScore = $this->calculateExperienceScore($jobText, $resumeText);

            // 5. Score final = Compétences (85%) + Expérience (15%)
            $skillsTotal = $skillsMatch['skills_technique_score'] +
                          $skillsMatch['soft_skills_score'] +
                          $skillsMatch['missions_score'] +
                          $skillsMatch['certifications_score'] +
                          $skillsMatch['langues_score'];

            $finalScore = ($skillsTotal * 0.85) + ($experienceScore['score'] * 0.15);

            // 6. Compilation des mots-clés matchés
            $allMatchedSkills = [];
            foreach ($skillsMatch['matched_skills'] as $category => $skills) {
                $allMatchedSkills = array_merge($allMatchedSkills, $skills);
            }

            return [
                'score' => min(round($finalScore, 2), 100),
                'keywords' => $allMatchedSkills,
                'experience' => $resumeExperience,
                'detected_sector' => $skillsMatch['sector_detected'],
                'score_breakdown' => [
                    'skills_techniques' => round($skillsMatch['skills_technique_score'], 2),
                    'soft_skills' => round($skillsMatch['soft_skills_score'], 2),
                    'missions' => round($skillsMatch['missions_score'], 2),
                    'certifications' => round($skillsMatch['certifications_score'], 2),
                    'langues' => round($skillsMatch['langues_score'], 2),
                    'experience' => round($experienceScore['score'], 2)
                ],
                'candidate_experience' => $resumeExperience,
                'required_experience' => $jobExperience,
                'analysis_data' => [
                    'job_skills_found' => $this->countSkillsByCategory($jobSkills),
                    'resume_skills_found' => $this->countSkillsByCategory($resumeSkills),
                    'critical_missing_skills' => $skillsMatch['missing_critical_skills'],
                    'matching_algorithm' => 'skills_based_v2',
                    'sector_detected' => $skillsMatch['sector_detected'],
                    'processed_at' => now()->toISOString()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur analyse compétences: ' . $e->getMessage());

            // Fallback vers l'ancien système en cas d'erreur
            return $this->calculateFallbackMatch($resumeText, $jobPosting);
        }
    }

    /**
     * Correspondance lexicale : extrait les mots-clés de la fiche
     * et vérifie leur présence dans le CV
     *
     * @return array ['score' => float, 'keywords' => array]
     */
    private function calculateLexicalMatch($jobText, $resumeText)
    {
        // Extraire les mots-clés de la fiche de poste
        $jobKeywords = $this->extractSignificantKeywords($jobText, 30);

        // Extraire les mots-clés du CV
        $resumeKeywords = $this->extractSignificantKeywords($resumeText, 100);

        // Trouver les correspondances
        $matchedKeywords = [];
        $matchCount = 0;

        foreach ($jobKeywords as $jobKeyword) {
            // Vérification directe
            if (in_array($jobKeyword, $resumeKeywords)) {
                $matchedKeywords[] = $jobKeyword;
                $matchCount++;
                continue;
            }

            // Vérification avec synonymes
            $synonyms = $this->getSynonyms($jobKeyword);
            foreach ($synonyms as $synonym) {
                if (in_array($synonym, $resumeKeywords)) {
                    $matchedKeywords[] = $jobKeyword;
                    $matchCount++;
                    break;
                }
            }
        }

        $score = count($jobKeywords) > 0
            ? ($matchCount / count($jobKeywords)) * 50
            : 0;

        return [
            'score' => $score,
            'keywords' => $matchedKeywords
        ];
    }

    /**
     * Score de fréquence : analyse la densité des termes importants
     *
     * @return array ['score' => float, 'keywords' => array]
     */
    private function calculateFrequencyScore($jobText, $resumeText)
    {
        $jobFreq = $this->calculateTermFrequency($jobText);
        $resumeFreq = $this->calculateTermFrequency($resumeText);

        // Prendre les 20 termes les plus fréquents de la fiche
        $importantTerms = array_slice($jobFreq, 0, 20, true);

        $score = 0;
        $matchedTerms = [];

        foreach ($importantTerms as $term => $jobFrequency) {
            if (isset($resumeFreq[$term])) {
                // Normalisation : plus le terme apparaît, mieux c'est
                $weight = min(1.0, $resumeFreq[$term] / $jobFrequency);
                $score += $weight;
                $matchedTerms[] = $term;
            }
        }

        $normalizedScore = count($importantTerms) > 0
            ? ($score / count($importantTerms)) * 30
            : 0;

        return [
            'score' => $normalizedScore,
            'keywords' => $matchedTerms
        ];
    }

    /**
     * Score d'expérience professionnelle
     *
     * @return array ['score' => float, 'years' => int]
     */
    private function calculateExperienceScore($jobText, $resumeText)
    {
        $jobExperience = $this->extractExperience($jobText);
        $resumeExperience = $this->extractExperience($resumeText);

        $score = 0;

        if ($jobExperience > 0) {
            if ($resumeExperience >= $jobExperience) {
                $score = 20;
            } elseif ($resumeExperience > 0) {
                $score = ($resumeExperience / $jobExperience) * 20;
            }
        } else {
            // Pas d'expérience mentionnée dans la fiche = on donne un bonus
            $score = 15;
        }

        return [
            'score' => $score,
            'years' => $resumeExperience
        ];
    }

    /**
     * Extrait les mots-clés significatifs d'un texte
     *
     * @param string $text
     * @param int $limit Nombre de mots-clés à retourner
     * @return array
     */
    private function extractSignificantKeywords($text, $limit = 30)
    {
        // Normalisation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', strtolower($text));

        // Filtrage
        $words = array_filter($words, function($word) {
            return strlen($word) > 3 && !in_array($word, $this->stopWords);
        });

        // Comptage
        $wordFreq = array_count_values($words);
        arsort($wordFreq);

        return array_slice(array_keys($wordFreq), 0, $limit);
    }

    /**
     * Calcule la fréquence des termes
     *
     * @param string $text
     * @return array ['mot' => fréquence]
     */
    private function calculateTermFrequency($text)
    {
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', strtolower($text));

        $words = array_filter($words, function($word) {
            return strlen($word) > 3 && !in_array($word, $this->stopWords);
        });

        $frequency = array_count_values($words);
        arsort($frequency);

        return $frequency;
    }

    /**
     * Extrait les années d'expérience d'un texte
     *
     * @param string $text
     * @return int
     */
    private function extractExperience($text)
    {
        $patterns = [
            '/(\d+)\+?\s*(?:ans?|années?|years?)/i',
            '/(\d+)\s*à\s*(\d+)\s*(?:ans?|années?|years?)/i',
            '/expérience.*?(\d+)\s*(?:ans?|années?)/i',
            '/experience.*?(\d+)\s*years?/i',
            '/depuis\s*(\d+)\s*(?:ans?|années?)/i',
            '/plus\s*de\s*(\d+)\s*(?:ans?|années?)/i',
            '/over\s*(\d+)\s*years?/i',
        ];

        $maxExperience = 0;

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            if (!empty($matches[1])) {
                $values = array_map('intval', $matches[1]);
                $maxExperience = max($maxExperience, max($values));
            }
        }

        return $maxExperience;
    }

    /**
     * Récupère les synonymes d'un mot
     *
     * @param string $word
     * @return array
     */
    private function getSynonyms($word)
    {
        return $this->synonyms[$word] ?? [$word];
    }

    /**
     * Détection simple du secteur basée sur des mots-clés
     *
     * @param string $text
     * @return string
     */
    private function detectSimpleSector($text)
    {
        $sectors = [
            'technical' => ['développeur', 'dev', 'developer', 'programmeur', 'ingénieur', 'engineer', 'technique', 'technology', 'software', 'informatique', 'it'],
            'management' => ['directeur', 'manager', 'responsable', 'chef', 'senior', 'director', 'head', 'lead', 'principal', 'management', 'gestion'],
            'commercial' => ['commercial', 'vendeur', 'business', 'sales', 'vente', 'account', 'client'],
            'healthcare' => ['médecin', 'infirmier', 'soignant', 'santé', 'médical', 'doctor', 'nurse', 'medical', 'healthcare'],
            'education' => ['enseignant', 'professeur', 'formateur', 'éducation', 'teacher', 'professor', 'education', 'training'],
            'finance' => ['comptable', 'financier', 'audit', 'finance', 'accounting', 'financial', 'controller'],
            'creative' => ['designer', 'créatif', 'graphique', 'communication', 'marketing', 'artistic', 'creative', 'design']
        ];

        $sectorScores = [];

        foreach ($sectors as $sector => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                $count = substr_count($text, $keyword);
                $score += $count;

                // Bonus si le mot-clé apparaît en début de texte (titre probable)
                if (strpos(substr($text, 0, 100), $keyword) !== false) {
                    $score += 2;
                }
            }

            if ($score > 0) {
                $sectorScores[$sector] = $score;
            }
        }

        if (empty($sectorScores)) {
            return 'general';
        }

        return array_keys($sectorScores, max($sectorScores))[0];
    }

    /**
     * Nettoie et corrige l'encodage UTF-8 du texte extrait
     *
     * @param string $text
     * @return string
     */
    private function cleanTextEncoding($text)
    {
        if (empty($text)) {
            return '';
        }

        // Convertir vers UTF-8 si nécessaire
        if (!mb_check_encoding($text, 'UTF-8')) {
            // Essayer de détecter l'encodage
            $encoding = mb_detect_encoding($text, ['UTF-8', 'UTF-16', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $text = mb_convert_encoding($text, 'UTF-8', $encoding);
            } else {
                // Forcer la conversion depuis ISO-8859-1 si la détection échoue
                $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
            }
        }

        // Supprimer les caractères de contrôle problématiques
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Supprimer les caractères UTF-8 invalides
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Nettoyer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Compte les compétences par catégorie
     */
    private function countSkillsByCategory($skills)
    {
        $counts = [];
        foreach ($skills as $category => $skillList) {
            $counts[$category] = count($skillList);
        }
        return $counts;
    }

    /**
     * Système de fallback en cas d'erreur avec le nouveau système
     */
    private function calculateFallbackMatch($resumeText, $jobPosting)
    {
        $resumeText = strtolower(trim($resumeText));
        $jobText = strtolower(trim($jobPosting->extracted_text ?: $jobPosting->description));

        // Score simple basé sur mots-clés
        $lexicalScore = $this->calculateLexicalMatch($jobText, $resumeText);
        $experienceScore = $this->calculateExperienceScore($jobText, $resumeText);

        $totalScore = $lexicalScore['score'] + ($experienceScore['score'] * 0.4);

        return [
            'score' => min(round($totalScore, 2), 100),
            'keywords' => $lexicalScore['keywords'],
            'experience' => $experienceScore['years'],
            'detected_sector' => $this->detectSimpleSector($jobText),
            'score_breakdown' => [
                'skills_techniques' => round($lexicalScore['score'] * 0.8, 2),
                'soft_skills' => 0,
                'missions' => 0,
                'certifications' => 0,
                'langues' => 0,
                'experience' => round($experienceScore['score'], 2)
            ],
            'candidate_experience' => $experienceScore['years'],
            'required_experience' => $this->extractExperience($jobText),
            'analysis_data' => [
                'matching_algorithm' => 'fallback_simple',
                'error_occurred' => true,
                'processed_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Méthode publique pour tester l'extraction de compétences
     */
    public function analyzeSkillsOnly($text, $type = 'job')
    {
        try {
            $skills = $this->skillsAnalyzer->extractSkills($text);
            return [
                'success' => true,
                'skills' => $skills,
                'type' => $type,
                'total_skills' => $this->countSkillsByCategory($skills)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'skills' => [],
                'type' => $type
            ];
        }
    }
}