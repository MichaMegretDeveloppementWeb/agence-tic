<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function testHomePageIsAccessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Agence TIC');
    }

    public function testRecruitmentPageIsAccessible(): void
    {
        $response = $this->get(route('recruitment'));

        $response->assertOk();
    }

    public function testLoginPageIsAccessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }
}
