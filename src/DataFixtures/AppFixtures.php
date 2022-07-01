<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker   = Factory::create();
        $slugger = new AsciiSlugger();

        /* Utilisateurs */
        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($this->passwordHasher->hashPassword($user, 'Test1234*'));

            $users[] = $user;

            $manager->persist($user);
        }

        /* Administrateur */
        $administrateur = new User();
        $administrateur
            ->setFirstname('Mickaël')
            ->setLastname('AUGER')
            ->setEmail('mauger@cefim.eu')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($administrateur, 'Test1234*'));

        $manager->persist($administrateur);

        /* Catégories */
        $categories = [];

        for ($c = 0; $c < 10; $c++) {
            $category = new Category();
            $category->setName($faker->realText(25, 5));

            $categories[] = $category;

            $manager->persist($category);
        }

        /* Articles */
        $posts = [];

        for ($a = 0; $a < 50; $a++) {
            $post = new Post();
            $post
                ->setTitle($faker->realText(25, 5))
                ->setContent($faker->realText(500, 5))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')))
                ->setSlug(strtolower($slugger->slug($post->getTitle())))
                ->setCategory($categories[random_int(0, count($categories) - 1)])
                ->setAuthor($administrateur);

            $posts[] = $post;

            $manager->persist($post);
        }

        /* Commentaires */
        for ($c = 0; $c < 250; $c++) {
            $comment     = new Comment();
            $comment
                ->setPost($posts[random_int(0, count($posts) - 1)])
                ->setAuthor($users[random_int(0, count($users) - 1)])
                ->setContent($faker->realText())
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+7 days')));

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
