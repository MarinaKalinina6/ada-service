<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdsController extends AbstractController
{
    public function __construct(AdRepository $adRepository)
    {
    }

    #[Route('/api/ads', methods: ['POST'])]
    public function add(request $request, EntityManagerInterface $entityManager): Response
    {
        //check body POST request
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BadRequestHttpException(sprintf('Filed to decode JSON: %s', $exception->getMessage()));
        }

        $title = $body['title'] ?? null;
        if (is_string($title) === false) {
            throw new BadRequestHttpException('Field "title" must be type string.');
        }
        if ($title === '') {
            throw new BadRequestHttpException('Field "title" is missing.');
        }
        if (mb_strlen($title) > 200) {
            throw new BadRequestHttpException('Field "title" must be less then 200 symbol.');
        }

        $description = $body['description'] ?? null;
        if (is_string($description) === false) {
            throw new BadRequestHttpException('Field "description" must be type string.');
        }
        if ($description === '') {
            throw new BadRequestHttpException('Field "description" is missing.');
        }
        if (mb_strlen($description) > 1000) {
            throw new BadRequestHttpException('Field "description" must be less then 1000 symbol.');
        }

        $photos = $body['photos'] ?? null;
        if (is_array($photos) !== true) {
            throw new BadRequestHttpException('Field "photos" must be array');
        }
        if (count($photos) > 3) {
            throw new NotFoundHttpException('Must be only 3 links.');
        }

        $price = $body['price'] ?? null;
        if ($price === null) {
            throw new BadRequestHttpException('Field "price" is missing.');
        }
        if (is_int($price) === false) {
            throw new BadRequestHttpException('Field "price" must of int type.');
        }
        if ($price < 1) {
            throw new BadRequestHttpException('Field "price" cannot be less than 1.');
        }

        //create ad
        $ad = new Ad(title: $title, description: $description, photos: $photos, price: $price);
        $entityManager->persist($ad);
        $entityManager->flush();

        return new JsonResponse($ad->getId(), Response::HTTP_CREATED);
    }

    #[Route('/api/ads', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
//        dump((new \DateTimeImmutable())->setTimezone(new \DateTimeZone('Europe/Minsk'))->format('Y-m-d H:i:s'));

        // get query parameters
        $page = $request->query->getInt('page');
        if ($page < 1) {
            $page = 1;
        }
        $sortByPrice = $request->query->getString('sort-by-price');
        if (in_array($sortByPrice, ['asc', 'desc'], true) === false) {
            $sortByPrice = null;
        }
        $sortByDate = $request->query->getString('sort-by-date');
        if (in_array($sortByDate, ['asc', 'desc'], true) === false) {
            $sortByDate = null;
        }

        // prepare repository arguments
        $orderBy = [];
        if ($sortByPrice !== null) {
            $orderBy['price'] = match ($sortByPrice) {
                'asc' => 'ASC',
                'desc' => 'DESC',
            };
        }
        if ($sortByDate !== null) {
            $orderBy['createdAt'] = match ($sortByDate) {
                'asc' => 'ASC',
                'desc' => 'DESC',
            };
        }
        $limit = 10;
        $offset = ($page * $limit) - $limit;

        // get ads
        $ads = $entityManager->getRepository(Ad::class)->findBy([], $orderBy, $limit, $offset);

        $arrayAds = [];
        foreach ($ads as $ad) {
            $photos = $ad->getPhotos();
            $arrayAds[] = [
                'title' => $ad->getTitle(),
                'description' => $ad->getDescription(),
                'photos' => $photos[0],
                'price' => $ad->getPrice(),
            ];
        }

        return new JsonResponse($arrayAds);
    }

    #[Route('/api/ads/{id}', methods: ['GET'])]
    public function view(Request $request, EntityManagerInterface $entityManager): Response
    {
        // get id
        $id = $request->attributes->getInt('id');
        if ($id <= 0) {
            throw new NotFoundHttpException(sprintf('Given id "%d" is not valid.', $id));
        }

        // get format
        $format = $request->query->getString('format');
        if ($format !== 'fields') {
            $format = null;
        }

        /** @var Ad|null $ad */
        $ad = $entityManager->getRepository(Ad::class)->find($id);
        if ($ad === null) {
            throw new NotFoundHttpException(sprintf('Ad with id "%d" is not found.', $id));
        }

        if ($format !== null) {
            $ad = [
                "title" => $ad->getTitle(),
                "description" => $ad->getDescription(),
                "photos" => $ad->getPhotos(),
                "price" => $ad->getPrice(),
            ];
        } else {
            $photos = $ad->getPhotos();
            $ad = [
                "title" => $ad->getTitle(),
                "photos" => $photos[0],
                "price" => $ad->getPrice(),
            ];
        }

        return new JsonResponse($ad);
    }
}

