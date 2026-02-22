<?php

use App\Models\User;

test('about page defaults to french copy even when accept language is english', function () {
    $response = $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get(route('about.show'));

    $response->assertSuccessful();
    $response->assertSee('Je suis un développeur logiciel Full-Stack avec plus de six ans d’expérience en développement web et en systèmes IT.');
    $response->assertDontSee('I am a Full-Stack Software Developer with over six years of experience in web development and IT systems.');
});

test('locale can be switched manually from the locale endpoint', function () {
    $this->from(route('about.show'))
        ->post(route('locale.update'), [
            'locale' => 'en',
        ])
        ->assertRedirect(route('about.show'));

    $response = $this->get(route('about.show'));

    $response->assertSuccessful();
    $response->assertSee('I am a Full-Stack Software Developer with over six years of experience in web development and IT systems.');
    $response->assertDontSee('Je suis un développeur logiciel Full-Stack avec plus de six ans d’expérience en développement web et en systèmes IT.');
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
