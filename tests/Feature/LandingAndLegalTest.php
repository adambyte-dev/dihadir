<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingAndLegalTest extends TestCase
{
    public function test_landing_page_renders_successfully_with_adam_profile_and_features(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('DIHADIR');
        $response->assertSee('Muhamad Adam Hidayat');
        $response->assertSee('Keisha Nur Asyifa');
        $response->assertSee('Aldi Faturrizqy');
        $response->assertSee('Nayma Zahra Nurrizki');
        $response->assertSee('SRJ-DEV-4E82A1');
        $response->assertSee('Sekolah Anda Berminat Menggunakan Sistem DIHADIR?');
        $response->assertSee('info@smkn1majalengka.sch.id');
    }

    public function test_privacy_policy_page_renders_successfully(): void
    {
        $response = $this->get(route('legal.privacy'));
        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('UU No. 27 Tahun 2022 (UU PDP)');
        $response->assertSee('Komitmen Perlindungan Privasi');
    }

    public function test_terms_of_service_page_renders_successfully(): void
    {
        $response = $this->get(route('legal.terms'));
        $response->assertStatus(200);
        $response->assertSee('Syarat');
        $response->assertSee('Ketentuan Layanan DIHADIR');
        $response->assertSee('Penerimaan Ketentuan');
        $response->assertSee('Kepemilikan Hak Cipta');
    }
}
