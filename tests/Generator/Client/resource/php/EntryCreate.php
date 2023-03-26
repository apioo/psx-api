<?php
/**
 * EntryCreate automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\MaxLength;
use PSX\Schema\Attribute\MinLength;
use PSX\Schema\Attribute\Pattern;
use PSX\Schema\Attribute\Required;

#[Required(array('title', 'date'))]
class EntryCreate implements \JsonSerializable
{
    protected ?int $id = null;
    protected ?int $userId = null;
    #[Pattern('[A-z]+')]
    #[MinLength(3)]
    #[MaxLength(16)]
    protected ?string $title = null;
    protected ?\PSX\DateTime\LocalDateTime $date = null;
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
    public function setDate(?\PSX\DateTime\LocalDateTime $date) : void
    {
        $this->date = $date;
    }
    public function getDate() : ?\PSX\DateTime\LocalDateTime
    {
        return $this->date;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('id' => $this->id, 'userId' => $this->userId, 'title' => $this->title, 'date' => $this->date), static function ($value) : bool {
            return $value !== null;
        });
    }
}
