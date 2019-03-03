<?php
// src/AppBundle/AppFixtures.php
namespace AppBundle\Fixtures;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // create 10 posts! Bam!
        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->setTitle('Title' . $i);
            $post->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean faucibus non purus non varius. Phasellus tincidunt dui pharetra, dignissim risus eget, sodales est. Sed eget sollicitudin velit. Phasellus eu pretium neque, quis ullamcorper nibh. Vestibulum semper gravida lacus sit amet interdum. Ut id diam dolor. Curabitur ut erat nec turpis gravida vestibulum. ');
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());
            $manager->persist($post);
        }
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@example.com');
        $user->setPlainPassword('pass1234');
        $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));

        $manager->persist($user);

        $manager->flush();
    }
}