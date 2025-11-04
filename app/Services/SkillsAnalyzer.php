<?php

namespace App\Services;

class SkillsAnalyzer
{
    /**
     * Base de données complète des compétences par secteur d'activité
     */
    private $skillsDatabase = [
        // ==================== COMPÉTENCES TECHNIQUES ====================

        // BTP & Construction
        'btp_construction' => [
            // Métiers du gros œuvre
            'maçonnerie', 'béton armé', 'coffrage', 'ferraillage', 'grue', 'nacelle', 'échafaudage',
            'terrassement', 'voirie', 'assainissement', 'canalisation', 'fondations',

            // Second œuvre
            'plomberie', 'électricité', 'chauffage', 'climatisation', 'ventilation', 'sanitaire',
            'carrelage', 'peinture', 'plâtrerie', 'cloisons', 'isolation', 'étanchéité',
            'menuiserie', 'charpente', 'couverture', 'zinguerie', 'parquet', 'faïence',

            // Équipements et machines
            'pelleteuse', 'bulldozer', 'compacteur', 'bétonnière', 'malaxeur', 'scie circulaire',
            'perforateur', 'meuleuse', 'niveau laser', 'théodolite', 'station totale',

            // Normes et réglementations
            'rt 2012', 'rt 2020', 'nf dtu', 'eurocodes', 'caces', 'sst', 'prap',
            'qualibat', 'rge', 'qualibat', 'label bbc', 'hqe'
        ],

        // Industrie & Manufacturing
        'industrie_manufacturing' => [
            // Production
            'usinage', 'tournage', 'fraisage', 'perçage', 'rectification', 'électroérosion',
            'soudage', 'soudure arc', 'soudure tig', 'soudure mig', 'brasage', 'oxycoupage',
            'forge', 'emboutissage', 'estampage', 'découpe laser', 'pliage', 'cintrage',
            'injection plastique', 'extrusion', 'soufflage', 'thermoformage',

            // Machines-outils
            'tour cnc', 'fraiseuse cnc', 'centre usinage', 'rectifieuse', 'presse hydraulique',
            'robot industriel', 'ligne assemblage', 'convoyeur', 'automate programmable',

            // Contrôle qualité
            'métrologie', 'contrôle dimensionnel', 'rugosimètre', 'projecteur de profil',
            'machine à mesurer tridimensionnelle', 'contrôle non destructif', 'ressuage',
            'magnétoscopie', 'radiographie', 'ultrasons',

            // Normes industrielles
            'iso 9001', 'iso 14001', 'ohsas 18001', 'iso 45001', 'iatf 16949', 'as9100',
            '5s', 'lean manufacturing', 'six sigma', 'tpm', 'smed', 'kaizen', 'gemba'
        ],

        // Automobile & Transport
        'automobile_transport' => [
            // Mécanique auto
            'diagnostic automobile', 'réparation moteur', 'transmission', 'embrayage', 'freinage',
            'suspension', 'direction', 'climatisation auto', 'injection', 'allumage',
            'électronique embarquée', 'multiplexage', 'can bus', 'obd', 'scanner diagnostic',

            // Carrosserie
            'peinture automobile', 'débosselage', 'redressage', 'soudure carrosserie',
            'masticage', 'ponçage', 'cabine peinture', 'marouflage', 'lustrage',

            // Transport routier
            'permis c', 'permis ce', 'permis d', 'fimo', 'fco', 'adr', 'chronotachygraphe',
            'bâchage', 'arrimage', 'hayon élévateur', 'grue auxiliaire',

            // Logistique
            'cariste', 'magasinier', 'préparateur commandes', 'réceptionnaire', 'expeditionnaire',
            'chariot élévateur', 'transpalette', 'gerbeur', 'picking', 'cross-docking'
        ],

        // Santé & Médical
        'sante_medical' => [
            // Soins infirmiers
            'soins infirmiers', 'perfusion', 'injection', 'pansement', 'prélèvement sanguin',
            'électrocardiogramme', 'scope', 'défibrillateur', 'oxygénothérapie', 'aspiration',
            'sondage', 'dialyse', 'chimiothérapie', 'radiothérapie',

            // Médecine
            'diagnostic médical', 'anamnèse', 'examen clinique', 'prescription', 'thérapeutique',
            'urgences', 'réanimation', 'anesthésie', 'chirurgie', 'endoscopie',

            // Paramédical
            'kinésithérapie', 'ergothérapie', 'orthophonie', 'psychomotricité', 'ostéopathie',
            'podologie', 'orthoptie', 'diététique', 'pharmacie', 'optique',

            // Équipements médicaux
            'échographe', 'scanner', 'irm', 'radiologie', 'mammographe', 'fluoroscopie',
            'pacemaker', 'respirateur', 'pousse-seringue', 'monitoring'
        ],

        // Agriculture & Agroalimentaire
        'agriculture_agroalimentaire' => [
            // Agriculture
            'tracteur', 'moissonneuse', 'pulvérisateur', 'épandeur', 'semoir', 'charrue',
            'culture céréales', 'élevage bovin', 'élevage porcin', 'aviculture', 'maraîchage',
            'viticulture', 'arboriculture', 'horticulture', 'serres', 'irrigation',
            'phytosanitaire', 'fertilisation', 'rotation cultures', 'agriculture biologique',

            // Agroalimentaire
            'haccp', 'hygiène alimentaire', 'traçabilité', 'étiquetage', 'conditionnement',
            'pasteurisation', 'stérilisation', 'lyophilisation', 'surgélation', 'fumage',
            'fermentation', 'distillation', 'embouteillage', 'conserverie', 'boulangerie',
            'pâtisserie', 'charcuterie', 'fromagerie', 'brasserie'
        ],

        // Hôtellerie Restauration Tourisme
        'hotellerie_restauration' => [
            // Cuisine
            'cuisine traditionnelle', 'cuisine gastronomique', 'pâtisserie', 'boulangerie',
            'découpe viande', 'découpe poisson', 'sauces', 'cuisson basse température',
            'dressage', 'créativité culinaire', 'diététique', 'allergies alimentaires',

            // Service
            'service en salle', 'bar', 'sommellerie', 'œnologie', 'cocktails', 'café',
            'accueil clientèle', 'réservation', 'encaissement', 'room service',

            // Hôtellerie
            'réception hôtel', 'conciergerie', 'gouvernante', 'femme de chambre',
            'yield management', 'channel manager', 'pms', 'opera', 'amadeus',

            // Équipements
            'piano cuisine', 'salamandre', 'four mixte', 'plonge', 'lave-vaisselle',
            'cellule refroidissement', 'chambre froide', 'trancheuse', 'hachoir'
        ],

        // Commerce & Vente
        'commerce_vente' => [
            // Techniques de vente
            'prospection', 'qualification leads', 'argumentaire', 'objections', 'closing',
            'négociation commerciale', 'fidélisation', 'up-selling', 'cross-selling',
            'relation client', 'sav', 'crm', 'force de vente', 'management commercial',

            // Grande distribution
            'mise en rayon', 'facing', 'balisage prix', 'inventaire', 'réception marchandises',
            'gestion stock', 'caisse enregistreuse', 'encaissement', 'fidélité client',

            // E-commerce
            'boutique en ligne', 'marketplace', 'dropshipping', 'seo e-commerce',
            'google shopping', 'facebook ads', 'emailing', 'retargeting',

            // Outils commerciaux
            'salesforce', 'pipedrive', 'hubspot', 'zoho', 'sage crm', 'dolibarr'
        ],

        // Finance Comptabilité Banque
        'finance_comptabilite' => [
            // Comptabilité
            'comptabilité générale', 'comptabilité analytique', 'consolidation', 'ifrs',
            'liasse fiscale', 'tva', 'is', 'cvae', 'cfe', 'taxe professionnelle',
            'immobilisations', 'amortissements', 'provisions', 'cut-off', 'lettrage',

            // Finance
            'analyse financière', 'budget prévisionnel', 'business plan', 'cash flow',
            'valorisation entreprise', 'private equity', 'venture capital', 'ipo',
            'trading', 'analyse technique', 'dérivés', 'forex', 'commodities',

            // Banque assurance
            'crédit', 'financement', 'risque crédit', 'bâle iii', 'solvabilité ii',
            'compliance', 'lutte anti-blanchiment', 'kyc', 'mifid', 'priips',

            // Outils
            'sage', 'ciel', 'ebp', 'quadratus', 'silae', 'sap', 'oracle financials',
            'bloomberg', 'reuters', 'factset', 'murex', 'sophis'
        ],

        // Juridique & Administration
        'juridique_administration' => [
            // Droit
            'droit des affaires', 'droit social', 'droit fiscal', 'droit commercial',
            'droit pénal', 'droit civil', 'droit public', 'droit international',
            'propriété intellectuelle', 'droit immobilier', 'droit environnement',

            // Procédures
            'contentieux', 'médiation', 'arbitrage', 'négociation juridique',
            'rédaction contrats', 'due diligence', 'compliance', 'rgpd',
            'veille juridique', 'conseil juridique',

            // Administration
            'fonction publique', 'marchés publics', 'code marchés', 'dématérialisation',
            'service public', 'usagers', 'état civil', 'urbanisme', 'environnement'
        ],

        // Éducation & Formation
        'education_formation' => [
            // Pédagogie
            'pédagogie', 'andragogie', 'didactique', 'évaluation', 'différenciation',
            'classe inversée', 'pédagogie active', 'montessori', 'freinet', 'steiner',
            'troubles apprentissage', 'dys', 'handicap', 'inclusion', 'adaptation',

            // Outils éducatifs
            'tableau interactif', 'tablette éducative', 'logiciels éducatifs',
            'e-learning', 'mooc', 'classe virtuelle', 'moodle', 'blackboard',

            // Spécialités
            'petite enfance', 'primaire', 'secondaire', 'supérieur', 'formation adultes',
            'alphabétisation', 'fle', 'mathématiques', 'sciences', 'littérature',
            'langues vivantes', 'eps', 'arts plastiques', 'musique'
        ],

        // Communication Marketing
        'communication_marketing' => [
            // Communication
            'stratégie communication', 'plan communication', 'relations presse',
            'communiqué presse', 'dossier presse', 'relations publiques',
            'événementiel', 'salons', 'conférences', 'community management',

            // Marketing digital
            'seo', 'sea', 'social media', 'content marketing', 'inbound marketing',
            'email marketing', 'marketing automation', 'lead nurturing',
            'google ads', 'facebook ads', 'linkedin ads', 'instagram', 'tiktok',

            // Création
            'identité visuelle', 'charte graphique', 'logo', 'branding',
            'photoshop', 'illustrator', 'indesign', 'after effects', 'premiere',
            'webdesign', 'ux ui design', 'wireframe', 'mockup', 'prototype',

            // Outils marketing
            'google analytics', 'google tag manager', 'mailchimp', 'sendinblue',
            'hubspot', 'marketo', 'salesforce marketing', 'hootsuite', 'buffer'
        ],

        // Sécurité & Défense
        'securite_defense' => [
            // Sécurité privée
            'agent sécurité', 'surveillance', 'gardiennage', 'protection rapprochée',
            'ssiap', 'incendie', 'évacuation', 'secours personnes', 'filtrage',
            'vidéosurveillance', 'télésurveillance', 'contrôle accès', 'ronde',

            // Sécurité informatique
            'cybersécurité', 'pentest', 'audit sécurité', 'iso 27001', 'rgpd',
            'firewall', 'antivirus', 'chiffrement', 'vpn', 'pki', 'siem',
            'incident response', 'forensic', 'threat hunting',

            // Forces de l'ordre
            'police', 'gendarmerie', 'douanes', 'surveillance territoire',
            'enquête', 'procédure pénale', 'code route', 'maintien ordre',

            // Défense
            'armée terre', 'marine nationale', 'armée air', 'renseignement',
            'logistique militaire', 'génie militaire', 'transmissions'
        ],

        // IT & Digital (détaillé)
        'informatique_digital' => [
            // Développement
            'php', 'javascript', 'python', 'java', 'c#', 'c++', 'go', 'rust', 'kotlin',
            'swift', 'react', 'vue', 'angular', 'node.js', 'laravel', 'symfony',
            'spring', 'django', 'flask', 'asp.net', 'ruby on rails',

            // Base de données
            'mysql', 'postgresql', 'mongodb', 'redis', 'elasticsearch', 'oracle',
            'sql server', 'cassandra', 'neo4j', 'firebase', 'dynamodb',

            // Infrastructure
            'linux', 'windows server', 'vmware', 'hyper-v', 'docker', 'kubernetes',
            'ansible', 'terraform', 'jenkins', 'gitlab ci', 'aws', 'azure', 'gcp',

            // Sécurité IT
            'ethical hacking', 'penetration testing', 'vulnerability assessment',
            'owasp', 'sans', 'cissp', 'ceh', 'oscp', 'kali linux',

            // Data & IA
            'big data', 'data science', 'machine learning', 'deep learning',
            'tensorflow', 'pytorch', 'scikit-learn', 'pandas', 'numpy',
            'tableau', 'power bi', 'qlik', 'spark', 'hadoop', 'kafka'
        ],

        // ==================== COMPÉTENCES TRANSVERSALES ====================

        'soft_skills' => [
            // Leadership
            'leadership', 'management', 'encadrement', 'direction équipe', 'coaching',
            'motivation équipe', 'fédération', 'vision stratégique', 'prise décision',
            'délégation', 'mentoring', 'développement talents',

            // Communication
            'communication orale', 'communication écrite', 'écoute active', 'empathie',
            'négociation', 'persuasion', 'présentation public', 'relations interpersonnelles',
            'diplomatie', 'médiation', 'gestion conflits', 'assertivité',

            // Organisation
            'gestion temps', 'priorisation', 'planification', 'organisation travail',
            'multitâche', 'rigueur', 'méthode', 'process improvement', 'efficacité',
            'gestion stress', 'résistance pression',

            // Adaptation
            'adaptabilité', 'flexibilité', 'agilité', 'réactivité', 'innovation',
            'créativité', 'curiosité', 'ouverture esprit', 'apprentissage continu',
            'résilience', 'persévérance',

            // Collaboration
            'travail équipe', 'collaboration', 'coopération', 'esprit équipe',
            'bienveillance', 'respect diversité', 'intelligence collective',
            'networking', 'relations clients', 'service client',

            // Résolution problèmes
            'analyse', 'synthèse', 'esprit critique', 'logique', 'créativité',
            'résolution problèmes', 'prise initiative', 'proactivité', 'autonomie',
            'sens responsabilité', 'intégrité', 'éthique professionnelle'
        ],

        // Langues
        'langues' => [
            'français', 'anglais', 'espagnol', 'allemand', 'italien', 'portugais',
            'chinois mandarin', 'japonais', 'coréen', 'arabe', 'russe', 'hindi',
            'néerlandais', 'suédois', 'norvégien', 'danois', 'finlandais',
            'polonais', 'tchèque', 'hongrois', 'grec', 'turc', 'hébreu',
            'niveau débutant', 'niveau intermédiaire', 'niveau avancé',
            'bilingue', 'langue maternelle', 'courant', 'professionnel',
            'toeic', 'toefl', 'ielts', 'bulats', 'bright', 'linguaskill'
        ],

        // Certifications professionnelles
        'certifications' => [
            // Management & Qualité
            'pmp', 'prince2', 'scrum master', 'product owner', 'itil', 'cobit',
            'lean six sigma', 'iso 9001', 'iso 14001', 'iso 45001', 'iso 27001',

            // IT
            'aws certified', 'azure certified', 'google cloud certified',
            'cisco ccna', 'cisco ccnp', 'microsoft certified', 'vmware vcp',
            'red hat certified', 'oracle certified', 'salesforce certified',

            // Finance
            'cfa', 'frm', 'cpa', 'acca', 'expertise comptable', 'audit cac',

            // RH
            'certification coaching', 'certification formation', 'gpec',

            // Sécurité
            'cissp', 'cism', 'cisa', 'ceh', 'oscp', 'sans giac',

            // Sectoriels
            'caces', 'aipr', 'habilitation électrique', 'ssiap', 'psc1',
            'permis cariste', 'permis nacelle', 'port epi'
        ]
    ];

    /**
     * Patterns pour extraire les compétences du texte
     */
    private $skillPatterns = [
        // Compétences explicites
        '/(?:compétences?|skills?|maîtrise|expertise|connaissance)[\s\n]*:?\s*([^\.]{10,200})/i',
        '/(?:technologies?|outils?|logiciels?)[\s\n]*:?\s*([^\.]{10,150})/i',
        '/(?:expérience|experience)\s+(?:avec|en|de|in|with)\s+([^,\.]{3,50})/i',

        // Certifications et formations
        '/(?:certifié|certified|certification)\s+([^,\.]{3,50})/i',
        '/(?:diplôme|degree)\s+(?:en\s+|in\s+)?([^,\.]{3,50})/i',
        '/(?:formation|training)\s+(?:en\s+|in\s+)?([^,\.]{3,50})/i',

        // Missions et responsabilités
        '/(?:responsable|responsible)\s+(?:de\s+|for\s+)?([^,\.]{10,80})/i',
        '/(?:gestion|management|manage)\s+(?:de\s+|des\s+|of\s+)?([^,\.]{10,80})/i',
        '/(?:pilotage|coordination|développement)\s+(?:de\s+|des\s+)?([^,\.]{10,80})/i',
    ];

    /**
     * Extrait intelligemment les compétences d'un texte
     */
    public function extractSkills($text)
    {
        $extractedSkills = [];
        $text = strtolower($this->cleanText($text));

        // Initialiser toutes les catégories
        foreach (array_keys($this->skillsDatabase) as $category) {
            $extractedSkills[$category] = [];
        }

        // Ajouter une catégorie pour les missions
        $extractedSkills['missions'] = [];

        // 1. Recherche directe dans la base de compétences
        foreach ($this->skillsDatabase as $category => $skills) {
            foreach ($skills as $skill) {
                if ($this->findSkillInText($skill, $text)) {
                    $extractedSkills[$category][] = $skill;
                }
            }
        }

        // 2. Extraction par patterns pour les compétences contextuelles
        foreach ($this->skillPatterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $skills = $this->parseSkillsList($match);
                    $extractedSkills = $this->categorizeExtractedSkills($skills, $extractedSkills);
                }
            }
        }

        // 3. Extraction des missions spécifiques
        $extractedSkills['missions'] = $this->extractMissions($text);

        // Nettoyer et dédupliquer
        foreach ($extractedSkills as $category => $skills) {
            $extractedSkills[$category] = array_unique($skills);
        }

        return $extractedSkills;
    }

    /**
     * Calcule le score de correspondance basé sur les compétences réelles
     */
    public function calculateSkillsMatch($jobSkills, $resumeSkills)
    {
        $scores = [
            'skills_technique_score' => 0,  // 50 points
            'soft_skills_score' => 0,       // 20 points
            'missions_score' => 0,          // 15 points
            'certifications_score' => 0,    // 10 points
            'langues_score' => 0,           // 5 points
            'matched_skills' => [],
            'missing_critical_skills' => [],
            'sector_detected' => $this->detectJobSector($jobSkills)
        ];

        // 1. Score compétences techniques (50 points) - Le plus important
        $techScore = $this->calculateTechnicalSkillsScore($jobSkills, $resumeSkills);
        $scores['skills_technique_score'] = $techScore['score'] * 50;
        $scores['matched_skills']['technical'] = $techScore['matched'];
        $scores['missing_critical_skills']['technical'] = $techScore['missing'];

        // 2. Score soft skills (20 points)
        $softScore = $this->calculateCategoryMatch(
            $jobSkills['soft_skills'] ?? [],
            $resumeSkills['soft_skills'] ?? []
        );
        $scores['soft_skills_score'] = $softScore['score'] * 20;
        $scores['matched_skills']['soft_skills'] = $softScore['matched'];

        // 3. Score missions (15 points)
        $missionScore = $this->calculateMissionMatch(
            $jobSkills['missions'] ?? [],
            $resumeSkills['missions'] ?? []
        );
        $scores['missions_score'] = $missionScore * 15;

        // 4. Score certifications (10 points)
        $certScore = $this->calculateCategoryMatch(
            $jobSkills['certifications'] ?? [],
            $resumeSkills['certifications'] ?? []
        );
        $scores['certifications_score'] = $certScore['score'] * 10;

        // 5. Score langues (5 points)
        $langScore = $this->calculateCategoryMatch(
            $jobSkills['langues'] ?? [],
            $resumeSkills['langues'] ?? []
        );
        $scores['langues_score'] = $langScore['score'] * 5;

        return $scores;
    }

    /**
     * Calcule spécifiquement le score des compétences techniques
     * (combine toutes les catégories techniques selon le secteur détecté)
     */
    private function calculateTechnicalSkillsScore($jobSkills, $resumeSkills)
    {
        $sector = $this->detectJobSector($jobSkills);

        // Catégories techniques selon le secteur
        $technicalCategories = $this->getTechnicalCategoriesForSector($sector);

        $allJobTechSkills = [];
        $allResumeTechSkills = [];

        // Rassembler toutes les compétences techniques
        foreach ($technicalCategories as $category) {
            $allJobTechSkills = array_merge($allJobTechSkills, $jobSkills[$category] ?? []);
            $allResumeTechSkills = array_merge($allResumeTechSkills, $resumeSkills[$category] ?? []);
        }

        return $this->calculateCategoryMatch($allJobTechSkills, $allResumeTechSkills);
    }

    /**
     * Détecte le secteur d'activité à partir des compétences demandées
     */
    private function detectJobSector($jobSkills)
    {
        $sectorScores = [];

        foreach ($this->skillsDatabase as $category => $skills) {
            if ($category === 'soft_skills' || $category === 'langues' || $category === 'certifications') {
                continue; // Ignorer les catégories non-sectorielles
            }

            $matchCount = 0;
            $jobSkillsInCategory = $jobSkills[$category] ?? [];

            if (!empty($jobSkillsInCategory)) {
                $matchCount = count($jobSkillsInCategory);
                $sectorScores[$category] = $matchCount;
            }
        }

        if (empty($sectorScores)) {
            return 'general';
        }

        return array_key_first(array_slice(arsort($sectorScores) ? $sectorScores : $sectorScores, 0, 1, true));
    }

    /**
     * Retourne les catégories techniques pour un secteur donné
     */
    private function getTechnicalCategoriesForSector($sector)
    {
        $sectorMapping = [
            'btp_construction' => ['btp_construction'],
            'industrie_manufacturing' => ['industrie_manufacturing'],
            'automobile_transport' => ['automobile_transport'],
            'sante_medical' => ['sante_medical'],
            'agriculture_agroalimentaire' => ['agriculture_agroalimentaire'],
            'hotellerie_restauration' => ['hotellerie_restauration'],
            'commerce_vente' => ['commerce_vente'],
            'finance_comptabilite' => ['finance_comptabilite'],
            'juridique_administration' => ['juridique_administration'],
            'education_formation' => ['education_formation'],
            'communication_marketing' => ['communication_marketing'],
            'securite_defense' => ['securite_defense'],
            'informatique_digital' => ['informatique_digital']
        ];

        return $sectorMapping[$sector] ?? ['informatique_digital']; // Par défaut IT
    }

    /**
     * Nettoie le texte pour l'analyse
     */
    private function cleanText($text)
    {
        // Supprimer les caractères spéciaux mais garder la ponctuation importante
        $text = preg_replace('/[^\p{L}\p{N}\s\-\.\,\:\;\(\)]/u', ' ', $text);
        // Normaliser les espaces
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Vérifie si une compétence est présente dans le texte
     */
    private function findSkillInText($skill, $text)
    {
        $skill = strtolower($skill);

        // Recherche exacte avec délimiteurs de mots
        if (preg_match('/\b' . preg_quote($skill, '/') . '\b/', $text)) {
            return true;
        }

        // Recherche avec variations courantes
        $variations = $this->getSkillVariations($skill);
        foreach ($variations as $variation) {
            if (preg_match('/\b' . preg_quote($variation, '/') . '\b/', $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse une liste de compétences séparées
     */
    private function parseSkillsList($skillsText)
    {
        $skills = [];
        // Séparer par virgules, points-virgules, tirets, etc.
        $parts = preg_split('/[,;•\-\n\|]/', $skillsText);

        foreach ($parts as $part) {
            $skill = trim($part);
            if (strlen($skill) > 2 && strlen($skill) < 50) {
                $skills[] = $skill;
            }
        }

        return $skills;
    }

    /**
     * Catégorise automatiquement les compétences extraites
     */
    private function categorizeExtractedSkills($skills, $extractedSkills)
    {
        foreach ($skills as $skill) {
            $skill = strtolower(trim($skill));
            $categorized = false;

            // Rechercher dans chaque catégorie de la base
            foreach ($this->skillsDatabase as $category => $knownSkills) {
                foreach ($knownSkills as $knownSkill) {
                    if ($this->isSimilarSkill($skill, $knownSkill)) {
                        $extractedSkills[$category][] = $knownSkill;
                        $categorized = true;
                        break 2;
                    }
                }
            }

            // Si pas trouvé, essayer de deviner la catégorie
            if (!$categorized) {
                $category = $this->guessSkillCategory($skill);
                if ($category && isset($extractedSkills[$category])) {
                    $extractedSkills[$category][] = $skill;
                }
            }
        }

        return $extractedSkills;
    }

    /**
     * Extrait les missions du texte
     */
    private function extractMissions($text)
    {
        $missions = [];

        $missionPatterns = [
            '/(?:missions?|responsabilités|tasks?|duties)[\s\n]*:?\s*([^\.]{20,150})/i',
            '/(?:responsable|responsible)\s+(?:de\s+|for\s+)?([^,\.]{15,100})/i',
            '/(?:gestion|management|manage|managing)\s+(?:de\s+|des\s+|of\s+)?([^,\.]{15,100})/i',
            '/(?:pilotage|coordination|animation|supervision)\s+(?:de\s+|des\s+)?([^,\.]{15,100})/i',
            '/(?:développement|development|création|creation)\s+(?:de\s+|of\s+)?([^,\.]{15,100})/i',
        ];

        foreach ($missionPatterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $mission = trim($match);
                    if (strlen($mission) > 15 && strlen($mission) < 150) {
                        $missions[] = $mission;
                    }
                }
            }
        }

        return array_unique($missions);
    }

    /**
     * Calcule le score de correspondance pour une catégorie
     */
    private function calculateCategoryMatch($jobSkills, $resumeSkills)
    {
        if (empty($jobSkills)) {
            return ['score' => 1.0, 'matched' => [], 'missing' => []];
        }

        $matched = [];
        $missing = [];

        foreach ($jobSkills as $jobSkill) {
            $found = false;
            foreach ($resumeSkills as $resumeSkill) {
                if ($this->isSimilarSkill($jobSkill, $resumeSkill)) {
                    $matched[] = $jobSkill;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $missing[] = $jobSkill;
            }
        }

        $score = count($matched) / count($jobSkills);

        return [
            'score' => $score,
            'matched' => $matched,
            'missing' => $missing
        ];
    }

    /**
     * Calcule le score de correspondance des missions
     */
    private function calculateMissionMatch($jobMissions, $resumeMissions)
    {
        if (empty($jobMissions)) {
            return 1.0;
        }

        $matches = 0;
        foreach ($jobMissions as $jobMission) {
            foreach ($resumeMissions as $resumeMission) {
                if ($this->areMissionsSimilar($jobMission, $resumeMission)) {
                    $matches++;
                    break;
                }
            }
        }

        return $matches / count($jobMissions);
    }

    /**
     * Vérifie si deux compétences sont similaires
     */
    private function isSimilarSkill($skill1, $skill2)
    {
        $skill1 = strtolower(trim($skill1));
        $skill2 = strtolower(trim($skill2));

        // Correspondance exacte
        if ($skill1 === $skill2) {
            return true;
        }

        // L'une contient l'autre (pour les acronymes et variations)
        if (strlen($skill1) > 3 && strlen($skill2) > 3) {
            if (strpos($skill1, $skill2) !== false || strpos($skill2, $skill1) !== false) {
                return true;
            }
        }

        // Distance de Levenshtein pour les fautes de frappe
        if (levenshtein($skill1, $skill2) <= 2 && min(strlen($skill1), strlen($skill2)) > 4) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si deux missions sont similaires
     */
    private function areMissionsSimilar($mission1, $mission2)
    {
        $words1 = $this->extractImportantWords($mission1);
        $words2 = $this->extractImportantWords($mission2);

        if (empty($words1) || empty($words2)) {
            return false;
        }

        $common = array_intersect($words1, $words2);
        $totalWords = max(count($words1), count($words2));

        return (count($common) / $totalWords) >= 0.3; // 30% de mots en commun
    }

    /**
     * Extrait les mots importants d'une phrase
     */
    private function extractImportantWords($text)
    {
        $stopWords = [
            'de', 'la', 'le', 'les', 'un', 'une', 'des', 'du', 'et', 'ou', 'pour',
            'avec', 'sans', 'dans', 'sur', 'sous', 'entre', 'vers', 'par', 'selon',
            'the', 'and', 'or', 'for', 'with', 'in', 'on', 'at', 'to', 'from'
        ];

        $words = preg_split('/\s+/', strtolower($text));
        $important = [];

        foreach ($words as $word) {
            $word = trim($word, '.,;:!?()[]');
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                $important[] = $word;
            }
        }

        return array_unique($important);
    }

    /**
     * Devine la catégorie d'une compétence
     */
    private function guessSkillCategory($skill)
    {
        // Mots-clés pour deviner la catégorie
        $categoryKeywords = [
            'informatique_digital' => ['web', 'software', 'programming', 'code', 'data', 'cloud', 'api'],
            'btp_construction' => ['bâtiment', 'construction', 'chantier', 'béton', 'maçonnerie'],
            'commerce_vente' => ['vente', 'commercial', 'client', 'négociation', 'prospection'],
            'finance_comptabilite' => ['comptable', 'finance', 'budget', 'audit', 'fiscalité'],
            'soft_skills' => ['communication', 'management', 'leadership', 'organisation', 'équipe']
        ];

        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($skill, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'soft_skills'; // Par défaut
    }

    /**
     * Génère des variations d'une compétence
     */
    private function getSkillVariations($skill)
    {
        $variations = [$skill];

        // Variations communes par secteur
        $commonVariations = [
            // IT
            'javascript' => ['js', 'ecmascript', 'javascript'],
            'python' => ['py'],
            'react' => ['reactjs', 'react.js'],
            'php' => ['php7', 'php8'],

            // BTP
            'maçonnerie' => ['maçon', 'maçonnage'],
            'électricité' => ['électricien', 'électrique'],
            'plomberie' => ['plombier'],

            // Commercial
            'négociation' => ['négocier', 'négociateur'],
            'prospection' => ['prospecter', 'prospecteur'],

            // Management
            'management' => ['manager', 'gestion', 'manage'],
            'leadership' => ['leader', 'lead'],
        ];

        if (isset($commonVariations[$skill])) {
            $variations = array_merge($variations, $commonVariations[$skill]);
        }

        return array_unique($variations);
    }
}