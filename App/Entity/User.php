<?php

namespace App\Entity;

use Doctrine\ORM\Events;
use Core\App;

/**
 * @Entity
 * @Table(name="user")
 * @HasLifecycleCallbacks
 **/
class User
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Column(type="string")
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @Column(type="string", length=64)
     */
    private $password;

    /**
     * @Column(type="string")
     */
    protected $salt;

    protected $errors = array();

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @prePersist
     */
    public function prePersist()
    {
        $this->validate('prePersist');
        if (!$this->getErrors()) {
            $this->updatePasswordAndSalt();
        }
    }

    /**
     * @preUpdate
     */
    public function preUpdate()
    {
        $this->validate('preUpdate');
        if (!$this->getErrors()) {
            $this->updatePasswordAndSalt();
        }
    }

    public function validate($scenario)
    {
        $this->errors = array();
        if (empty($this->email)) {
            $this->errors[] = 'Emails shouldn\'t be empty';
        }
        $entityManager = App::getEntityManager();
        if (('prePersist' == $scenario) &&empty($this->plainPassword)) {
            $this->errors[] = 'Password shouldn\'t be empty';
        }

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('u.email')
            ->from('App\\Entity\\User', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $this->getEmail());

        if ($this->getId()) {
            $queryBuilder = $queryBuilder->andWhere('u.id <> :id')
                ->setParameter('id', $this->getId());
        }
        $query = $queryBuilder->getQuery();
        $user = $query->getOneOrNullResult();

        if ($user) {
            $this->errors[] = 'User with email ' . $this->email . ' is already exists';
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function updatePasswordAndSalt()
    {
        if (!empty($this->plainPassword)) {
            $this->salt = md5(mt_rand());
            $this->password = md5($this->getPlainPassword() . $this->getSalt());
        }
    }

    public function verifyPassword($password)
    {
        return ($this->password == md5($password . $this->getSalt()));
    }
}
