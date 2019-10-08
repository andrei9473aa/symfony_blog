<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixture extends BaseFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $previews = [
            'img/blog-big/1.jpg',
            'img/blog-big/2.jpg',
            'img/blog-big/3.jpg'
        ];

        $this->loadRandomly(Post::class, 'post', 50, function(Post $post) use ($previews) {
            $post->setTitle($this->faker->sentence);
            $post->setPreview($this->faker->randomElement($previews));
            $post->setBody($this->faker->text(900));

            /** @var User $user */
            $user = $this->getRandomReference('user');
            $post->setUser($user);
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }
}
