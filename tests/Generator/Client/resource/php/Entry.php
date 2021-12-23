<?php
/**
 * Entry generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\MaxLength;
use PSX\Schema\Attribute\MinLength;
use PSX\Schema\Attribute\Pattern;

class Entry implements \JsonSerializable
{
    protected ?int $id = null;
    protected ?int $userId = null;
    #[Pattern('[A-z]+')]
    #[MinLength(3)]
    #[MaxLength(16)]
    protected ?string $title = null;
    protected ?\DateTime $date = null;
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setUserId(?int $userId) : void
    {
        $this->userId = $userId;
    }
    public function getUserId() : ?int
    {
        return $this->userId;
    }
    public function setTitle(?string $title) : void
    {
        $this->title = $title;
    }
    public function getTitle() : ?string
    {
        return $this->title;
    }
    public function setDate(?\DateTime $date) : void
    {
        $this->date = $date;
    }
    public function getDate() : ?\DateTime
    {
        return $this->date;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('id' => $this->id, 'userId' => $this->userId, 'title' => $this->title, 'date' => $this->date), static function ($value) : bool {
            return $value !== null;
        });
    }
}
