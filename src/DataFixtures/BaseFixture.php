<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

abstract class BaseFixture extends Fixture
{
    private $manager;
    protected $faker;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
    }

    protected function loadRandomly(string $entityClass, string $group, int $count, callable $function) {

        for ($i = 0; $i < $count; $i++) {
            $entity = new $entityClass();

            $function($entity);

            $this->manager->persist($entity);
            $this->addReference(sprintf('%s_%d', $group, $i), $entity);
        }
    }

    protected function getRandomReference(string $group) {

        $references = $this->getReferencesByGroup($group);

        $reference = $this->faker->randomElement($references);
        return $this->getReference($reference);
    }

    private function getReferencesByGroup(string $group) {
        $references = $this->referenceRepository->getReferences();
        $groupReferences = array();

        foreach ($references as $key => $reference) {
            if (strpos($key.'_', $group) === 0) {
                $groupReferences[] = $key;
            }
        }

        return $groupReferences;
    }
}