<?php

namespace Vishal\ModuleCreator\Console;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem;
use Vishal\ModuleCreator\Helper\Data as ModuleCreatorHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Command to create new module
 */
class CreateModule extends Command
{
    public const NAMESPACE = 'namespace';

    public const MODULE = 'module';

    /**
     * @var ModuleCreatorHelper
     */
    protected $moduleCreatorHelper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param ModuleCreatorHelper $moduleCreatorHelper
     * @param Filesystem $filesystem
     */
    public function __construct(
        ModuleCreatorHelper $moduleCreatorHelper,
        Filesystem $filesystem
    ) {
        $this->moduleCreatorHelper = $moduleCreatorHelper;
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('module-creator:create')
            ->setDescription('Create a new module')
            ->setDefinition($this->getInputList());

        parent::configure();
    }

    /**
     * Get a list of options and arguments for the command
     *
     * @return mixed
     */
    public function getInputList()
    {
        return [
            new InputOption(
                self::NAMESPACE,
                null,
                InputOption::VALUE_REQUIRED,
                'Namespace for the module '
            ),
            new InputOption(
                self::MODULE,
                null,
                InputOption::VALUE_REQUIRED,
                'Module name '
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getOption(self::NAMESPACE);
        $module = $input->getOption(self::MODULE);
        $proceed = false;
        if ($namespace && $module) {
            $proceed = true;
        }
        if ($proceed) {
            $destinationPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath() . ModuleCreatorHelper::MODULE_FOLDER_NAME;
            $data = [
                "namespace" => $namespace,
                "module" => $module
            ];
            try {
                $this->moduleCreatorHelper->createModule($data);
            } catch (ValidatorException $e) {
                $output->writeln(__($e->getMessage()));

            } catch (LocalizedException $e) {
                $output->writeln(__($e->getMessage()));
            }

            $output->writeln(__('You saved the module. Please check the module under path, ' . $destinationPath));
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } else {
            if (!$namespace) {
                $output->writeln("Please provide namespace");
            }
            if (!$module) {
                $output->writeln("Please provide module name");
            }
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
