<?php

namespace Vishal\ModuleCreator\Console;

use Vishal\ModuleCreator\Helper\Data as ModuleCreatorHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModuleCreator extends Command
{
    public const TYPE_MODULE = "module";
    public const TYPE_THEME = "frontend theme";

    /**
     * @var ModuleCreatorHelper
     */
    protected $moduleCreatorHelper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $shell;

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
        $this->setName('module-creator:creator')
            ->setDescription('Create a new module using shell')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->shell = new SymfonyStyle($input, $output);

        /*$this->shell->progressStart(10);
        for ($i = 0; $i < 10; $i++) {
            // simulate some work
            sleep(1);
            $this->shell->text('Creating theme...');
            // advance the progress bar 1 step
            $this->shell->progressAdvance();
        }
        $this->shell->progressFinish();*/

        $scaffolderType = $this->shell->choice(
            'Select what do you want to generate',
            [self::TYPE_MODULE, self::TYPE_THEME],
            0
        );
        switch ($scaffolderType) {
            case self::TYPE_MODULE:
                $destinationPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath() . ModuleCreatorHelper::MODULE_FOLDER_NAME;
                $this->moduleCreatorHelper->createModuleCommand($this->shell, $destinationPath);
                break;
            case self::TYPE_THEME:
                $destinationPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath() . ModuleCreatorHelper::THEME_FOLDER_NAME;
                $this->moduleCreatorHelper->createThemeCommand($this->shell, $destinationPath);
                break;
        }
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
