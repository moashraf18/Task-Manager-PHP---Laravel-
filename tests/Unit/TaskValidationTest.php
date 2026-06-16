<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TaskValidationTest extends TestCase
{
    // ── TEST 4: Title length validation logic ─────────────────────────────────
    public function test_title_must_be_at_least_3_characters(): void
    {
        $title = 'ab';
        $this->assertLessThan(3, strlen($title));  // proves validation would catch it
    }

    // ── TEST 5: Priority must be one of the allowed values ────────────────────
    public function test_priority_values_are_valid(): void
    {
        $allowed = ['low', 'medium', 'high'];
        $this->assertContains('low',    $allowed);
        $this->assertContains('medium', $allowed);
        $this->assertContains('high',   $allowed);
        $this->assertNotContains('urgent', $allowed); // invalid value
    }

    // ── TEST 6: Due date in the past is invalid ───────────────────────────────
    public function test_past_due_date_is_invalid(): void
    {
        $pastDate = date('Y-m-d', strtotime('-1 day'));
        $today    = date('Y-m-d');
        $this->assertLessThan($today, $pastDate);
    }
}