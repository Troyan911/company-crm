<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;

abstract class BaseController extends AbstractController
{
    protected readonly object $service;
    protected readonly object $transformer;

    protected function init(object $service, object $transformer): void
    {
        $this->service = $service;
        $this->transformer = $transformer;
    }

    protected function paginatedList(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));

        /** @var Pagerfanta $pager */
        $pager = $this->service->listPaginated($page, $limit);

        $data = array_map(
            fn($c) => $this->transformer->toOutputDTO($c),
            iterator_to_array($pager->getCurrentPageResults())
        );

        return $this->json([
            'data' => $data,
            'meta' => [
                'page' => $pager->getCurrentPage(),
                'pages' => $pager->getNbPages(),
                'total' => $pager->getNbResults(),
                'limit' => $limit,
            ]
        ]);
    }
}
