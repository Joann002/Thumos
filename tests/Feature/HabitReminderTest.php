<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\DailyHabitReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HabitReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-06-18'); // Thursday (ISO day 4)
        Notification::fake();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_a_user_with_a_pending_daily_habit_is_notified(): void
    {
        $user = User::factory()->create();
        $user->habits()->create(['name' => 'Méditer', 'frequency' => 'daily']);

        $this->artisan('app:send-habit-reminders')->assertSuccessful();

        Notification::assertSentTo(
            $user,
            DailyHabitReminder::class,
            fn (DailyHabitReminder $n) => $n->pendingHabits === ['Méditer'],
        );
    }

    public function test_a_completed_habit_does_not_trigger_a_reminder(): void
    {
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => true]);

        $this->artisan('app:send-habit-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_a_weekly_habit_not_due_today_is_ignored(): void
    {
        $user = User::factory()->create();
        // Scheduled Mon/Wed/Fri only; today is Thursday.
        $user->habits()->create([
            'name' => 'Sport',
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5],
        ]);

        $this->artisan('app:send-habit-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_a_weekly_habit_due_today_triggers_a_reminder(): void
    {
        $user = User::factory()->create();
        // Scheduled Thursday (4) among others; today is Thursday.
        $user->habits()->create([
            'name' => 'Cours du soir',
            'frequency' => 'weekly',
            'days_of_week' => [2, 4],
        ]);

        $this->artisan('app:send-habit-reminders')->assertSuccessful();

        Notification::assertSentTo($user, DailyHabitReminder::class);
    }
}
