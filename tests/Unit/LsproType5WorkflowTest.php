<?php

namespace Tests\Unit;

use App\Support\LsproType5Workflow;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LsproType5WorkflowTest extends TestCase
{
    public function test_statuses_are_mapped_to_the_interactive_certification_steps(): void
    {
        $this->assertSame(1, LsproType5Workflow::stageFor('menunggu_ttd'));
        $this->assertSame(3, LsproType5Workflow::stageFor('perjanjian'));
        $this->assertSame(4, LsproType5Workflow::stageFor('billing'));
        $this->assertSame(6, LsproType5Workflow::stageFor('proses_audit'));
        $this->assertSame(7, LsproType5Workflow::stageFor('keputusan'));
        $this->assertSame(8, LsproType5Workflow::stageFor('selesai'));
    }

    public function test_valid_forward_and_correction_transitions_are_allowed(): void
    {
        $this->assertTrue(LsproType5Workflow::canTransition('menunggu_ttd', 'diajukan'));
        $this->assertTrue(LsproType5Workflow::canTransition('diajukan', 'verifikasi_tu'));
        $this->assertTrue(LsproType5Workflow::canTransition('verifikasi_tu', 'perbaikan'));
        $this->assertTrue(LsproType5Workflow::canTransition('verifikasi_tu', 'perjanjian'));
        $this->assertTrue(LsproType5Workflow::canTransition('keputusan', 'selesai'));
        $this->assertTrue(LsproType5Workflow::canTransition('keputusan', 'ditolak'));
    }

    public function test_skipping_a_stage_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LsproType5Workflow::assertTransition('perjanjian', 'proses_audit');
    }

    public function test_legacy_statuses_are_normalized(): void
    {
        $this->assertSame('perjanjian', LsproType5Workflow::normalize('lengkap'));
        $this->assertSame('billing', LsproType5Workflow::normalize('invoice_diterbitkan'));
        $this->assertSame('ditolak', LsproType5Workflow::normalize('rejected'));
    }
}
