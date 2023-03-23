<?php

namespace Tests;


use Database\Seeders\BasicRequiredDataSeeder;

trait RunsBasicSeeders
{
    public function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[RunsBasicSeeders::class])) {
            $this->runBasicSeeder();
        }

        return $uses;
    }

    private function runBasicSeeder()
    {
        resolve(BasicRequiredDataSeeder::class)->run();
    }
}
