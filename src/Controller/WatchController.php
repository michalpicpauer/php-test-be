<?php

namespace App\Controller;

use App\Exception\ManagerException;
use App\Manager\WatchManager;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations\Get;

class WatchController extends FOSRestController
{
    /**
     * @Get("/watch/{id}",
     *     requirements={"id" = "\d+"},
     * )
     *
     * @param int          $id
     * @param WatchManager $manager
     *
     * @return Response
     *
     * @throws ManagerException
     * @throws InvalidArgumentException
     */
    public function getByIdAction(int $id, WatchManager $manager, SerializerInterface $serializer)
    {
        $data = $manager->getWatchById($id);

        if (empty($data)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Watch not found");
        }

        return new Response($serializer->serialize($data, 'json'));
    }
}
