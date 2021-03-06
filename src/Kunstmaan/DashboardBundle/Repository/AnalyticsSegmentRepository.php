<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AnalyticsSegmentRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnalyticsSegmentRepository extends EntityRepository
{
    /**
     * Get a segment.
     *
     * @param int $id
     *
     * @return AnalyticsOverview|bool
     */
    public function deleteSegment($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('KunstmaanDashboardBundle:AnalyticsSegment', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);

        $results = $qb->getQuery()->getResult();
        if ($results) {
            $em->remove($results[0]);
            $em->flush();
        }
    }

    /**
     * Initialise a segment by adding new overviews if they don't exist yet.
     *
     * @param AnalyticsSegment $segment
     * @param int              $configId
     */
    public function initSegment($segment, $configId = false)
    {
        if (!count($segment->getOverviews()->toArray())) {
            if ($configId) {
                $config = $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->find($configId);
            } else {
                $config = $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst();
            }
            $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->addOverviews($config, $segment);
        }
    }
}
