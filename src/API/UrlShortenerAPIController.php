<?php

declare(strict_types=1);

namespace App\API;

use App\API\Input\ShortenUrlInput;
use App\Domain\AddShortenedUrl\AddShortenedUrlCommand;
use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\GetRedirections\GetShortenedUrlByIdQuery;
use App\Domain\GetRedirections\GetShortenedUrlsQuery;
use App\Domain\GetUrlCount\GetUrlCountQuery;
use App\Domain\RemoveShortenedUrl\RemoveShortenedUrlCommand;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Query\QueryBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @Route("/api/short-urls", name="api_") */
class UrlShortenerAPIController extends AbstractController
{
    private CommandBus $commandBus;
    private QueryBus $queryBus;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        CommandBus $commandBus,
        QueryBus $queryBus,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="add_new", methods={"POST"})
     * @OA\Tag(name="Short Url")
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *          @OA\Property(property="url", type="string", example="https://www.laredoute.fr")
     *     )
     * )
     * @OA\Response(
     *     response="200",
     *     description="Adds a new URL redirect",
     *     @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="uri", type="string", example="WEIew_eof-UWEf")
     *     )
     * )
     */
    public function addNewUrl(Request $request): Response
    {
        try {
            /**
             * @var $payload ShortenUrlInput
             */
            $payload = $this->serializer->deserialize($request->getContent(), ShortenUrlInput::class, 'json');
        } catch (\Exception $exception) {
            throw new BadRequestException('The payload is badly formatted');
        }

        $errors = $this->validator->validate($payload);

        if (count($errors)) {
            // space for improvement
            throw new BadRequestException('The submitted value is not a valid url');
        }

        try {
            $uri = $this->commandBus->handleCommand(new AddShortenedUrlCommand($payload->getUrl()));
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occurred');
        }

        return $this->json(['uri' => $uri]);
    }

    /**
     * @Route("", name="get_all_urls", methods={"GET"})
     * @OA\Tag(name="Short Url")
     * @OA\Response(
     *     response="200",
     *     description="List of all available short url redirects",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=App\Domain\ShortenedUrlDto::class)),
     *     )
     * )
     */
    public function getAllLinkDetails(): Response
    {
        try {
            $urls = $this->queryBus->handleQuery(new GetShortenedUrlsQuery());
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occurred');
        }

        return $this->json($urls);
    }

    /**
     * @Route("/{id}", name="get_single_url", methods={"GET"})
     * @OA\Tag(name="Short Url")
     * @OA\Response(
     *     response="200",
     *     description="Detail of a url redirect",
     *     @Model(type=App\Domain\ShortenedUrlDto::class)
     * )
     * @OA\Parameter(name="id", description="The short url Id", in="query", example="6d2b9340-e738-41e0-a2cb-f5feff456965")
     */
    public function getShortenedUrlDetails(string $id): Response
    {
        if (!uuid_is_valid($id)) {
            throw new BadRequestException('Id is not a valid UUID');
        }

        $url = $this->queryBus->handleQuery(new GetShortenedUrlByIdQuery($id));

        if (!$url) {
            throw new NotFoundHttpException('The short Url you provided does not exist');
        }

        return $this->json($url);
    }

    /**
     * @Route("/{id}", name="remove_shortened_url", methods={"DELETE"})
     * @OA\Tag(name="Short Url")
     * @OA\Parameter(name="id", description="The short url Id", in="query", example="6d2b9340-e738-41e0-a2cb-f5feff456965")
     */
    public function removeShortenedUrl(string $id): Response
    {
        if (!uuid_is_valid($id)) {
            throw new BadRequestException('Id is not a valid UUID');
        }

        try {
            $this->commandBus->handleCommand(new RemoveShortenedUrlCommand($id));
        } catch (ShortenedUrlDoesNotExistException $exception) {
            throw new NotFoundHttpException('The url you want to delete, does not exist');
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occurred');
        }

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/count", name="get_count", methods={"GET"})
     * @OA\Tag(name="Short Url")
     * @OA\Parameter(name="id", description="The short url Id", in="query", example="6d2b9340-e738-41e0-a2cb-f5feff456965")
     * @OA\Response(
     *     response="200",
     *     description="Returns the number of views",
     *     @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="count", type="string", example="123")
     *     )
     * )
     */
    public function getLinkCounts(string $id): Response
    {
        if (!uuid_is_valid($id)) {
            throw new BadRequestException('Id is not a valid UUID');
        }

        try {
            $count = $this->queryBus->handleQuery(new GetUrlCountQuery($id));
        } catch (ShortenedUrlDoesNotExistException $exception) {
            throw new NotFoundHttpException('The shorten url does not exist');
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occurred');
        }

        return $this->json(['count' => $count]);
    }
}
