<?php

use App\Models\User;

test('about page uses english copy when accept language is english', function () {
    $response = $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get(route('about.show'));

    $response->assertSuccessful();
    $response->assertSee('For full background and professional references, see my LinkedIn profile.');
    $response->assertDontSee('Pour un parcours complet et des références professionnelles, consulte mon profil LinkedIn.');
});

test('community link create page uses user preferred locale copy', function () {
    $user = User::factory()->create([
        'preferred_locale' => 'en',
    ]);

    $response = $this->actingAs($user)->get(route('community-links.create'));

    $response->assertSuccessful();
    $response->assertSee('Submit a community link');
    $response->assertSee('Language');
    $response->assertDontSee('Soumettre un lien communautaire');
});
