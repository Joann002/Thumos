<?php

namespace Tests\Unit;

use App\Helpers\DateHelper;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DateHelperTest extends TestCase
{
    /** @test */
    public function it_formats_date_in_french()
    {
        $date = Carbon::parse('2024-03-15');
        $formatted = DateHelper::formatFrench($date);

        $this->assertEquals('15 mars 2024', $formatted);
    }

    /** @test */
    public function it_returns_today_for_current_date()
    {
        $today = Carbon::today();
        $result = DateHelper::relativeTimeFrench($today);

        $this->assertEquals("aujourd'hui", $result);
    }

    /** @test */
    public function it_returns_yesterday()
    {
        $yesterday = Carbon::yesterday();
        $result = DateHelper::relativeTimeFrench($yesterday);

        $this->assertEquals('hier', $result);
    }

    /** @test */
    public function it_returns_tomorrow()
    {
        $tomorrow = Carbon::tomorrow();
        $result = DateHelper::relativeTimeFrench($tomorrow);

        $this->assertEquals('demain', $result);
    }

    /** @test */
    public function it_calculates_days_until_future_date()
    {
        $futureDate = Carbon::today()->addDays(5);
        $days = DateHelper::daysUntil($futureDate);

        $this->assertEquals(5, $days);
    }

    /** @test */
    public function it_returns_negative_for_past_dates()
    {
        $pastDate = Carbon::today()->subDays(3);
        $days = DateHelper::daysUntil($pastDate);

        $this->assertEquals(-3, $days);
    }

    /** @test */
    public function it_gets_french_day_name()
    {
        $monday = Carbon::parse('2024-01-01'); // Monday
        $result = DateHelper::dayNameFrench($monday);

        $this->assertEquals('lundi', $result);
    }
}
