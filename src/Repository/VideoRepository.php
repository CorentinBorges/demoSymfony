<?php

namespace App\Repository;

use App\Entity\Figure;
use App\Entity\Video;
use App\Form\TrickFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends BaseRepository
{
    const YOUTUBE_LINK = "https://www.youtube.com/embed/";

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Video::class,$entityManager);
    }

    public function createVideo($figure,$link)
    {
        $video = new Video();
        $linkArray = preg_split('#/#', $link);
        $linkCode = $linkArray[3];
        $video
            ->setFigure($figure)
            ->setLink(self::YOUTUBE_LINK.$linkCode);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }

    public function createVideos(FormInterface $form, Figure $figure)
    {
        for ($n=1;$n<=TrickFormType::NB_VIDEO;$n++) {
            if (isset($form['video'.$n]) &&  !empty($form['video'.$n]->getData())) {
                $video = new Video();
                $linkArray=preg_split('#/#',$form['video'.$n]->getData());
                $linkCode = $linkArray[3];
                $video
                    ->setFigure($figure)
                    ->setLink(self::YOUTUBE_LINK.$linkCode);

                $this->entityManager->persist($video);
            }
        }
    }

    public function editVideo(int $id,string $link)
    {
        $video = $this->findOneBy(["id" => $id]);
        $finalLink = $this->createLink($link);
        $video->setLink($finalLink);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }

    public function deleteVideosFromTrick($trickId)
    {
        $tricksVideos = $this->findBy(["figure" => $trickId]);
        foreach ($tricksVideos as $tricksVideo) {
            $this->entityManager->remove($tricksVideo);
        }
    }

    public function createLink($link)
    {
        $linkArray=preg_split('#/#',$link);
        $linkCode = $linkArray[3];
        return self::YOUTUBE_LINK . $linkCode;
    }

    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
