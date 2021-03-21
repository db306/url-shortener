<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\ShortenedUrl;
use App\Domain\ShortenedUrlRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class DoctrineShortenedUrlRepository implements ShortenedUrlRepository
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository(ShortenedUrl::class);
    }

    public function save(ShortenedUrl $url): void
    {
        $this->em->persist($url);
        $this->em->flush();
    }

    public function getShortenedUrlByUri(string $uri): ?ShortenedUrl
    {
        return $this->repository->findOneBy(['uri' => $uri]);
    }

    public function getUrlCount(string $id): ?int
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('u.count')
            ->from('App:ShortenedUrl', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function getShortenedUrls(): array
    {
        return $this->repository->findAll();
    }

    public function getShortenedUrlById(string $id): ?ShortenedUrl
    {
        return $this->repository->find($id);
    }

    public function removeShortenedUrl(ShortenedUrl $shortenedUrl): void
    {
        $this->em->remove($shortenedUrl);
        $this->em->flush();
    }
}
