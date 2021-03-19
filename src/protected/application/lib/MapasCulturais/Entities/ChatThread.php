<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\App;
use MapasCulturais\Traits;
use MapasCulturais\UserInterface;

/**
 * ChatThread
 *
 * @property-read int $id
 * @property \MapasCulturais\Entity $object the owner of this chat thread
 * @property string $identifier
 * @property string $description
 * @property-read \DateTime $createTimestamp
 * @property \DateTime $lastMessageTimestamp
 *
 * @ORM\Table(name="chat_thread")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 * @ORM\HasLifecycleCallbacks
 */
class ChatThread extends \MapasCulturais\Entity
{
    use Traits\EntityAgentRelation;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="chat_thread_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     */
    protected $objectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="object_type", type="object_type", length=255, nullable=false)
     */
    protected $objectType;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", nullable=false)
     */
    protected $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_message_timestamp", type="datetime", nullable=true)
     */
    protected $lastMessageTimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status;

    /**
     * @var \MapasCulturais\Entities\ChatThreadAgentRelation[] Agent Relations
     *
     * @ORM\OneToMany(targetEntity="MapasCulturais\Entities\ChatThreadAgentRelation", mappedBy="owner", cascade="remove", orphanRemoval=true)
     * @ORM\JoinColumn(name="id", referencedColumnName="object_id", onDelete="CASCADE")
    */
    protected $__agentRelations;

    protected $_ownerEntity;

    public function canUser($action, $userOrAgent=null)
    {
        $app = App::i();
        if (!$app->isAccessControlEnabled()) {
            return true;
        }
        if (is_null($userOrAgent)) {
            $user = $app->user;
        } else if ($userOrAgent instanceof UserInterface) {
            $user = $userOrAgent;
        } else {
            $user = $userOrAgent->getOwnerUser();
        }
        if (($action == "@control") &&
            $this->getOwnerEntity()->canUser("@control")) {
            return true;
        }
        return parent::canUser($action, $user);
    }

    public function getOwner()
    {
        return $this->getOwnerEntity()->owner;
    }

    /**
     * Returns the owner entity of this chat thread.
     * @return \MapasCulturais\Entity
     */
    public function getOwnerEntity()
    {
        if (!$this->_ownerEntity && ($this->objectType && $this->objectId)) {
            $repo = App::i()->repo((string) $this->objectType);
            $this->_ownerEntity = $repo->find($this->objectId);
        }
        return $this->_ownerEntity;
    }

    public function getParticipants()
    {
        $participants = [
            "owner" => [$this->owner->user],
            "admin" => $this->getUsersWithControl()
        ];
        $agent_relations = array_values($this->getAgentRelations());
        $participants = array_reduce($agent_relations,
                                      function ($previous, $relation) {
            $current = $previous;
            if (!isset($current[$relation->group])) {
                $current[$relation->group] = [];
            }
            if (!in_array($relation->agent->user,
                          $current[$relation->group])) {
                $current[$relation->group][] = $relation->agent->user;
            }
            return $current;
        }, $participants);
        return $participants;
    }

    public function sendNotifications(ChatMessage $message)
    {
        self::registeredType($this->type)->sendNotifications($message);
        return;
    }

    function setType(string $slug)
    {
        self::registeredType($slug);
        $this->type = $slug;
        return;
    }

    static private function registeredType($slug)
    {
        $registered = App::i()->getRegisteredChatThreadType($slug);
        if (!isset($registered)) {
            throw new \Exception("{$slug} is not a registered chat thread " .
                                 "type.");
        }
        return $registered;
    }

    /**
     * Chats have admins (@control) and post permissions.
     * Users that can post are admins or the user of a related agent.
     */
    protected function canUserPost($user)
    {
        if ($this->status != self::STATUS_ENABLED) {
            return false;
        }
        return $this->canUserView($user);
    }

    protected function canUserView($user)
    {
        if ($this->canUser("@control")) {
            return true;
        }
        foreach ($this->getAgentRelations() as $relation) {
            if ($user->id == $relation->agent->user->id) {
                return true;
            }
        }
        return false;
    }

    //============================================================= //
    // The following lines ara used by MapasCulturais hook system.
    // Please do not change them.
    // ============================================================ //

    /** @ORM\PrePersist */
    public function prePersist($args=null) { parent::prePersist($args); }
    /** @ORM\PostPersist */
    public function postPersist($args=null) { parent::postPersist($args); }

    /** @ORM\PreRemove */
    public function preRemove($args=null) { parent::preRemove($args); }
    /** @ORM\PostRemove */
    public function postRemove($args=null) { parent::postRemove($args); }

    /** @ORM\PreUpdate */
    public function preUpdate($args=null) { parent::preUpdate($args); }
    /** @ORM\PostUpdate */
    public function postUpdate($args=null) { parent::postUpdate($args); }
}