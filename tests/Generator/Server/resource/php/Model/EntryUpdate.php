<?php
/**
 * EntryUpdate automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Model;


class EntryUpdate implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?int $id = null;
    protected ?int $userId = null;
    protected ?string $title = null;
    protected ?\PSX\DateTime\LocalDateTime $date = null;
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }
    public function getUserId(): ?int
    {
        return $this->userId;
    }
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setDate(?\PSX\DateTime\LocalDateTime $date): void
    {
        $this->date = $date;
    }
    public function getDate(): ?\PSX\DateTime\LocalDateTime
    {
        return $this->date;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('id', $this->id);
        $record->put('userId', $this->userId);
        $record->put('title', $this->title);
        $record->put('date', $this->date);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}
