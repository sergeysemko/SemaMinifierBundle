<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sema\Bundle\MinifierBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Assetic\Filter\MinifyCssCompressorFilter;
use Assetic\Filter\JSMinPlusFilter;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AssetsCombineCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('assets:combine')
            ->setDefinition(array(
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
            ))
            ->setDescription('Installs bundles web assets under a public web directory')
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('kernel')->getRootDir();
        $targetArg = rtrim($input->getArgument('target'), '/').'/';
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $targetDir = $rootDir . '/../'.$targetArg;
        $confDir = $rootDir.'/config';
        if (!is_dir($targetDir)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        if (!file_exists($confDir . '/assets.json')) {
            throw new \InvalidArgumentException('The file '.$confDir.'/assets.json does not exist.');
        }

        $output->writeln("Combining assets");
        $assetsFiles = json_decode(file_get_contents($confDir . '/assets.json'), true);
        foreach ($assetsFiles as $type => $combined) {
            foreach ($combined as $name => $files) {
                file_put_contents($targetDir.$files['output'], '');
                $this->assets[$type][$name] = (array)($files['input']);
                foreach($this->assets[$type][$name] as $value) {
                    file_put_contents($targetDir.$files['output'], file_get_contents($targetDir.$value), FILE_APPEND);
                }

                $file = $targetDir.$files['output'];
                if (is_file($file)) {
                    $f = new File($file);
                    switch ($f->getExtension()) {
                        case 'css':
                            $resource = new AssetCollection(
                                array(
                                        new FileAsset($file)
                                     ),
                                array(
                                        new MinifyCssCompressorFilter()
                                    )
                            );
                            $resource->load();
                            file_put_contents($file, $resource->dump());
                            $output->writeln(sprintf('File <comment>%s</comment> was combined and minified', $file));
                            break;
                        case 'js':
                            $resource = new AssetCollection(
                                array(
                                        new FileAsset($file)
                                     ),
                                array(
                                        new JSMinPlusFilter()
                                    )
                            );
                            $resource->load();
                            file_put_contents($file, $resource->dump());
                            $output->writeln(sprintf('File <comment>%s</comment> was combined and minified', $file));
                            break;
                    }
                }
            }
        }
    }
}
