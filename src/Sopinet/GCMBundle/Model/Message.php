<?php

namespace Sopinet\GCMBundle\Model;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Message trait.
 *
 * Should be used inside entity, that needs to be one Message.
 */
trait Message
{
    /**
     * @var datetime
     *
     * @ORM\Column(name="dateSend", type="datetime")
     */
    protected $dateSend;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateReceieved", type="datetime")
     */
    protected $dateReceieved;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="enum('text', 'location', 'file', 'contact', 'reminder')")
     */
    protected $type;

    /**
     * @var text
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    protected $text;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * Set dateSend
     *
     * @param \DateTime $dateSend
     *
     * @return Message
     */
    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    /**
     * Get dateSend
     *
     * @return \DateTime
     */
    public function getDateSend()
    {
        return $this->dateSend;
    }

    /**
     * Set dateReceieved
     *
     * @param \DateTime $dateReceieved
     *
     * @return Message
     */
    public function setDateReceieved($dateReceieved)
    {
        $this->dateReceieved = $dateReceieved;

        return $this;
    }

    /**
     * Get dateReceieved
     *
     * @return \DateTime
     */
    public function getDateReceieved()
    {
        return $this->dateReceieved;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Message
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString() {
        return $this->getText();
    }
}