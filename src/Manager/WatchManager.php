<?php

namespace App\Manager;

use App\Exception\ManagerException;
use App\Exception\MySqlRepositoryException;
use App\Exception\XmlLoaderException;
use App\Loader\XmlWatchLoader;
use App\Repository\MySqlWatchRepository;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

class WatchManager
{
    const WATCH_ITEM = 'watch_';

    /** @var LoggerInterface */
    protected $logger;

    /** @var CacheItemPoolInterface */
    protected $cacheItemPool;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var MySqlWatchRepository */
    protected $repository;

    /** @var XmlWatchLoader */
    protected $loader;

    /** @var string */
    protected $dataSource;

    /**
     * WatchManager constructor.
     * @param LoggerInterface        $logger
     * @param CacheItemPoolInterface $cacheItemPool
     * @param SerializerInterface    $serializer
     * @param MySqlWatchRepository   $repository
     * @param XmlWatchLoader         $loader
     * @param string                 $dataSource
     */
    public function __construct(
        LoggerInterface $logger,
        CacheItemPoolInterface $cacheItemPool,
        SerializerInterface $serializer,
        MySqlWatchRepository $repository,
        XmlWatchLoader $loader,
        string $dataSource
    ) {
        $this->logger = $logger;
        $this->cacheItemPool = $cacheItemPool;
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->loader = $loader;
        $this->dataSource = $dataSource;
    }


    /**
     * @param int $id
     *
     * @return array|null Returns array with data of found watch or null if watch was not found.
     *
     * @throws ManagerException Is thrown when is set unsupported slower source for data.
     * @throws InvalidArgumentException May be thrown if the key for cache item is not a legal value.
     */
    public function getWatchById(int $id): ?array
    {
        $result = null;

        $watchItem = $this->cacheItemPool->getItem(self::WATCH_ITEM . $id);

        if (!$watchItem->isHit()) {
            // if not in cache, get data from slower source a save it in cache
            switch ($this->dataSource) {
                case 'db':
                    try {
                        $mySqlWatchDTO = $this->repository->getWatchById($id);
                        $result = $mySqlWatchDTO->getArray();
                    } catch (MySqlRepositoryException $e) {
                        $this->logger->error($e->getMessage());
                        return null;
                    }
                    break;
                case 'xml':
                    try {
                        $result = $this->loader->loadByIdFromXml($id);

                        if ($result === null) {
                            return null;
                        }
                    } catch (XmlLoaderException $e) {
                        $this->logger->error($e->getMessage());
                        return null;
                    }

                    break;
                default:
                    throw new ManagerException(
                        'Data source not supported. Set parameter data_souce to \'db\' or \'xml\''
                    );
            }

            $watchItem->set($this->serializer->serialize($result, 'json'));

            $this->cacheItemPool->save($watchItem);
        } else {
            // ishit - deserialize watch to array
            $result = $this->serializer->deserialize($watchItem->get(), 'array', 'json');
        }

        return $result;
    }
}