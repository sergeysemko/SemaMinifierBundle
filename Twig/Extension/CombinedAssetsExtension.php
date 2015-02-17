<?php

namespace Sema\Bundle\MinifierBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CombinedAssetsExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $assets = array();

    protected $container;

    /**
     * @param ContainerInterface $container
     * @param bool $debug
     */
    public function __construct(ContainerInterface $container, $debug)
    {
        $this->container = $container;
        $confDir = $this->container->get('kernel')->getRootDir().'/config';
        if (!file_exists($confDir . '/assets.json')) {
            throw new \RuntimeException('The file '.$confDir.'/assets.json does not exist.');
        }

        $assetsFiles = json_decode(file_get_contents($confDir . '/assets.json'), true);
        foreach ($assetsFiles as $type => $combined) {
            foreach ($combined as $name => $files) {
                $this->assets[$type][$name] = (array)($debug ? $files['input'] : $files['output']);
            }
        }
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array('assets' => $this->assets);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'combined_assets';
    }
}
