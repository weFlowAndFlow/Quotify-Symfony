<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\OriginalWork;
use App\Entity\Quote;
use App\Entity\User;
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

    public function load(ObjectManager $em)
    {
        // User
        $user1 = new User();
        $user1->setEmail('admin@admin.com');
        $user1->setFirstname('Jean');
        $user1->setLastname('Dupont');
        $password1 = $this->encoder->encodePassword($user1, 'password');
        $user1->setPassword($password1);
        $user1->setVerified(true);

        $user2 = new User();
        $user2->setEmail('john@doe.com');
        $user2->setFirstname('John');
        $user2->setLastname('Doe');
        $password2 = $this->encoder->encodePassword($user2, 'password');
        $user2->setPassword($password1);
        $user2->setVerified(true);

        $em->persist($user1);
        $em->persist($user2);

        // Category
        $cat1 = new Category();
        $cat1->setName('Death');
        $cat1->setUser($user2);

        $cat2 = new Category();
        $cat2->setName('Love');
        $cat2->setUser($user2);

        $cat3 = new Category();
        $cat3->setName('life');
        $cat3->setUser($user2);

        // Author
        $aut1 = new Author();
        $aut1->setForename('Michael');
        $aut1->setName('Cunningham');
        $aut1->setUser($user2);

        $aut2 = new Author();
        $aut2->setName('Madonna');
        $aut2->setUser($user2);

        $aut3 = new Author();
        $aut3->setName('Kelis');
        $aut3->setUser($user2);

        $aut4 = new Author();
        $aut4->setForename('Henry-David');
        $aut4->setName('Thoreau');
        $aut4->setUser($user2);

        // OriginalWork
        $og1 = new OriginalWork();
        $og1->setTitle('The hours');
        $og1->setYear(1999);
        $og1->setUser($user2);

        $og2 = new OriginalWork();
        $og2->setTitle('Walden');
        $og2->setYear(1850);
        $og2->setUser($user2);

        $og3 = new OriginalWork();
        $og3->setTitle('Born this way');
        $og3->setYear(2012);
        $og3->setUser($user2);

        // Quote
        $q1 = new Quote();
        $q1->setText("I'm beautiful in my way");
        $q1->setNotes('This is a note for this quote');

        $q2 = new Quote();
        $q2->setText('I used to live in Walden, next to a lake, and this was so wonderful');

        $q3 = new Quote();
        $q3->setText("How often since then has she wondered what might have happened if she'd tried to remain with him; if she’d returned Richard's kiss on the corner of Bleeker and McDougal, gone off somewhere (where?) with him, never bought the packet of incense or the alpaca coat with rose-shaped buttons. Couldn’t they have discovered something larger and stranger than what they've got. It is impossible not to imagine that other future, that rejected future, as taking place in Italy or France, among big sunny rooms and gardens; as being full of infidelities and great battles; as a vast and enduring romance laid over friendship so searing and profound it would accompany them to the grave and possibly even beyond. She could, she thinks, have entered another world. She could have had a life as potent and dangerous as literature itself.

Or then again maybe not, Clarissa tells herself. That's who I was. This is who I am--a decent woman with a good apartment, with a stable and affectionate marriage, giving a party. Venture too far for love, she tells herself, and you renounce citizenship in the country you've made for yourself. You end up just sailing from port to port.

Still, there is this sense of missed opportunity. Maybe there is nothing, ever, that can equal the recollection of having been young together. Maybe it's as simple as that. Richard was the person Clarissa loved at her most optimistic moment. Richard had stood beside her at the pond's edge at dusk, wearing cut-off jeans and rubber sandals. Richard had called her Mrs. Dalloway, and they had kissed. His mouth had opened to hers; (exciting and utterly familiar, she'd never forget it) had worked its way shyly inside until she met its own. They'd kissed and walked around the pond together.

It had seemed like the beginning of happiness, and Clarissa is still sometimes shocked, more than thirty years later to realize that it was happiness; that the entire experience lay in a kiss and a walk. The anticipation of dinner and a book. The dinner is by now forgotten; Lessing has been long overshadowed by other writers. What lives undimmed in Clarissa's mind more than three decades later is a kiss at dusk on a patch of dead grass, and a walk around a pond as mosquitoes droned in the darkening air. There is still that singular perfection, and it's perfect in part because it seemed, at the time, so clearly to promise more. Now she knows: That was the moment, right then. There has been no other.");

        $q4 = new Quote();
        $q4->setText('oh yeah yeah yeah yeah');
        $q4->setNotes('This is a note for this quote, its another one just for example');

        // ASSOCIATIONS
        $q1->setAuthor($aut2);
        $q1->setOriginalwork($og3);
        $q1->setUser($user2);
        $q1->addCategory($cat3);
        $q1->addCategory($cat2);
        $q1->addCategory($cat1);

        $q2->setAuthor($aut4);
        $q2->setOriginalwork($og2);
        $q2->setUser($user2);
        $q2->addCategory($cat3);

        $q3->setAuthor($aut1);
        $q3->setOriginalwork($og1);
        $q3->setUser($user2);
        $q3->addCategory($cat2);
        $q3->addCategory($cat3);

        $q4->setAuthor($aut2);
        $q4->setUser($user2);

        $aut1->addOriginalWork($og1);
        $aut2->addOriginalWork($og3);
        $aut4->addOriginalWork($og2);
        $aut1->addOriginalWork($og1);
        $aut3->addOriginalWork($og3);

        // Persist
        $em->persist($q1);
        $em->persist($q2);
        $em->persist($q3);
        $em->persist($q4);
        //  	$em->persist($user1);

        $em->flush();
    }
}
