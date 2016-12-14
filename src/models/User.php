<?php   // src/models/User.php

namespace MiW16\Results\Models;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * User
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UNIQ_TOKEN", columns={"token"}
 *          )
 *      }
 *     )
 * @ORM\Entity
 */
class User implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=40, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=40, nullable=false)
     */
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 0;
        $this->username = '';
        $this->email = '';
        $this->enabled = false;
        $this->token = sha1(uniqid('', true));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled(bool $enabled): User
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * Verifies that the given hash matches the user password.
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin(\DateTime $lastLogin): User
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return User
     */
    public function setToken(string $token): User
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array(
            'id'            => $this->id,
            'username'      => utf8_encode($this->username),
            'email'         => utf8_encode($this->email),
            'enabled'       => $this->enabled,
            'token'         => $this->token
        );
    }
}

/**
 * @SWG\Definition(
 *     definition="User",
 *     required = { "id", "username", "email" },
 *     @SWG\Property(
 *          property    = "id",
 *          description = "User Id",
 *          type        = "integer",
 *          format      = "int32"
 *      ),
 *      @SWG\Property(
 *          property    = "username",
 *          description = "User name",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "email",
 *          description = "User email",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "token",
 *          description = "API access token",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "enabled",
 *          description = "Denotes if user is enabled",
 *          type        = "boolean"
 *      ),
 *      example = {
 *          "id"       = 1508,
 *          "username" = "User name",
 *          "email"    = "User email",
 *          "enabled"  = true,
 *          "token"    = "$2$6a7f5b9e15f9c4a51495"
 *     }
 * )
 * @SWG\Parameter(
 *      name        = "userId",
 *      in          = "path",
 *      description = "ID of user to fetch",
 *      required    = true,
 *      type        = "integer",
 *      format      = "int32"
 * )
 */

/**
 * @SWG\Definition(
 *      definition = "UserData",
 *      @SWG\Property(
 *          property    = "username",
 *          description = "User name",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "email",
 *          description = "User email",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "password",
 *          description = "User password",
 *          type        = "string",
 *          format      = "password"
 *      ),
 *      @SWG\Property(
 *          property    = "enabled",
 *          description = "Denotes if user is enabled",
 *          type        = "boolean"
 *      ),
 *      @SWG\Property(
 *          property    = "token",
 *          description = "User token",
 *          type        = "string"
 *      ),
 *      example = {
 *          "username"  = "User_name",
 *          "email"     = "User_email@example.com",
 *          "password"  = "User_password",
 *          "enabled"   = true,
 *          "token"     = "$2$6a7f5b9e15f9c4a51495"
 *      }
 * )
 */

/**
 * User array definition
 *
 * @SWG\Definition(
 *     definition = "UsersArray",
 *      @SWG\Property(
 *          property    = "users",
 *          description = "Users array",
 *          type        = "array",
 *          items       = {
 *              "$ref": "#/definitions/User"
 *          }
 *      )
 * )
 */