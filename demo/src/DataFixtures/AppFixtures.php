<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $authors = ['Alice', 'Bob', 'Charlie', 'Diana', 'Eve', 'Frank', 'Grace', 'Heidi'];

        $topics = [
            'Getting Started with Symfony 7', 'Understanding Doctrine ORM Relationships',
            'Building REST APIs with API Platform', 'Symfony Messenger: Async Processing',
            'Testing Best Practices in Symfony', 'Securing Your Symfony Application',
            'Performance Optimization Tips', 'Working with Twig Templates',
            'Deploying Symfony to Production', 'Symfony and AI: The Future',
            'Mastering Symfony Forms', 'Event-Driven Architecture in Symfony',
            'Symfony Console Commands', 'Service Container Deep Dive',
            'HTTP Client Component', 'Symfony Mailer Integration',
            'Webpack Encore for Assets', 'Symfony Workflow Component',
            'Custom Validators in Symfony', 'Symfony Cache Component',
        ];

        $bodies = [
            'Symfony brings exciting new features including improved performance, better developer experience, and native support for modern PHP features like enums and fibers.',
            'Understanding the fundamentals is crucial for building scalable applications. This guide walks through the key concepts every developer should know.',
            'Modern web development requires a solid understanding of best practices. In this article, we explore the patterns that make Symfony applications maintainable.',
            'The Symfony ecosystem continues to grow with new components and bundles that simplify common tasks and improve developer productivity.',
            'Performance is a feature. In this deep dive, we look at the tools and techniques available in Symfony for building fast, responsive applications.',
        ];

        for ($i = 0; $i < 100; $i++) {
            $post = new Post();
            $post->setTitle($topics[$i % count($topics)] . ($i >= count($topics) ? ' (Part ' . ((int) ($i / count($topics)) + 1) . ')' : ''));
            $post->setBody($bodies[$i % count($bodies)]);
            $post->setCreatedAt(new \DateTimeImmutable(sprintf('-%d days', 300 - $i * 3)));

            $commentCount = random_int(5, 15);
            for ($j = 0; $j < $commentCount; $j++) {
                $comment = new Comment();
                $comment->setAuthor($authors[array_rand($authors)]);
                $comment->setContent($this->generateComment());
                $comment->setCreatedAt(new \DateTimeImmutable(sprintf('-%d hours', random_int(1, 2000))));
                $comment->setPost($post);
                $manager->persist($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function generateComment(): string
    {
        $comments = [
            'Great article! This really helped me understand the concept.',
            'I\'ve been looking for exactly this explanation. Thanks!',
            'Could you elaborate on the performance implications?',
            'We use this pattern in production and it works great.',
            'Nice write-up. One thing I\'d add is error handling.',
            'This saved me hours of debugging. Thank you!',
            'Interesting approach. How does this scale?',
            'I ran into issues with this on Symfony 6.4, any tips?',
            'The code examples are really clear and practical.',
            'Would love to see a follow-up on advanced use cases.',
            'We migrated to this approach last month — no regrets.',
            'How does this compare to the Laravel equivalent?',
            'Bookmarked! Will definitely reference this later.',
            'The profiler screenshot really drives the point home.',
            'Any gotchas when using this with PostgreSQL?',
        ];

        return $comments[array_rand($comments)];
    }
}
