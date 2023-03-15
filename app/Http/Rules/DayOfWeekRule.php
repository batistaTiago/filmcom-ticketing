<?php


namespace App\Http\Rules;

use Carbon\CarbonInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DayOfWeekRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sunday = CarbonInterface::SUNDAY;
        $saturday = CarbonInterface::SATURDAY;
        if ($value < $sunday || $value > $saturday) {
            $fail("The :attribute must be a weekday: ($sunday for sunday and $saturday for saturday).");
        }
    }
}
