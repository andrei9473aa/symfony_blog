<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadRandomly(Comment::class, 'comment', 350, function (Comment $comment) {
            $comment->setBody($this->faker->text(255));

            /** @var Post $post */
            $post = $this->getRandomReference('post');
            $comment->setPost($post);

            /** @var User $user */
            $user = $this->getRandomReference('user');
            $comment->setUser($user);
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PostFixture::class
        ];
    }
}
