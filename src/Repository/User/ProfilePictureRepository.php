<?php

namespace App\Repository\User;

use App\Entity\User\ProfilePicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfilePictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilePicture::class);
    }

    public function remove(ProfilePicture $profilePicture): void
    {
        if(null === $this->find($profilePicture->getId())) {
            return;
        }

        $this->_em->remove($profilePicture);
        $this->_em->flush();
    }
}
