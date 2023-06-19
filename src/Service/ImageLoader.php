<?php

namespace App\Service;

use App\Repository\ThemeRepository;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ImageLoader
{
    /**
     * @var int
     */
    private int $fileSizeLimit = 2 * 1024 * 1024; // 2Mb

    /**
     * @var array
     */
    private array $allowedTypes  = [
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $containerBag;
    /**
     * @var ThemeRepository
     */
    private ThemeRepository $themeRepository;

    public function __construct(ContainerBagInterface $containerBag, ThemeRepository $themeRepository)
    {
        $this->containerBag = $containerBag;
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param array $files
     * @param string $themeCode
     * @return array
     */
    public function load(array $files, string $themeCode): array
    {
        $themeEntity = $this->themeRepository->findOneBy(['code' => $themeCode]);
        $uploaddir = sprintf($this->containerBag->get('IMAGE_PATH'), $themeEntity->getName());
        $images = $files['images'];
        $imagePaths = [];

        if (count($images['name']) > 5) {
            throw new Exception('Максимальное количество загружаемых файлов - 5');
        }

        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['error'][$i] !== 0) {
                throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
            }

            $destName = $uploaddir . $images['name'][$i];

            if (!$this->moveFile($images['tmp_name'][$i], $destName)) {
                throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
            }

            if (!$this->validateFile($images['type'][$i], $images['size'][$i], $this->fileSizeLimit, $this->allowedTypes)) {
                $this->removeFile($destName);
                throw new Exception('При загрузке ' . $i + 1 .  ' файла произошла ошибка');
            }
            $imagePaths[] = 'build/images/themes/' . $themeEntity->getName() . '/' . $images['name'][$i];
        }

        return $imagePaths;
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    private function moveFile(string $from, string $to): bool
    {
        return move_uploaded_file($from, $to);
    }

    /**
     * @param string $name
     * @return bool
     */
    private function removeFile(string $name): bool
    {
        return unlink($name);
    }

    /**
     * @param string $type
     * @param int $filesize
     * @param int $size
     * @param array $typeList
     * @return bool
     */
    private function validateFile(string $type, int $fileSize, int $size, array $typeList): bool
    {
        if (!$this->validateFileSize($fileSize, $size)) {
            return false;
        }

        return $this->validateFileType($type, $typeList);
    }

    /**
     * @param int $filesize
     * @param int $maxSizeLimit
     * @return bool
     */
    private function validateFileSize(int $fileSize, int $maxSizeLimit): bool
    {
        return $fileSize <= $maxSizeLimit;
    }

    /**
     * @param string $type
     * @param array $types
     * @return bool
     */
    private function validateFileType(string $type, array $types): bool
    {
        return in_array(
            $type,
            $types,
            true
        );
    }
}
