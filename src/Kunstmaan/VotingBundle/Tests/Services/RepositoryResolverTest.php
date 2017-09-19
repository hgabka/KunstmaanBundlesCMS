<?php

namespace Kunstmaan\VotingBundle\Tests\Services;

use Kunstmaan\VotingBundle\Services\RepositoryResolver;

/**
 * Unit test for repository resolver.
 *
 * @coversNothing
 */
class RepositoryResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataRepositoryEvent
     *
     * @param mixed $event
     * @param mixed $repositoryname
     */
    public function testGetRepositoryForEvent($event, $repositoryname)
    {
        $mockEm = $this->getMock('Doctrine\ORM\EntityManager', ['getRepository'], [], 'MockedEm', false);

        $mockEm->expects($this->once())
                 ->method('getRepository')
                 ->with($this->equalTo($repositoryname));

        $resolver = new RepositoryResolver($mockEm);

        $resolver->getRepositoryForEvent($event);
    }

    public function dataRepositoryEvent()
    {
        return [
            [$this->getMock('\Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent', [], [], 'MockDownVoteEvent', false), 'Kunstmaan\VotingBundle\Entity\UpDown\DownVote'],
            [$this->getMock('\Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent', [], [], 'MockUpVoteEvent', false), 'Kunstmaan\VotingBundle\Entity\UpDown\UpVote'],
            [$this->getMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent', [], [], 'MockFacebookLikeEvent', false), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike'],
            [$this->getMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent', [], [], 'MockFacebookSendEvent', false), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend'],
            [$this->getMock('\Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent', [], [], 'MockLinkedInShareEvent', false), 'Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare'],
        ];
    }
}
