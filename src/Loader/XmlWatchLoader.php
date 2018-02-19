<?php

namespace App\Loader;

use App\Exception\XmlLoaderException;

class XmlWatchLoader
{
    /** @var string */
    private $watchSource;

    public function __construct(string $watchSource)
    {
        $this->watchSource = $watchSource;
    }

    /**
     * @param string $watchIdentification
     *
     * @return array|null
     *
     * @throws XmlLoaderException May be thrown on a fatal error, such as XML file containing data of watches
     * could not be loaded or parsed.
     */
    public function loadByIdFromXml(string $watchIdentification): ?array
    {
        try {
            $watchXml = simplexml_load_string(file_get_contents($this->watchSource));

            $watches = $watchXml->xpath("//Watch");

            foreach ($watches as $watchElement) {
                if ((string)$watchElement->Id == $watchIdentification) {
                    return [
                        'identification' => (int)$watchElement->Id,
                        'title'          => (string)$watchElement->Title,
                        'price'          => (int)$watchElement->Price,
                        'description'    => (string)$watchElement->Description,
                    ];
                }
            }
        } catch (\Exception $e) {
            throw new XmlLoaderException($e->getMessage());
        }

        return null;
    }
}