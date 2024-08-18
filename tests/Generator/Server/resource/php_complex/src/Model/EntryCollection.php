class EntryCollection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    /**
     * @var array<Entry>|null
     */
    protected ?array $entry = null;
    /**
     * @param array<Entry>|null $entry
     */
    public function setEntry(?array $entry) : void
    {
        $this->entry = $entry;
    }
    /**
     * @return array<Entry>|null
     */
    public function getEntry() : ?array
    {
        return $this->entry;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('entry', $this->entry);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}
