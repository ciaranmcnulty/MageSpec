<?php

namespace MageTest\PhpSpec\MagentoExtension\CodeGenerator\Generator\Xml\Element;

use MageTest\PhpSpec\MagentoExtension\CodeGenerator\Generator\Xml\XmlGeneratorException;

class ModelElement extends SimpleElementAbstract implements ConfigElementInterface
{
    /**
     * @param string $type
     * @return boolean
     */
    public function supports($type)
    {
        return $type === 'model';
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param string $type
     * @param string $moduleName
     * @throws XmlGeneratorException
     * @return null
     */
    public function addElementToXml(\SimpleXMLElement $xml, $type, $moduleName)
    {
        $globalElements = $xml->xpath('/config/global');

        if (!count($globalElements)) {
            throw new XmlGeneratorException(sprintf('Global element not found in %s config file', $moduleName));
        }

        $modelsElement = $this->getModelsElement($globalElements[0]);

        $modelsClassContainer = $modelsElement->addChild(strtolower($moduleName));
        $modelsClassContainer->addChild('class', $moduleName . '_' . ucfirst($type));

        $resourceElementName = strtolower($moduleName).'_resource';
        if (count($modelsElement->xpath($resourceElementName))) {
            $modelsClassContainer->addChild('resourceModel', $resourceElementName);
        }
    }

    /**
     * @return \SimpleXmlElement
     */
    private function getModelsElement(\SimpleXMLElement $xml)
    {
        $modelElements = $xml->xpath('models');

        if (!count($modelElements)) {
            return $xml->addChild('models');
        }

        return $modelElements[0];
    }
}
