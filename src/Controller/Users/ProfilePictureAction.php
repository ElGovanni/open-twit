<?php

namespace App\Controller\Users;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\User\ProfilePicture;
use App\Entity\User\User;
use App\ImageFilter\BoxResize;
use App\Repository\User\ProfilePictureRepository;
use DateTime;
use League\Flysystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfilePictureAction extends AbstractController
{
    public function __invoke(
        Request $request,
        ValidatorInterface $validator,
        ProfilePictureRepository $pictureRepository,
        BoxResize $boxResize,
        SymfonyFileSystem $filesystem,
        Filesystem $storage,
        string $cacheDir,
    ): ProfilePicture
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        /** @var User $user */
        $user = $this->getUser();

        $picture = new ProfilePicture();
        $picture->setFile($file);

        $violations = $validator->validate($picture);
        if (count($violations)) {
            throw new ValidationException($violations);
        }

        if ($user->getProfilePicture() !== null) {
            $pictureRepository->remove($user->getProfilePicture());
        }
        $picture->setUser($user);

        $dateNow = new DateTime();
        $path = sprintf(
            'profile_picture/%s/%s.webp',
            $dateNow->getTimestamp(),
            uniqid($user->getUsername() . '_')
        );

        $resizedData = $boxResize->applyFilter($picture->getFile(), 250, 250);
        $storage->write($path, $resizedData);

        $picture->setFileName($path);

        return $picture;
    }
}
