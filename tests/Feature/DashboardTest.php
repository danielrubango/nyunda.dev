<?php

use App\Filament\Resources\CommunityLinks\CommunityLinkResource;
use App\Filament\Resources\NewsletterEditions\NewsletterEditionResource;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('admin users can visit the dashboard', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee(__('ui.nav.blog'));
    $response->assertSee(__('ui.nav.links'));
    $response->assertSee(__('ui.nav.account'));
    $response->assertSee('Voir mes contenus');
    $response->assertSee('Proposer un contenu');
    $response->assertDontSee('Moderate comments and community interactions.');
    $response->assertDontSee('Track subscriber growth and exports.');
    $response->assertSee(CommunityLinkResource::getUrl(panel: 'admin'), false);
    $response->assertSee(NewsletterEditionResource::getUrl(panel: 'admin'), false);
});

test('non admin users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee(__('ui.nav.account'));
    $response->assertDontSee('Gerer la communaute (Filament)');
    $response->assertDontSee('Gerer la newsletter (Filament)');
});
