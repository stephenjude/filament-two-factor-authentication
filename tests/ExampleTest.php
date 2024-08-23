<?php

it('can test', function () {
    $this->actingAs(User::factory()->create());

    expect(filament()->auth()->check())->toBeTrue();
});
