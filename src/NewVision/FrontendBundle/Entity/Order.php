<?php
/**
 * This file is part of the NewVisionAirportsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use NewVision\PublishWorkflowBundle\PublishWorkflowTrait;
use NewVision\PublishWorkflowBundle\PublishWorkflowInterface;
use NewVision\SEOBundle\SeoAwareInterface;
use NewVision\SEOBundle\SeoAwareTrait;

/**
 * Airport's entity
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 * @Gedmo\Loggable
 *
 * @package NewVisionAirportsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class Order
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="no", type="integer", nullable=true)
     */
    protected $no;

     /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="distance", type="string", length=255, nullable=true)
     */
    protected $distance;


    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="from_place", type="string", length=255, nullable=true)
     */
    protected $from;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="to_place", type="string", length=255, nullable=true)
     */
    protected $to;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="string", length=255, nullable=true)
     */
    protected $date;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="return_date", type="string", length=255, nullable=true)
     */
    protected $return_date;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="start_time", type="string", length=255, nullable=true)
     */
    protected $start_time;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="return_time", type="string", length=255, nullable=true)
     */
    protected $return_time;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="offer", type="string", length=255, nullable=true)
     */
    protected $offer;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="from_lat", type="string", length=255, nullable=true)
     */
    protected $from_lat;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="from_lng", type="string", length=255, nullable=true)
     */
    protected $from_lng;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="to_lat", type="string", length=255, nullable=true)
     */
    protected $to_lat;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="to_lng", type="string", length=255, nullable=true)
     */
    protected $to_lng;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="via_lat", type="string", length=255, nullable=true)
     */
    protected $via_lat;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="via_lng", type="string", length=255, nullable=true)
     */
    protected $via_lng;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="duration", type="string", length=255, nullable=true)
     */
    protected $duration;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="offer_point", type="string", length=255, nullable=true)
     */
    protected $offer_point;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="via", type="string", length=255, nullable=true)
     */
    protected $via;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="passengers", type="string", length=255, nullable=true)
     */
    protected $passengers;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="payment_type", type="string", length=255, nullable=true)
     */
    protected $paymentType;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="payment_status", type="string", length=255, nullable=true)
     */
    protected $paymentStatus;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="payment_transaction", type="string", length=255, nullable=true)
     */
    protected $paymentTransaction;

    /**
     * First name of the person
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * Last name of the person
     *
     * @var string
     * @ORM\Column(name="family", type="string", length=255)
     */
    protected $family;

    /**
     * E-mail address of the person
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    /**
     * Phone of the person
     *
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * Phone of the person
     *
     * @var string
     *
     * @ORM\Column(name="baggage_details", type="string", length=255, nullable=true)
     */
    protected $baggageDetails;

    /**
     * Phone of the person
     *
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=255, nullable=true)
     */
    protected $info;

    /**
     * Phone of the person
     *
     * @var string
     *
     * @ORM\Column(name="meet", type="string", length=255, nullable=true)
     */
    protected $meet;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="amount", type="string", length=255, nullable=true)
     */
    protected $amount;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string        $title
     * @return Entertainment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->no;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime     $createdAt
     * @return Entertainment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime     $updatedAt
     * @return Entertainment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return (string)$this->getNo() ?: 'n/a';
    }

    /**
     * Get the value of Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of Price
     *
     * @param mixed price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets the value of from.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the value of from.
     *
     * @param string $from the from
     *
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Gets the value of to.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the value of to.
     *
     * @param string $to the to
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }


    /**
     * Gets the value of distance.
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Sets the value of distance.
     *
     * @param string $distance the distance
     *
     * @return self
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the value of date.
     *
     * @param string $date the date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets the value of passengers.
     *
     * @return string
     */
    public function getPassengers()
    {
        return $this->passengers;
    }

    /**
     * Sets the value of passengers.
     *
     * @param string $passengers the passengers
     *
     * @return self
     */
    public function setPassengers($passengers)
    {
        $this->passengers = $passengers;

        return $this;
    }

    /**
     * Gets the value of paymentType.
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Sets the value of paymentType.
     *
     * @param string $paymentType the payment type
     *
     * @return self
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Gets the value of paymentTransaction.
     *
     * @return string
     */
    public function getPaymentTransaction()
    {
        return $this->paymentTransaction;
    }

    /**
     * Sets the value of paymentTransaction.
     *
     * @param string $paymentTransaction the payment transaction
     *
     * @return self
     */
    public function setPaymentTransaction($paymentTransaction)
    {
        $this->paymentTransaction = $paymentTransaction;

        return $this;
    }

    /**
     * Gets the First name of the person.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the First name of the person.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the Last name of the person.
     *
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Sets the Last name of the person.
     *
     * @param string $family the family
     *
     * @return self
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Gets the E-mail address of the person.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the E-mail address of the person.
     *
     * @param string $email the email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the Phone of the person.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the Phone of the person.
     *
     * @param string $phone the phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets the Phone of the person.
     *
     * @return string
     */
    public function getBaggageDetails()
    {
        return $this->baggageDetails;
    }

    /**
     * Sets the Phone of the person.
     *
     * @param string $baggageDetails the baggage details
     *
     * @return self
     */
    public function setBaggageDetails($baggageDetails)
    {
        $this->baggageDetails = $baggageDetails;

        return $this;
    }

    /**
     * Gets the Phone of the person.
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the Phone of the person.
     *
     * @param string $info the info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets the value of amount.
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets the value of amount.
     *
     * @param mixed $amount the amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Gets the value of start_time.
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Sets the value of start_time.
     *
     * @param string $start_time the start time
     *
     * @return self
     */
    public function setStartTime($start_time)
    {
        $this->start_time = $start_time;

        return $this;
    }

    /**
     * Gets the value of return_time.
     *
     * @return string
     */
    public function getReturnTime()
    {
        return $this->return_time;
    }

    /**
     * Sets the value of return_time.
     *
     * @param string $return_time the return time
     *
     * @return self
     */
    public function setReturnTime($return_time)
    {
        $this->return_time = $return_time;

        return $this;
    }

    /**
     * Gets the value of via.
     *
     * @return string
     */
    public function getVia()
    {
        return $this->via;
    }

    /**
     * Sets the value of via.
     *
     * @param string $via the via
     *
     * @return self
     */
    public function setVia($via)
    {
        $this->via = $via;

        return $this;
    }

    /**
     * Gets the value of duration.
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets the value of duration.
     *
     * @param string $duration the duration
     *
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Gets the value of offer.
     *
     * @return string
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Sets the value of offer.
     *
     * @param string $offer the offer
     *
     * @return self
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Gets the value of from_lat.
     *
     * @return string
     */
    public function getFromLat()
    {
        return $this->from_lat;
    }

    /**
     * Sets the value of from_lat.
     *
     * @param string $from_lat the from lat
     *
     * @return self
     */
    public function setFromLat($from_lat)
    {
        $this->from_lat = $from_lat;

        return $this;
    }

    /**
     * Gets the value of to_lat.
     *
     * @return string
     */
    public function getToLat()
    {
        return $this->to_lat;
    }

    /**
     * Sets the value of to_lat.
     *
     * @param string $to_lat the to lat
     *
     * @return self
     */
    public function setToLat($to_lat)
    {
        $this->to_lat = $to_lat;

        return $this;
    }

    /**
     * Gets the value of from_lng.
     *
     * @return string
     */
    public function getFromLng()
    {
        return $this->from_lng;
    }

    /**
     * Sets the value of from_lng.
     *
     * @param string $from_lng the from lng
     *
     * @return self
     */
    public function setFromLng($from_lng)
    {
        $this->from_lng = $from_lng;

        return $this;
    }

    /**
     * Gets the value of to_lng.
     *
     * @return string
     */
    public function getToLng()
    {
        return $this->to_lng;
    }

    /**
     * Sets the value of to_lng.
     *
     * @param string $to_lng the to lng
     *
     * @return self
     */
    public function setToLng($to_lng)
    {
        $this->to_lng = $to_lng;

        return $this;
    }

    /**
     * Gets the value of via_lat.
     *
     * @return string
     */
    public function getViaLat()
    {
        return $this->via_lat;
    }

    /**
     * Sets the value of via_lat.
     *
     * @param string $via_lat the via lat
     *
     * @return self
     */
    public function setViaLat($via_lat)
    {
        $this->via_lat = $via_lat;

        return $this;
    }

    /**
     * Gets the value of via_lng.
     *
     * @return string
     */
    public function getViaLng()
    {
        return $this->via_lng;
    }

    /**
     * Sets the value of via_lng.
     *
     * @param string $via_lng the via lng
     *
     * @return self
     */
    public function setViaLng($via_lng)
    {
        $this->via_lng = $via_lng;

        return $this;
    }

    /**
     * Gets the value of offer_point.
     *
     * @return string
     */
    public function getOfferPoint()
    {
        return $this->offer_point;
    }

    /**
     * Sets the value of offer_point.
     *
     * @param string $offer_point the offer point
     *
     * @return self
     */
    public function setOfferPoint($offer_point)
    {
        $this->offer_point = $offer_point;

        return $this;
    }

    /**
     * Gets the value of return_date.
     *
     * @return string
     */
    public function getReturnDate()
    {
        return $this->return_date;
    }

    /**
     * Sets the value of return_date.
     *
     * @param string $return_date the return date
     *
     * @return self
     */
    public function setReturnDate($return_date)
    {
        $this->return_date = $return_date;

        return $this;
    }

    /**
     * Gets the value of no.
     *
     * @return string
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * Sets the value of no.
     *
     * @param string $no the no
     *
     * @return self
     */
    public function setNo($no)
    {
        $this->no = $no;

        return $this;
    }

    /**
     * Gets the value of paymentStatus.
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Sets the value of paymentStatus.
     *
     * @param string $paymentStatus the payment status
     *
     * @return self
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Gets the Phone of the person.
     *
     * @return string
     */
    public function getMeet()
    {
        return $this->meet;
    }

    /**
     * Sets the Phone of the person.
     *
     * @param string $meet the meet
     *
     * @return self
     */
    public function setMeet($meet)
    {
        $this->meet = $meet;

        return $this;
    }
}
