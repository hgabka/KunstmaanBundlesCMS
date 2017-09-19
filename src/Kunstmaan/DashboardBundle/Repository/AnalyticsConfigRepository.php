<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;

/**
 * AnalyticsConfigRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnalyticsConfigRepository extends EntityRepository
{
    /**
     * Get the first config from the database, creates a new entry if the config doesn't exist yet.
     *
     * @param mixed $createNew
     *
     * @return AnalyticsConfig $config
     */
    public function findFirst($createNew = true)
    {
        // Backwards compatibility: select the first config, still used in the dashboard, specified config ids are set in the dashboard collection bundle
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM KunstmaanDashboardBundle:AnalyticsConfig c');
        $result = $query->getResult();
        // if no configs exist, create a new one
        if (!$result && $createNew) {
            return $this->createConfig();
        } elseif ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * Get the default overviews for a config.
     *
     * @param mixed $config
     *
     * @return AnalyticsConfig $config
     */
    public function findDefaultOverviews($config)
    {
        $em = $this->getEntityManager();

        return $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')
            ->findBy([
                    'config' => $config,
                    'segment' => null,
                ]);
    }

    /**
     * Create a new config.
     *
     * @return AnalyticsConfig
     */
    public function createConfig()
    {
        $em = $this->getEntityManager();

        $config = new AnalyticsConfig();
        $em->persist($config);
        $em->flush();

        $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->addOverviews($config);

        return $config;
    }

    /**
     * Flush a config.
     *
     * @param int $id the config id
     */
    public function flushConfig($id = false)
    {
        $em = $this->getEntityManager();

        // Backward compatibilty to flush overviews without a config set
        if (!$id) {
            $overviewRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
            foreach ($overviewRepository->findAll() as $overview) {
                $em->remove($overview);
            }
            $em->flush();

            return;
        }

        $config = $id ? $this->find($id) : $this->findFirst();
        foreach ($config->getOverviews() as $overview) {
            $em->remove($overview);
        }
        foreach ($config->getSegments() as $segment) {
            $em->remove($segment);
        }
        $em->flush();
    }

    /**
     * Update the timestamp when data is collected.
     *
     * @param int id
     * @param mixed $id
     */
    public function setUpdated($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setLastUpdate(new \DateTime());
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the token.
     *
     * @param string $token
     * @param mixed  $id
     */
    public function saveToken($token, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setToken($token);
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the property id.
     *
     * @param string $propertyId
     * @param mixed  $id
     */
    public function savePropertyId($propertyId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setPropertyId($propertyId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the account id.
     *
     * @param string $accountId
     * @param mixed  $id
     */
    public function saveAccountId($accountId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setAccountId($accountId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the profile id.
     *
     * @param string $profileId
     * @param mixed  $id
     */
    public function saveProfileId($profileId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setProfileId($profileId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the config name.
     *
     * @param string $profileId
     * @param mixed  $name
     * @param mixed  $id
     */
    public function saveConfigName($name, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setName($name);
        $em->persist($config);
        $em->flush();
    }

    /**
     * Resets the profile id.
     *
     * @param int id
     * @param mixed $id
     */
    public function resetProfileId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setProfileId('');
        $em->persist($config);
        $em->flush();
    }

    /**
     * Resets the  account id, property id and profile id.
     *
     * @param int id
     * @param mixed $id
     */
    public function resetPropertyId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setAccountId('');
        $config->setProfileId('');
        $config->setPropertyId('');
        $em->persist($config);
        $em->flush();
    }
}
