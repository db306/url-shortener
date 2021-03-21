<?php

declare(strict_types=1);

namespace App\API;

use App\Domain\GetRedirections\GetShortenedUrlByUriQuery;
use App\Domain\IncrementViewCount\IncrementViewCountCommand;
use App\Domain\ShortenedUrlDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/", name="redirect_") */
class RedirectController extends AbstractController
{
    private CommandBus $commandBus;
    private QueryBus $queryBus;

    public function __construct(
        CommandBus $commandBus,
        QueryBus $queryBus
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/{uri}", name="to_short_url", methods={"GET"})
     */
    public function redirectShortUrl(string $uri): Response
    {
        /**
         * @var $url ShortenedUrlDto
         */
        $url = $this->queryBus->handleQuery(new GetShortenedUrlByUriQuery($uri));

        if (!$url) {
            throw new NotFoundHttpException('The short Url you provided does not exist');
        }

        // Place for improvement: this could be sent async in order to improve performance
        $this->commandBus->handleCommand(new IncrementViewCountCommand($uri));

        return $this->redirect($url->getRedirectUrl());
    }
}
