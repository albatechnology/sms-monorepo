<?php

namespace Database\Factories;

use Bezhanov\Faker\ProviderCollectionHelper;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class BaseFactory extends Factory
{
    /**
     * Override default faker, add provider
     *
     * @return Generator
     * @throws BindingResolutionException
     */
    protected function withFaker(): Generator
    {
        $faker = Container::getInstance()->make(Generator::class);
        ProviderCollectionHelper::addAllProvidersTo($faker);
        return $faker;
    }

    public function sample()
    {
        return $this;
    }
}
