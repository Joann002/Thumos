<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\GoalTask;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'utilisateur de test
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // === OBJECTIFS ===
        $goals = [
            [
                'title' => 'Apprendre Laravel Avancé',
                'description' => 'Maîtriser les concepts avancés de Laravel : queues, events, notifications, tests.',
                'category' => 'Développement',
                'target_date' => now()->addMonths(3),
                'status' => 'in_progress',
                'tasks' => [
                    ['title' => 'Suivre le cours sur Laravel Queues', 'done' => true, 'order' => 1],
                    ['title' => 'Implémenter un système d\'events', 'done' => true, 'order' => 2],
                    ['title' => 'Créer des notifications par email', 'done' => false, 'order' => 3],
                    ['title' => 'Écrire des tests unitaires complets', 'done' => false, 'order' => 4],
                ]
            ],
            [
                'title' => 'Lire 12 livres cette année',
                'description' => 'Un livre par mois pour enrichir mes connaissances.',
                'category' => 'Personnel',
                'target_date' => now()->endOfYear(),
                'status' => 'in_progress',
                'tasks' => [
                    ['title' => 'Clean Code - Robert Martin', 'done' => true, 'order' => 1],
                    ['title' => 'The Pragmatic Programmer', 'done' => true, 'order' => 2],
                    ['title' => 'Domain-Driven Design', 'done' => false, 'order' => 3],
                    ['title' => 'Refactoring - Martin Fowler', 'done' => false, 'order' => 4],
                ]
            ],
            [
                'title' => 'Créer un Side Project rentable',
                'description' => 'Développer et lancer une application SaaS générant 1000€/mois.',
                'category' => 'Entrepreneuriat',
                'target_date' => now()->addMonths(6),
                'status' => 'planned',
                'tasks' => [
                    ['title' => 'Valider l\'idée avec 10 interviews', 'done' => false, 'order' => 1],
                    ['title' => 'Créer un MVP en 2 semaines', 'done' => false, 'order' => 2],
                    ['title' => 'Acquérir les 100 premiers utilisateurs', 'done' => false, 'order' => 3],
                    ['title' => 'Implémenter les paiements Stripe', 'done' => false, 'order' => 4],
                ]
            ],
            [
                'title' => 'Améliorer ma condition physique',
                'description' => 'Perdre 5kg et courir 10km sans s\'arrêter.',
                'category' => 'Santé',
                'target_date' => now()->addMonths(4),
                'status' => 'in_progress',
                'tasks' => [
                    ['title' => 'Courir 3x par semaine', 'done' => true, 'order' => 1],
                    ['title' => 'Atteindre 5km sans pause', 'done' => true, 'order' => 2],
                    ['title' => 'Courir 7km sans pause', 'done' => false, 'order' => 3],
                    ['title' => 'Courir 10km sans pause', 'done' => false, 'order' => 4],
                ]
            ],
        ];

        foreach ($goals as $goalData) {
            $tasks = $goalData['tasks'];
            unset($goalData['tasks']);
            
            $goal = Goal::create([
                'user_id' => $user->id,
                ...$goalData,
            ]);

            foreach ($tasks as $taskData) {
                GoalTask::create([
                    'goal_id' => $goal->id,
                    ...$taskData,
                ]);
            }
        }

        // === HABITUDES ===
        $habits = [
            [
                'name' => 'Méditation matinale',
                'frequency' => 'daily',
                'days_of_week' => null,
                'color' => '#8B5CF6', // violet
            ],
            [
                'name' => 'Coder sur un side project',
                'frequency' => 'daily',
                'days_of_week' => null,
                'color' => '#3B82F6', // bleu
            ],
            [
                'name' => 'Lire 30 minutes',
                'frequency' => 'daily',
                'days_of_week' => null,
                'color' => '#10B981', // vert
            ],
            [
                'name' => 'Sport (Course)',
                'frequency' => 'weekly',
                'days_of_week' => [1, 3, 5], // Lun, Mer, Ven (ISO 8601: 1=Lundi)
                'color' => '#EF4444', // rouge
            ],
            [
                'name' => 'Journal de gratitude',
                'frequency' => 'daily',
                'days_of_week' => null,
                'color' => '#F59E0B', // orange
            ],
            [
                'name' => 'Apprentissage (cours en ligne)',
                'frequency' => 'weekly',
                'days_of_week' => [2, 4, 6], // Mar, Jeu, Sam
                'color' => '#EC4899', // rose
            ],
        ];

        $habitModels = [];
        foreach ($habits as $habitData) {
            $habitModels[] = Habit::create([
                'user_id' => $user->id,
                ...$habitData,
            ]);
        }

        // === LOGS D'HABITUDES (30 derniers jours avec des streaks réalistes) ===
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            foreach ($habitModels as $habit) {
                // Simuler une complétion réaliste avec des streaks et des manques
                $shouldComplete = false;
                
                if ($habit->name === 'Méditation matinale') {
                    // Streak de 15 jours récent
                    $shouldComplete = $i >= 15 && rand(1, 100) > 10;
                } elseif ($habit->name === 'Coder sur un side project') {
                    // Bon streak avec quelques absences
                    $shouldComplete = rand(1, 100) > 20;
                } elseif ($habit->name === 'Lire 30 minutes') {
                    // Très régulier
                    $shouldComplete = rand(1, 100) > 15;
                } elseif ($habit->name === 'Sport (Course)') {
                    // Seulement les jours planifiés avec bonne adhérence
                    if ($habit->isDueOn($date)) {
                        $shouldComplete = rand(1, 100) > 25;
                    }
                } elseif ($habit->name === 'Journal de gratitude') {
                    // Un peu irrégulier
                    $shouldComplete = rand(1, 100) > 40;
                } elseif ($habit->name === 'Apprentissage (cours en ligne)') {
                    // Jours planifiés avec adhérence moyenne
                    if ($habit->isDueOn($date)) {
                        $shouldComplete = rand(1, 100) > 35;
                    }
                }

                if ($shouldComplete) {
                    HabitLog::create([
                        'habit_id' => $habit->id,
                        'date' => $date,
                        'completed' => true,
                    ]);
                }
            }
        }

        // === JOURNAL ENTRIES (15 dernières entrées) ===
        $journalEntries = [
            [
                'date' => now()->subDays(1),
                'content' => 'Excellente journée ! J\'ai terminé l\'implémentation du système de streaks pour les habitudes. Le code est propre et testé. Je me sens vraiment productif en ce moment.',
                'mood' => 'excellent',
            ],
            [
                'date' => now()->subDays(2),
                'content' => 'Journée un peu difficile. Bloqué sur un bug pendant 3 heures. Finalement résolu grâce à une relecture du code. Rappel : faire des pauses plus souvent.',
                'mood' => 'neutral',
            ],
            [
                'date' => now()->subDays(4),
                'content' => 'Super session de course ce matin ! 7km en 40 minutes. Je progresse vers mon objectif de 10km. La régularité paie vraiment.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(5),
                'content' => 'J\'ai lu un chapitre fascinant sur le Domain-Driven Design. Je commence à comprendre comment mieux structurer mes applications. Hâte d\'appliquer ces concepts.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(7),
                'content' => 'Journée productive ! J\'ai mis en place la CI/CD pour mon side project. Les tests tournent automatiquement sur chaque commit. C\'est satisfaisant de voir le workflow fonctionner.',
                'mood' => 'excellent',
            ],
            [
                'date' => now()->subDays(9),
                'content' => 'Réflexion du jour : je dois mieux équilibrer code et lecture. Je passe parfois trop de temps à coder sans prendre le temps d\'apprendre de nouvelles choses.',
                'mood' => 'neutral',
            ],
            [
                'date' => now()->subDays(11),
                'content' => 'Premier interview utilisateur pour mon side project. Feedback très positif ! Quelques ajustements à faire sur l\'UX mais l\'idée plaît. Motivé pour la suite.',
                'mood' => 'excellent',
            ],
            [
                'date' => now()->subDays(13),
                'content' => 'Mauvaise journée. Pas réussi à me concentrer, trop de distractions. Je dois améliorer ma routine matinale pour mieux démarrer la journée.',
                'mood' => 'bad',
            ],
            [
                'date' => now()->subDays(15),
                'content' => 'Découverte intéressante : Inertia.js simplifie vraiment le développement full-stack. Plus besoin de gérer une API REST séparée. J\'adore cette approche.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(18),
                'content' => 'Week-end tranquille. J\'ai pris le temps de ne rien faire de productif. Parfois, le repos est aussi important que le travail. Je me sens rechargé.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(20),
                'content' => 'Journée marathon de code. 10 heures sur l\'implémentation du calendrier. Fatigué mais satisfait du résultat. Le drag & drop fonctionne parfaitement.',
                'mood' => 'neutral',
            ],
            [
                'date' => now()->subDays(22),
                'content' => 'J\'ai commencé à méditer tous les matins. Seulement 10 minutes mais je sens déjà la différence. Plus calme, plus concentré. Je vais continuer.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(25),
                'content' => 'Première fois que je cours 5km sans m\'arrêter ! Objectif atteint. Maintenant direction les 7km. La progression est lente mais constante.',
                'mood' => 'excellent',
            ],
            [
                'date' => now()->subDays(27),
                'content' => 'Discussion inspirante avec un autre dev sur les architectures modulaires. J\'ai découvert de nouvelles approches pour organiser mon code. Toujours apprendre.',
                'mood' => 'good',
            ],
            [
                'date' => now()->subDays(30),
                'content' => 'Début du mois ! J\'ai défini mes objectifs et mes habitudes à suivre. Enthousiaste pour ce nouveau départ. Let\'s go!',
                'mood' => 'excellent',
            ],
        ];

        foreach ($journalEntries as $entryData) {
            JournalEntry::create([
                'user_id' => $user->id,
                ...$entryData,
            ]);
        }
    }
}
