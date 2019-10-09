<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Security;

class PostFixture extends BaseFixture implements DependentFixtureInterface
{
    private $security;

    public function __construct(ObjectManager $manager, Security $security)
    {
        parent::__construct($manager);
        $this->security = $security;
    }

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

            $poster = $this->getRandomPoster();

            $post->setUser($poster);
        });

        $manager->flush();
    }

    private function getRandomPoster() {
        $isPoster = false;

        while ($isPoster === false) {
            /** @var User $user */
            $user = $this->getRandomReference('user');
            $roles = $user->getRoles();

            if (in_array('ROLE_POSTER', $roles)) {
                $isPoster = true;
            }
        }

        return $user;
    }

    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }
}
