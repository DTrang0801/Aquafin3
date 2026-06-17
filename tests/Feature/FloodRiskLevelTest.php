<?php

use App\Enums\FloodRiskLevel;

test('FloodRiskLevel has correct string values', function () {
    expect(FloodRiskLevel::Low->value)->toBe('low');
    expect(FloodRiskLevel::Medium->value)->toBe('medium');
    expect(FloodRiskLevel::High->value)->toBe('high');
});

test('FloodRiskLevel labels are in Dutch', function () {
    expect(FloodRiskLevel::Low->label())->toBe('Laag');
    expect(FloodRiskLevel::Medium->label())->toBe('Gemiddeld');
    expect(FloodRiskLevel::High->label())->toBe('Hoog');
});

test('meetsOrExceeds returns true when level equals required', function () {
    expect(FloodRiskLevel::Low->meetsOrExceeds(FloodRiskLevel::Low))->toBeTrue();
    expect(FloodRiskLevel::Medium->meetsOrExceeds(FloodRiskLevel::Medium))->toBeTrue();
    expect(FloodRiskLevel::High->meetsOrExceeds(FloodRiskLevel::High))->toBeTrue();
});

test('meetsOrExceeds returns true when level exceeds required', function () {
    expect(FloodRiskLevel::Medium->meetsOrExceeds(FloodRiskLevel::Low))->toBeTrue();
    expect(FloodRiskLevel::High->meetsOrExceeds(FloodRiskLevel::Low))->toBeTrue();
    expect(FloodRiskLevel::High->meetsOrExceeds(FloodRiskLevel::Medium))->toBeTrue();
});

test('meetsOrExceeds returns false when level is below required', function () {
    expect(FloodRiskLevel::Low->meetsOrExceeds(FloodRiskLevel::Medium))->toBeFalse();
    expect(FloodRiskLevel::Low->meetsOrExceeds(FloodRiskLevel::High))->toBeFalse();
    expect(FloodRiskLevel::Medium->meetsOrExceeds(FloodRiskLevel::High))->toBeFalse();
});

test('FloodRiskLevel can be created from string value', function () {
    expect(FloodRiskLevel::from('low'))->toBe(FloodRiskLevel::Low);
    expect(FloodRiskLevel::from('medium'))->toBe(FloodRiskLevel::Medium);
    expect(FloodRiskLevel::from('high'))->toBe(FloodRiskLevel::High);
});
