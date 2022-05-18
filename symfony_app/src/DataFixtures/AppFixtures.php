<?php

namespace App\DataFixtures;

use App\Entity\Listing;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadListings($manager);
        $this->loadTasks($manager);
    }

    public function loadTasks(ObjectManager $manager): void
    {
        foreach($this->getTaskData() as ['title' => $title, 'state' => $state]) {
            $listing = (new Task())->setTitle($title)->setState($state);

            $manager->persist($listing);
        }

        $manager->flush();
    }

    public function getTaskData(): array
    {
        return [
            [
                'title' => 'Call Goku',
                'state' => false,
            ],
            [
                'title' => 'Training with Piccolo',
                'state' => false,
            ],
            [
                'title' => 'Go to the parc with Pan',
                'state' => false,
            ],
            [
                'title' => 'Call Gohan',
                'state' => false,
            ],
            [
                'title' => 'Play with Goten',
                'state' => false,
            ],
            [
                'title' => 'Date with Chichi',
                'state' => false,
            ]
        ];
    }

    public function loadListings(ObjectManager $manager): void
    {
        foreach($this->getListingData() as ['name' => $name]) {
            $listing = (new Listing())->setName($name);

            $manager->persist($listing);
        }

        $manager->flush();
    }

    public function getListingData(): array
    {
        return [
            [
                'name' => 'List One',
            ],
            [
                'name' => 'List Deux',
            ],
            [
                'name' => 'List San',
            ]
        ];
    }
}
