<?php 
/**
 * Message generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class Message
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
}