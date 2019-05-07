<?php
/**
 * CHttpSession mocked for controller tests.
 */
namespace fidelize\YiiPhactory;

class FPhactorySessionMock extends \CHttpSession
{
    /**
     * Stubbed session storage
     * @var array
     */
    protected $_session;

    /**
     * Stubbed session timeout
     * @var integer
     */
    protected $_timeout = 1440;

    /**
     * Stubbed session ID
     * @var integer
     */
    protected $_id = 1;

    /**
     * Stubbed session name
     * @var string
     */
    protected $_name = 'session';

    /**
     * Stubbed session path
     * @var string
     */
    protected $_path = '/';

    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        parent::init();

        if($this->autoStart)
            $this->open();
        register_shutdown_function(array($this,'close'));
    }

    /**
     * Returns a value indicating whether to use custom session storage.
     * This method should be overriden to return true if custom session storage handler should be used.
     * If returning true, make sure the methods {@link openSession}, {@link closeSession}, {@link readSession},
     * {@link writeSession}, {@link destroySession}, and {@link gcSession} are overridden in child
     * class, because they will be used as the callback handlers.
     * The default implementation always return false.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return false;
    }

    /**
     * Starts the session if it has not started yet.
     */
    public function open()
    {
        $this->_session = [];
    }

    /**
     * Ends the current session and store session data.
     */
    public function close()
    {
        $this->_session = null;
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     */
    public function destroy()
    {
        $this->_session = null;
    }

    /**
     * @return boolean whether the session has started
     */
    public function getIsStarted()
    {
        return $this->_session !== null;
    }

    /**
     * @return string the current session ID
     */
    public function getSessionID()
    {
        return $this->_id;
    }

    /**
     * @param string $value the session ID for the current session
     */
    public function setSessionID($value)
    {
        $this->_id = $value;
    }

    /**
     * Updates the current session id with a newly generated one .
     * Please refer to {@link http://php.net/session_regenerate_id} for more details.
     * @param boolean $deleteOldSession Whether to delete the old associated session file or not.
     * @since 1.1.8
     */
    public function regenerateID($deleteOldSession=false)
    {
        $this->_id = $this->setSessionID(uniqid());
    }

    /**
     * @return string the current session name
     */
    public function getSessionName()
    {
        return $this->_name;
    }

    /**
     * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
     */
    public function setSessionName($value)
    {
        $this->_name = $value;
    }

    /**
     * @return string the current session save path, defaults to {@link http://php.net/session.save_path}.
     */
    public function getSavePath()
    {
        return $this->_path;
    }

    /**
     * @param string $value the current session save path
     * @throws CException if the path is not a valid directory
     */
    public function setSavePath($value)
    {
        $this->_path = $value;
    }

    /**
     * @return array the session cookie parameters.
     * @see http://us2.php.net/manual/en/function.session-get-cookie-params.php
     */
    public function getCookieParams()
    {
        return [];
    }

    /**
     * Sets the session cookie parameters.
     * The effect of this method only lasts for the duration of the script.
     * Call this method before the session starts.
     * @param array $value cookie parameters, valid keys include: lifetime, path,
     * domain, secure, httponly. Note that httponly is all lowercase.
     * @see http://us2.php.net/manual/en/function.session-set-cookie-params.php
     */
    public function setCookieParams($value)
    {
    }

    /**
     * @return string how to use cookie to store session ID. Defaults to 'Allow'.
     */
    public function getCookieMode()
    {
        return 'none';
    }

    /**
     * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
     */
    public function setCookieMode($value)
    {
    }

    /**
     * @return float the probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
     */
    public function getGCProbability()
    {
        return 1;
    }

    /**
     * @param float $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
     * @throws CException if the value is beyond [0,100]
     */
    public function setGCProbability($value)
    {
    }

    /**
     * @return boolean whether transparent sid support is enabled or not, defaults to false.
     */
    public function getUseTransparentSessionID()
    {
        return false;
    }

    /**
     * @param boolean $value whether transparent sid support is enabled or not.
     */
    public function setUseTransparentSessionID($value)
    {
    }

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout($value)
    {
        $this->_timeout = $value;
    }

    /**
     * Session open handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath,$sessionName)
    {
        return true;
    }

    /**
     * Session close handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @return boolean whether session is closed successfully
     */
    public function closeSession()
    {
        return true;
    }

    /**
     * Session read handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        return '';
    }

    /**
     * Session write handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id,$data)
    {
        return true;
    }

    /**
     * Session destroy handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        return true;
    }

    //------ The following methods enable CHttpSession to be CMap-like -----

    /**
     * Returns an iterator for traversing the session variables.
     * This method is required by the interface IteratorAggregate.
     * @return CHttpSessionIterator an iterator for traversing the session variables.
     */
    public function getIterator()
    {
        throw new Exception('CHttpSessionIterator has not been mocked yet.');
        //return new CHttpSessionIterator;
    }

    /**
     * Returns the number of items in the session.
     * @return integer the number of session variables
     */
    public function getCount()
    {
        return count($this->_session);
    }

    /**
     * Returns the number of items in the session.
     * This method is required by Countable interface.
     * @return integer number of items in the session.
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * @return array the list of session variable names
     */
    public function getKeys()
    {
        return array_keys($this->_session);
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is very similar to {@link itemAt} and {@link offsetGet},
     * except that it will return $defaultValue if the session variable does not exist.
     * @param mixed $key the session variable name
     * @param mixed $defaultValue the default value to be returned when the session variable does not exist.
     * @return mixed the session variable value, or $defaultValue if the session variable does not exist.
     * @since 1.1.2
     */
    public function get($key,$defaultValue=null)
    {
        return isset($this->_session[$key]) ? $this->_session[$key] : $defaultValue;
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is exactly the same as {@link offsetGet}.
     * @param mixed $key the session variable name
     * @return mixed the session variable value, null if no such variable exists
     */
    public function itemAt($key)
    {
        return isset($this->_session[$key]) ? $this->_session[$key] : null;
    }

    /**
     * Adds a session variable.
     * Note, if the specified name already exists, the old value will be removed first.
     * @param mixed $key session variable name
     * @param mixed $value session variable value
     */
    public function add($key,$value)
    {
        $this->_session[$key]=$value;
    }

    /**
     * Removes a session variable.
     * @param mixed $key the name of the session variable to be removed
     * @return mixed the removed value, null if no such session variable.
     */
    public function remove($key)
    {
        if(isset($this->_session[$key]))
        {
            $value=$this->_session[$key];
            unset($this->_session[$key]);
            return $value;
        }
        else
            return null;
    }

    /**
     * Removes all session variables
     */
    public function clear()
    {
        foreach(array_keys($this->_session) as $key)
            unset($this->_session[$key]);
    }

    /**
     * @param mixed $key session variable name
     * @return boolean whether there is the named session variable
     */
    public function contains($key)
    {
        return isset($this->_session[$key]);
    }

    /**
     * @return array the list of all session variables in array
     */
    public function toArray()
    {
        return $this->_session;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_session[$offset]);
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return isset($this->_session[$offset]) ? $this->_session[$offset] : null;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet($offset,$item)
    {
        $this->_session[$offset]=$item;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        unset($this->_session[$offset]);
    }
}
