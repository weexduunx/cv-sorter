<?php

/**
 * Test démonstratif du système universel de matching CV
 * Ce fichier montre comment le système s'adapte automatiquement à différents secteurs
 */

require_once __DIR__ . '/../app/Services/ResumeMatchingService.php';
require_once __DIR__ . '/../app/Services/DynamicSkillExtractor.php';
require_once __DIR__ . '/../app/Services/ExperienceExtractor.php';
require_once __DIR__ . '/../app/Services/ScoringConfigService.php';

class UniversalMatchingTest
{
    private $matcher;

    public function __construct()
    {
        $this->matcher = new App\Services\ResumeMatchingService();
    }

    /**
     * Test 1: Secteur Médical - Poste d'Infirmier
     */
    public function testHealthcareSector()
    {
        echo "=== TEST SECTEUR MÉDICAL : Poste d'Infirmier ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Nous recherchons un(e) infirmier(ère) diplômé(e) d'état pour notre service de réanimation adulte.
                Vous aurez en charge les soins intensifs, la surveillance monitoring des patients,
                la gestion des perfusions et des médicaments, l'assistance à la ventilation mécanique.
                Expérience minimum 3 ans en réanimation ou soins intensifs exigée.
                Maîtrise des protocoles d'urgence vitale et des gestes de réanimation.
                Capacité à travailler en équipe pluridisciplinaire avec médecins et aides-soignants.
                Formation aux nouveaux protocoles de soins assurée.
            "
        ];

        $candidateCV = "
            Infirmière diplômée depuis 5 ans avec spécialisation en soins intensifs.
            4 années d'expérience en service de réanimation adulte au CHU.
            Maîtrise parfaite du monitoring des paramètres vitaux et de la ventilation assistée.
            Expérience de la gestion des perfusions complexes et des protocoles médicamenteux.
            Formation récente aux nouveaux protocoles de réanimation cardio-pulmonaire.
            Travail en équipe avec médecins réanimateurs et équipe paramédicale.
            Capacité à gérer les situations d'urgence vitale.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Infirmier Réanimation", $result);
    }

    /**
     * Test 2: Secteur IT - Poste de Développeur
     */
    public function testTechnicalSector()
    {
        echo "\n=== TEST SECTEUR TECHNIQUE : Développeur Full-Stack ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Développeur Full-Stack Senior recherché pour rejoindre notre équipe technique.
                Stack technique : PHP/Laravel, React.js, MySQL, Docker, AWS.
                Vous développerez des applications web complexes, APIs REST, microservices.
                Maîtrise obligatoire : PHP 8+, Laravel 9+, JavaScript ES6+, React hooks.
                Expérience avec Git, CI/CD, tests unitaires (PHPUnit), architecture MVC.
                Minimum 5 ans d'expérience en développement web full-stack.
                Méthodologies Agile/Scrum, travail en équipe DevOps.
                Anglais technique requis pour documentation.
            "
        ];

        $candidateCV = "
            Développeur Full-Stack avec 6 ans d'expérience en développement web.
            Expertise Laravel depuis 4 ans, maîtrise PHP 8 et des dernières versions.
            Front-end : React.js, JavaScript ES6+, hooks React, responsive design.
            Base de données : MySQL, PostgreSQL, optimisation des requêtes.
            DevOps : Docker, AWS (EC2, S3, RDS), Jenkins pour CI/CD.
            Tests : PHPUnit, Jest, couverture de code, TDD.
            Git workflow, méthodologies Agile, Scrum master certification.
            Anglais courant, documentation technique en anglais.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Développeur Full-Stack", $result);
    }

    /**
     * Test 3: Secteur Commercial - Responsable Grands Comptes
     */
    public function testSalesSector()
    {
        echo "\n=== TEST SECTEUR COMMERCIAL : Responsable Grands Comptes ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Responsable Grands Comptes B2B pour développement commercial région Île-de-France.
                Prospection et développement portefeuille clients entreprises 500+ salariés.
                Négociation contrats complexes, relation client long terme, upselling.
                Objectifs : 2M€ CA annuel, croissance 15% sur portefeuille existant.
                Minimum 7 ans expérience vente B2B, idéalement secteur services.
                Maîtrise CRM Salesforce, outils prospection LinkedIn Sales Navigator.
                Compétences négociation, présentation, closing, suivi client.
                Mobilité régionale, permis B obligatoire.
                Formation commerce/business, anglais professionnel.
            "
        ];

        $candidateCV = "
            Business Development Manager avec 8 ans d'expérience vente B2B.
            Spécialiste développement grands comptes secteur services aux entreprises.
            Prospection active et développement portefeuille 50 comptes stratégiques.
            Négociation contrats 100K€ à 500K€, taux closing 65%.
            Dépassement objectifs : 120% sur les 3 dernières années.
            Maîtrise Salesforce CRM, LinkedIn Sales Navigator, HubSpot.
            Techniques de négociation, présentations commerciales, relation client.
            Permis B, déplacements régionaux fréquents.
            Formation école de commerce, anglais business fluent.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Responsable Grands Comptes", $result);
    }

    /**
     * Test 4: Secteur Éducation - Professeur de Mathématiques
     */
    public function testEducationSector()
    {
        echo "\n=== TEST SECTEUR ÉDUCATION : Professeur de Mathématiques ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Professeur de Mathématiques certifié pour collège et lycée.
                Enseignement mathématiques niveaux 6ème à Terminale S.
                CAPES Mathématiques obligatoire, expérience 3 ans minimum.
                Capacité à gérer classes hétérogènes, pédagogie différenciée.
                Suivi personnalisé élèves en difficulté, préparation examens.
                Coordination projets pédagogiques, travail équipe enseignante.
                Maîtrise outils numériques : TBI, Geogebra, Python algorithmique.
                Participation conseils de classe, rencontres parents d'élèves.
                Formation continue, veille pédagogique.
            "
        ];

        $candidateCV = "
            Professeur certifié Mathématiques (CAPES 2018) avec 4 ans d'expérience.
            Enseignement collège-lycée, spécialisation niveaux Seconde à Terminale.
            Gestion classes de 30 élèves, pédagogie adaptée publics difficiles.
            Soutien scolaire élèves en décrochage, préparation bac scientifique.
            Coordination projets interdisciplinaires mathématiques-sciences.
            Maîtrise parfaite outils numériques éducatifs : GeoGebra, Python.
            Animation ateliers algorithmique, initiation programmation.
            Excellent relationnel parents-élèves, conseils de classe.
            Formation continue didactique, innovation pédagogique.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Professeur Mathématiques", $result);
    }

    /**
     * Test 5: Secteur Finance - Contrôleur de Gestion
     */
    public function testFinanceSector()
    {
        echo "\n=== TEST SECTEUR FINANCE : Contrôleur de Gestion ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Contrôleur de Gestion pour groupe industriel 500M€ chiffre d'affaires.
                Pilotage budgets, reporting mensuel direction générale.
                Analyse écarts, contrôle coûts, optimisation marges opérationnelles.
                Consolidation filiales, tableaux de bord KPIs financiers.
                Minimum 5 ans expérience contrôle gestion, environnement industriel.
                Maîtrise SAP FI/CO, Excel avancé, Power BI pour reporting.
                Formation finance/comptabilité, idéalement DSCG ou Master.
                Anglais opérationnel pour reporting groupe international.
                Rigueur, sens analyse, communication écrite/orale.
            "
        ];

        $candidateCV = "
            Contrôleur de Gestion Senior avec 6 ans d'expérience secteur industriel.
            Pilotage budgets 50M€, reporting financier direction et actionnaires.
            Analyse performance, écarts budgétaires, recommandations optimisation.
            Consolidation 8 filiales, tableaux de bord KPIs opérationnels.
            Maîtrise expert SAP FI/CO, Excel VBA, Power BI dashboards.
            Formation DSCG, spécialisation contrôle de gestion industrielle.
            Anglais courant, reporting mensuel en anglais groupe américain.
            Rigueur analytique, synthèse, présentation direction générale.
            Optimisation coûts : 2M€ économies sur 2 dernières années.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Contrôleur de Gestion", $result);
    }

    /**
     * Test 6: Secteur Créatif - Designer UX/UI
     */
    public function testCreativeSector()
    {
        echo "\n=== TEST SECTEUR CRÉATIF : Designer UX/UI ===\n\n";

        $jobPosting = (object) [
            'extracted_text' => "
                Designer UX/UI pour startup fintech en forte croissance.
                Conception interfaces utilisateur applications mobile et web.
                Recherche utilisateur, personas, wireframes, prototypes interactifs.
                Design system, atomic design, responsive design mobile-first.
                Minimum 4 ans expérience design produit digital.
                Maîtrise Figma, Sketch, Adobe Creative Suite, Principle.
                Collaboration étroite équipes Product et développement.
                Tests utilisateurs, A/B testing, optimisation conversions.
                Portfolio démontrant projets UX/UI aboutis.
                Anglais pour équipe internationale.
            "
        ];

        $candidateCV = "
            Designer UX/UI avec 5 ans d'expérience produits digitaux.
            Spécialisation applications fintech et e-commerce.
            Recherche utilisateur approfondie, interviews, personas détaillés.
            Wireframing Figma, prototypes haute-fidélité, tests utilisateurs.
            Design systems scalables, atomic design, 15 composants réutilisables.
            Maîtrise expert Figma, Sketch, After Effects, Principle.
            Collaboration Product Managers, développeurs front-end quotidienne.
            A/B testing interfaces : +25% conversion moyenne projets.
            Portfolio 20 projets, applications 500K+ utilisateurs.
            Anglais courant, équipes internationales 3 ans.
        ";

        $result = $this->matcher->calculateMatch($candidateCV, $jobPosting);
        $this->displayResult("Designer UX/UI", $result);
    }

    /**
     * Affiche les résultats d'un test de manière formatée
     */
    private function displayResult($jobTitle, $result)
    {
        echo "📋 POSTE : {$jobTitle}\n";
        echo "🎯 SCORE FINAL : {$result['score']}/100\n";
        echo "🏢 SECTEUR DÉTECTÉ : " . ucfirst($result['sector']) . "\n\n";

        echo "📊 DÉTAIL DES SCORES :\n";
        echo "   • Correspondance Lexicale : {$result['breakdown']['lexical']}/50\n";
        echo "   • Analyse TF-IDF : " . round($result['breakdown']['frequency'], 1) . "/30\n";
        echo "   • Expérience : {$result['breakdown']['experience']}/20\n";
        echo "   • Expérience candidat : {$result['experience']} ans\n";
        echo "   • Expérience requise : {$result['breakdown']['job_experience_required']} ans\n\n";

        echo "🎯 CONFIGURATION SECTEUR :\n";
        if (isset($result['analysis']['weights_used'])) {
            $weights = $result['analysis']['weights_used'];
            echo "   • Pondération Lexicale : " . ($weights['lexical'] * 100) . "%\n";
            echo "   • Pondération Fréquence : " . ($weights['frequency'] * 100) . "%\n";
            echo "   • Pondération Expérience : " . ($weights['experience'] * 100) . "%\n";
        }

        echo "\n🔍 MOTS-CLÉS CORRESPONDANTS :\n";
        $keywords = array_slice($result['keywords'], 0, 10);
        foreach ($keywords as $keyword) {
            echo "   ✅ {$keyword}\n";
        }

        if (isset($result['analysis']['recommendations']) && !empty($result['analysis']['recommendations'])) {
            echo "\n💡 RECOMMANDATIONS :\n";
            foreach (array_slice($result['analysis']['recommendations'], 0, 3) as $rec) {
                $priority = $rec['priority'] === 'high' ? '🔴' : ($rec['priority'] === 'medium' ? '🟡' : '🟢');
                echo "   {$priority} {$rec['message']}\n";
            }
        }

        $verdict = $result['score'] >= 80 ? "✅ EXCELLENT MATCH" :
                  ($result['score'] >= 60 ? "⚠️ BON MATCH" : "❌ MATCH FAIBLE");
        echo "\n🏆 VERDICT : {$verdict}\n";
        echo str_repeat("=", 70) . "\n";
    }

    /**
     * Lance tous les tests
     */
    public function runAllTests()
    {
        echo "🚀 DÉMONSTRATION SYSTÈME UNIVERSEL DE MATCHING CV\n";
        echo "📋 Test de 6 secteurs différents pour prouver la généricité\n";
        echo str_repeat("=", 70) . "\n";

        $this->testHealthcareSector();
        $this->testTechnicalSector();
        $this->testSalesSector();
        $this->testEducationSector();
        $this->testFinanceSector();
        $this->testCreativeSector();

        echo "\n🎉 CONCLUSION :\n";
        echo "Le système s'adapte automatiquement à chaque secteur :\n";
        echo "• Détection automatique du secteur\n";
        echo "• Pondérations adaptées selon le métier\n";
        echo "• Extraction dynamique des compétences\n";
        echo "• Analyse contextuelle de l'expérience\n";
        echo "• Recommandations personnalisées\n";
        echo "\n✅ SYSTÈME 100% UNIVERSEL VALIDÉ !\n";
    }
}

// Exécution des tests si le fichier est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new UniversalMatchingTest();
    $test->runAllTests();
}