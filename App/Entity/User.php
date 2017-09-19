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
    const EMAIL_REG_EXPR = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:'
        .'(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-'
        .'\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*'
        .'\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C'
        .'\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?'
        .'[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|'
        .'(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}'
        .'(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}'
        .'(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}'
        .'(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])'
        .'|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
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
            $this->errors[] = 'Emails shouldn\'t be empty.';
        }
        if (!preg_match(self::EMAIL_REG_EXPR, $this->email)) {
            $this->errors[] = 'Email is not valid.';
        }
        $entityManager = App::getEntityManager();
        if (('prePersist' == $scenario) &&empty($this->plainPassword)) {
            $this->errors[] = 'Password shouldn\'t be empty.';
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
