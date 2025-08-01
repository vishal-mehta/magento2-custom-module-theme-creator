<?php

namespace Vishal\ModuleCreator\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Helper class for NewModule
 */
class Data extends AbstractHelper
{
    public const MODULE_FOLDER_NAME = "MagentoModule";
    public const THEME_FOLDER_NAME = "MagentoTheme";
    public const SOURCE_FOLDER_NAME = "Source";
    public const TEMPLATE_FOLDER_NAME = "template";

    /**
     * @var string
     */
    protected $destinationPath = '';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readDirectoryFactory;

    /**
     * @var Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $shell;

    /**
     * @var string
     */
    protected $vendorName;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var string
     */
    protected $themeName;

    /**
     * @var string
     */
    protected $themeTitle;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param File $file
     * @param Reader $reader
     * @param ReadFactory $readDirectoryFactory
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        File $file,
        Reader $reader,
        ReadFactory $readDirectoryFactory,
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->reader = $reader;
        $this->readDirectoryFactory = $readDirectoryFactory;
        parent::__construct($context);
    }

    /**
     * Get a current module path
     *
     * @param string $isModule
     * @return string
     */
    public function getCurrentModulePath($isModule = "module")
    {
        $currentModuleName =  \Magento\Framework\App\Helper\AbstractHelper::_getModuleName();
        if ($isModule == "module") {
            return $this->reader->getModuleDir('', $currentModuleName) .
                DIRECTORY_SEPARATOR . self::SOURCE_FOLDER_NAME;
        } else {
            return $this->reader->getModuleDir('', $currentModuleName) .
                DIRECTORY_SEPARATOR . self::TEMPLATE_FOLDER_NAME;
        }
    }

    /**
     * Change a file path
     *
     * @param mixed|array $data
     * @param mixed|string $item
     * @param bool $small
     * @return array|string|string[]
     */
    public function changeFilePathWithOriginalNamespace($data, $item, $small = false)
    {
        $moduleNameFormatted = $this->getModuleNameFormatted($data);

        /** @var for $item Module */
        $item = str_replace(
            "Module",
            $moduleNameFormatted['capModule'],
            str_replace("Namespace", $moduleNameFormatted['capNamespace'], $item)
        );

        /** @var for $item Theme */
        $item = str_replace("Storefront", $moduleNameFormatted['lowModule'], $item);

        if ($small) {
            /** @var for $item module */
            $item = str_replace("module", $moduleNameFormatted['lowModule'], $item);
        }

        return $item;
    }

    /**
     * Create a new module
     *
     * @param mixed|array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function createModule($data)
    {
        $this->destinationPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $this->file->checkAndCreateFolder($this->destinationPath . self::MODULE_FOLDER_NAME);

        $sourcePath = $this->getCurrentModulePath("module");

        $scanDir = $this->readDirectoryFactory->create($sourcePath)->readRecursively();
        foreach ($scanDir as $index => $item) {
            if (count(explode(".", $item)) > 1) {
                $scanDir[$index] = DIRECTORY_SEPARATOR . $item;
            } else {
                unset($scanDir[$index]);
            }
        }
        $sourceFileList = array_values($scanDir);

        $destinationFileList = [];
        foreach ($sourceFileList as $index => $item) {
            $destinationFileList[] = $this->changeFilePathWithOriginalNamespace($data, $item);
        }

        $this->copyFilesToDestination($data, $sourceFileList, $destinationFileList, "module");

        $this->updateVariables($data, $destinationFileList);

        $this->changeFileName($data, $this->destinationPath);
    }

    /**
     * Create a new theme
     *
     * @param mixed|array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function createTheme($data)
    {
        $this->destinationPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $this->file->checkAndCreateFolder($this->destinationPath . self::THEME_FOLDER_NAME);

        $sourcePath = $this->getCurrentModulePath("theme");

        $scanDir = $this->readDirectoryFactory->create($sourcePath)->readRecursively();
        foreach ($scanDir as $index => $item) {
            if (count(explode(".", $item)) > 1) {
                $scanDir[$index] = DIRECTORY_SEPARATOR . $item;
            } else {
                unset($scanDir[$index]);
            }
        }
        $sourceFileList = array_values($scanDir);

        $destinationFileList = [];
        foreach ($sourceFileList as $index => $item) {
            $destinationFileList[] = $this->changeFilePathWithOriginalNamespace($data, $item, true);
        }

        $this->copyFilesToDestination($data, $sourceFileList, $destinationFileList, "theme");

        $this->updateVariables($data, $destinationFileList);
    }

    /**
     * Change file names with original name
     *
     * @param mixed|array $data
     * @param string $destinationPath
     * @return void
     */
    public function changeFileName($data, $destinationPath)
    {
        $fileList = [];
        $fileList[] = "/app/code/Namespace/Module/view/adminhtml/layout/module_index_index.xml";
        $fileList[] = "/app/code/Namespace/Module/view/adminhtml/layout/module_index_edit.xml";
        $fileList[] = "/app/code/Namespace/Module/view/adminhtml/layout/module_index_new.xml";
        $fileList[] = "/app/code/Namespace/Module/view/adminhtml/ui_component/module_index_listing.xml";
        $fileList[] = "/app/code/Namespace/Module/view/adminhtml/ui_component/module_form.xml";
        $fileList[] = "/app/code/Namespace/Module/view/frontend/layout/module_index_index.xml";
        $fileList[] = "/app/code/Namespace/Module/view/frontend/layout/module_index_view.xml";
        $fileList[] = "/app/code/Namespace/Module/view/frontend/templates/module.phtml";

        $fileLists = [];
        foreach ($fileList as $index => $item) {
            $item = $this->changeFilePathWithOriginalNamespace($data, $item);
            $_item = $this->changeFilePathWithOriginalNamespace($data, $item, true);

            $fileLists[$index]['source'] = $destinationPath . $item;
            $fileLists[$index]['target'] = $destinationPath . $_item;

            $this->file->mv($destinationPath . $item, $destinationPath . $_item);
        }
    }

    /**
     * Copy files to destination folder
     *
     * @param mixed|array $data
     * @param mixed|array $sourceFileList
     * @param mixed|array $destinationFileList
     * @param string $isModule
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function copyFilesToDestination($data, $sourceFileList, $destinationFileList, $isModule = "module")
    {
        $sourcePath = $this->getCurrentModulePath($isModule);

        if ($isModule == "module") {
            $this->destinationPath = $this->destinationPath . self::MODULE_FOLDER_NAME;
        } else {
            $this->destinationPath = $this->destinationPath . self::THEME_FOLDER_NAME;
        }

        foreach ($destinationFileList as $file) {
            $newPath = substr($file, 0, strrpos($file, '/'));
            $this->createFolderStructure($newPath, $this->destinationPath);
        }

        foreach ($destinationFileList as $index => $file) {
            $this->file->cp(
                $sourcePath . $sourceFileList[$index],
                $this->destinationPath . DIRECTORY_SEPARATOR . $file
            );
        }
    }

    /**
     * Create a folder structure
     *
     * @param string $newPath
     * @param string $destinationPath
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createFolderStructure($newPath, $destinationPath)
    {
        $folders = explode('/', $newPath);
        $current = $destinationPath;

        foreach ($folders as $folder) {
            $currentFolder = $current . DIRECTORY_SEPARATOR . $folder;
            $this->file->checkAndCreateFolder($currentFolder);
            $current = $currentFolder;
        }
    }

    /**
     * Update variable into the file
     *
     * @param mixed|array $data
     * @param mixed|array $destinationFileList
     * @return void
     */
    public function updateVariables($data, $destinationFileList)
    {
        foreach ($destinationFileList as $file) {
            $fileContent = "";
            $currentFile =  $this->destinationPath . DIRECTORY_SEPARATOR . $file;
            $fileContent = $this->file->read($currentFile);
            $fileExtension = explode('.', $file);
            switch ($fileExtension[1]) {
                case "xml":
                    $fileContent = $this->replaceXMLVariables($data, $fileContent);
                    break;
                case "php":
                case "phtml":
                    $fileContent = $this->replacePHPVariables($data, $fileContent);
                    break;
                default:
                    break;
            }
            if (!empty($fileContent)) {
                $this->file->write($currentFile, $fileContent);
            }
        }
    }

    /**
     * Get formatted module name
     *
     * @param mixed|array $data
     * @return array
     */
    public function getModuleNameFormatted($data)
    {
        return [
            "capNamespace" => ucfirst($data['namespace']),
            "lowNamespace" => strtolower($data['namespace']),
            "capModule" => ucfirst($data['module']),
            "capsModule" => strtoupper($data['module']),
            "lowModule" => strtolower($data['module']),
            "themeTitle" => (isset($data['theme_title'])) ? ucfirst($data['theme_title']) : ""
        ];
    }

    /**
     * Replace xml variable
     *
     * @param mixed|array $data
     * @param string $fileContent
     * @return array|string|string[]|null
     */
    public function replaceXMLVariables($data, $fileContent)
    {
        $moduleNameFormatted = $this->getModuleNameFormatted($data);

        $search = [
            '/\[Namespace\]/',
            '/\[namespace\]/',
            '/\[Module\]/',
            '/\[module\]/',
            '/\[MODULE\]/',
            '/\[THEME_TITLE\]/'
        ];

        $replace = [
            $moduleNameFormatted['capNamespace'],
            $moduleNameFormatted['lowNamespace'],
            $moduleNameFormatted['capModule'],
            $moduleNameFormatted['lowModule'],
            $moduleNameFormatted['capsModule'],
            $moduleNameFormatted['themeTitle']
        ];

        return preg_replace($search, $replace, $fileContent);
    }

    /**
     * Replace php variables
     *
     * @param mized|array $data
     * @param string $fileContent
     * @return array|string|string[]|null
     */
    public function replacePHPVariables($data, $fileContent)
    {
        $moduleNameFormatted = $this->getModuleNameFormatted($data);

        $search = [
            '/<Namespace>/',
            '/<namespace>/',
            '/<Module>/',
            '/<module>/',
            '/<MODULE>/'
        ];

        $replace = [
            $moduleNameFormatted['capNamespace'],
            $moduleNameFormatted['lowNamespace'],
            $moduleNameFormatted['capModule'],
            $moduleNameFormatted['lowModule'],
            $moduleNameFormatted['capsModule']
        ];

        return preg_replace($search, $replace, $fileContent);
    }

    /**
     * Sanitize parameter value
     *
     * @param string $name
     * @param bool $allowSpace
     * @return array|string|string[]|null
     */
    public function sanitizeModuleName($name, $allowSpace = false)
    {
        if ($allowSpace) {
            return preg_replace("/[^A-Za-z\s]+/", "", $name);
        } else {
            return preg_replace("/[^A-Za-z]+/", "", $name);
        }
    }

    /**
     * Get module structure base path
     *
     * @param string $destinationPath
     * @return string
     */
    public function getModuleBasePath($destinationPath)
    {
        return $destinationPath . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "code" .
            DIRECTORY_SEPARATOR . $this->vendorName . DIRECTORY_SEPARATOR . $this->moduleName;
    }

    /**
     * Get theme template base path
     *
     * @param string $destinationPath
     * @return string
     */
    public function getThemeBasePath($destinationPath)
    {
        return $destinationPath . DIRECTORY_SEPARATOR . $this->vendorName . DIRECTORY_SEPARATOR . $this->themeName;
    }

    /**
     * Start module creation command
     *
     * @param SymfonyStyle $shell
     * @param string $destinationPath
     * @return false|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function createModuleCommand(SymfonyStyle $shell, $destinationPath)
    {
        $this->shell = $shell;
        $this->vendorName = $this->shell->ask('Your module Vendor name:', '');
        $this->vendorName = ucfirst(strtolower($this->sanitizeModuleName($this->vendorName)));
        if (empty($this->vendorName)) {
            $this->shell->warning('Cannot create a vendor with empty name');
            return false;
        }
        $this->moduleName = $this->shell->ask('Your Module name:', '');
        $this->moduleName = ucfirst(strtolower($this->sanitizeModuleName($this->moduleName)));
        if (empty($this->moduleName)) {
            $this->shell->warning('Cannot create a new module with empty name');
            return false;
        }

        $this->shell->table(['Your data', ''], [
            ['vendor', $this->vendorName],
            ['module', $this->moduleName],
            ['path', $this->getModuleBasePath($destinationPath)]
        ]);
        if (!$this->shell->confirm('Do you wish to continue?', 'Y')) {
            $this->shell->warning('Exiting without creating module...');
            return false;
        }
        $this->shell->text('Creating module...');
        $data = [
            "namespace" => $this->vendorName,
            "module" => $this->moduleName
        ];
        $this->createModule($data);
        $this->shell->success('Your new module is ready at ' . $this->getModuleBasePath($destinationPath));
    }

    /**
     * Start theme creation
     *
     * @param SymfonyStyle $shell
     * @param string $destinationPath
     * @return false|void
     */
    public function createThemeCommand(SymfonyStyle $shell, $destinationPath)
    {
        $this->shell = $shell;
        $this->vendorName = $this->shell->ask('Your theme Vendor name:', '');
        $this->vendorName = ucfirst(strtolower($this->sanitizeModuleName($this->vendorName)));
        if (empty($this->vendorName)) {
            $this->shell->warning('Cannot create a vendor with empty name');
            return false;
        }
        $this->themeName = $this->shell->ask('Your new Theme name:', '');
        $this->themeName = strtolower($this->sanitizeModuleName($this->themeName));
        if (empty($this->themeName)) {
            $this->shell->warning('Cannot create a new theme folder with empty name');
            return false;
        }

        $this->themeTitle = $this->shell->ask('Your new Theme title:', '');
        $this->themeTitle = $this->sanitizeModuleName($this->themeTitle, true);
        if (empty($this->themeTitle)) {
            $this->shell->warning('Cannot create a new theme folder with empty name');
            return false;
        }

        $this->shell->table(['Your data', ''], [
            ['vendor', $this->vendorName],
            ['theme', $this->themeName],
            ['theme_title', $this->themeTitle],
            ['path', $this->getThemeBasePath($destinationPath)]
        ]);

        if (!$this->shell->confirm('Do you wish to continue?', 'Y')) {
            $this->shell->warning('Exiting without creating theme...');
            return false;
        }
        $this->shell->text('Creating theme...');
        $data = [
            "namespace" => $this->vendorName,
            "theme" => $this->themeName,
            "module" => $this->themeName,
            "theme_title" => $this->themeTitle
        ];
        $this->createTheme($data);
        $this->shell->success('Your new theme is ready at ' . $this->getThemeBasePath($destinationPath));
    }
}
