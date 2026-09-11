<?php

declare(strict_types=1);

/**
 * CHttpSession mocked for controller tests.
 */

namespace fidelize\YiiPhactory;

class FPhactorySessionMock extends \CHttpSession
{
    /**
     * Stubbed session storage
     *
     * @var array<string|int, mixed>|null
     */
    protected ?array $_session = null;

    /**
     * Stubbed session timeout
     */
    protected int $_timeout = 1440;

    /**
     * Stubbed session ID
     */
    protected string $_id = '1';

    /**
     * Stubbed session name
     */
    protected string $_name = 'session';

    /**
     * Stubbed session path
     */
    protected string $_path = '/';

    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init(): void
    {
        parent::init();

        if ($this->autoStart) {
            $this->open();
        }
        register_shutdown_function([$this, 'close']);
    }

    /**
     * Returns a value indicating whether to use custom session storage.
     */
    public function getUseCustomStorage(): bool
    {
        return false;
    }

    /**
     * Starts the session if it has not started yet.
     */
    public function open(): void
    {
        $this->_session = [];
    }

    /**
     * Ends the current session and store session data.
     */
    public function close(): void
    {
        $this->_session = null;
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     */
    public function destroy(): void
    {
        $this->_session = null;
    }

    /**
     * @return boolean whether the session has started
     */
    public function getIsStarted(): bool
    {
        return $this->_session !== null;
    }

    /**
     * @return string the current session ID
     */
    public function getSessionID(): string
    {
        return $this->_id;
    }

    /**
     * @param string $value the session ID for the current session
     */
    public function setSessionID(mixed $value): void
    {
        $this->_id = (string) $value;
    }

    /**
     * Updates the current session id with a newly generated one .
     */
    public function regenerateID(mixed $deleteOldSession = false): void
    {
        $this->setSessionID((string) uniqid());
    }

    /**
     * @return string the current session name
     */
    public function getSessionName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
     */
    public function setSessionName(mixed $value): void
    {
        $this->_name = (string) $value;
    }

    /**
     * @return string the current session save path, defaults to {@link http://php.net/session.save_path}.
     */
    public function getSavePath(): string
    {
        return $this->_path;
    }

    /**
     * @param string $value the current session save path
     */
    public function setSavePath(mixed $value): void
    {
        $this->_path = (string) $value;
    }

    /**
     * @return array<string, mixed> the session cookie parameters.
     */
    public function getCookieParams(): array
    {
        return [];
    }

    /**
     * Sets the session cookie parameters.
     *
     * @param array<string, mixed> $value cookie parameters
     */
    public function setCookieParams(mixed $value): void {}

    /**
     * @return string how to use cookie to store session ID. Defaults to 'Allow'.
     */
    public function getCookieMode(): string
    {
        return 'none';
    }

    /**
     * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
     */
    public function setCookieMode(mixed $value): void {}

    /**
     * @return float the probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
     */
    public function getGCProbability(): float
    {
        return 1;
    }

    /**
     * @param float $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
     */
    public function setGCProbability(mixed $value): void {}

    /**
     * @return boolean whether transparent sid support is enabled or not, defaults to false.
     */
    public function getUseTransparentSessionID(): bool
    {
        return false;
    }

    /**
     * @param boolean $value whether transparent sid support is enabled or not.
     */
    public function setUseTransparentSessionID(mixed $value): void {}

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout(): int
    {
        return $this->_timeout;
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout(mixed $value): void
    {
        $this->_timeout = (int) $value;
    }

    /**
     * Session open handler.
     */
    public function openSession(mixed $savePath, mixed $sessionName): bool
    {
        return true;
    }

    /**
     * Session close handler.
     */
    public function closeSession(): bool
    {
        return true;
    }

    /**
     * Session read handler.
     */
    public function readSession(mixed $id): string
    {
        return '';
    }

    /**
     * Session write handler.
     */
    public function writeSession(mixed $id, mixed $data): bool
    {
        return true;
    }

    /**
     * Session destroy handler.
     */
    public function destroySession(mixed $id): bool
    {
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     */
    public function gcSession(mixed $maxLifetime): bool
    {
        return true;
    }

    //------ The following methods enable CHttpSession to be CMap-like -----

    /**
     * Returns an iterator for traversing the session variables.
     */
    public function getIterator(): \Iterator
    {
        throw new \Exception('CHttpSessionIterator has not been mocked yet.');
    }

    /**
     * Returns the number of items in the session.
     *
     * @return int<0, max>
     */
    public function getCount(): int
    {
        return count($this->sessionData());
    }

    /**
     * Returns the number of items in the session.
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->getCount();
    }

    /**
     * @return list<string|int> the list of session variable names
     */
    public function getKeys(): array
    {
        return array_keys($this->sessionData());
    }

    /**
     * Returns the session variable value with the session variable name.
     */
    public function get($key, $defaultValue = null): mixed
    {
        $session = $this->sessionData();

        return array_key_exists($key, $session) ? $session[$key] : $defaultValue;
    }

    /**
     * Returns the session variable value with the session variable name.
     */
    public function itemAt($key): mixed
    {
        $session = $this->sessionData();

        return array_key_exists($key, $session) ? $session[$key] : null;
    }

    /**
     * Adds a session variable.
     */
    public function add($key, $value): void
    {
        $this->ensureSession();
        $this->_session[$key] = $value;
    }

    /**
     * Removes a session variable.
     */
    public function remove($key): mixed
    {
        $session = $this->sessionData();

        if (!array_key_exists($key, $session)) {
            return null;
        }

        $value = $session[$key];
        unset($this->_session[$key]);

        return $value;
    }

    /**
     * Removes all session variables
     */
    public function clear(): void
    {
        if ($this->_session === null) {
            return;
        }

        foreach (array_keys($this->_session) as $key) {
            unset($this->_session[$key]);
        }
    }

    /**
     * @param int|string $key session variable name
     */
    public function contains($key): bool
    {
        return array_key_exists($key, $this->sessionData());
    }

    /**
     * @return array<string|int, mixed> the list of all session variables in array
     */
    public function toArray(): array
    {
        return $this->sessionData();
    }

    /**
     * This method is required by the interface ArrayAccess.
     */
    public function offsetExists(mixed $offset): bool
    {
        if (!is_int($offset) && !is_string($offset)) {
            return false;
        }

        return array_key_exists($offset, $this->sessionData());
    }

    /**
     * This method is required by the interface ArrayAccess.
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!is_int($offset) && !is_string($offset)) {
            return null;
        }

        $session = $this->sessionData();

        return array_key_exists($offset, $session) ? $session[$offset] : null;
    }

    /**
     * This method is required by the interface ArrayAccess.
     */
    public function offsetSet(mixed $offset, mixed $item): void
    {
        $this->ensureSession();

        if ($offset === null) {
            $this->_session[] = $item;

            return;
        }

        if (!is_int($offset) && !is_string($offset)) {
            return;
        }

        $this->_session[$offset] = $item;
    }

    /**
     * This method is required by the interface ArrayAccess.
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!is_int($offset) && !is_string($offset) || $this->_session === null) {
            return;
        }

        unset($this->_session[$offset]);
    }

    /**
     * @return array<string|int, mixed>
     */
    private function sessionData(): array
    {
        return $this->_session ?? [];
    }

    private function ensureSession(): void
    {
        $this->_session ??= [];
    }
}
