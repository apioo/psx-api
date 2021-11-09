<?php 
/**
 * EntryMessage generated on 0000-00-00
 * @see https://sdkgen.app
 */

class EntryMessage implements \JsonSerializable
{
    /**
     * @var bool|null
     */
    protected $success;
    /**
     * @var string|null
     */
    protected $message;
    /**
     * @param bool|null $success
     */
    public function setSuccess(?bool $success) : void
    {
        $this->success = $success;
    }
    /**
     * @return bool|null
     */
    public function getSuccess() : ?bool
    {
        return $this->success;
    }
    /**
     * @param string|null $message
     */
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    /**
     * @return string|null
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('success' => $this->success, 'message' => $this->message), static function ($value) : bool {
            return $value !== null;
        });
    }
}
