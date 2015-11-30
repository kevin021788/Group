<?php

namespace core\Group\Container;

use ReflectionClass;
use Exception;
use App;
use core\Group\Exceptions\NotFoundException;
use core\Group\Contracts\Container\Container as ContainerContract;

class Container implements ContainerContract
{
    protected $app;

	private static $_instance;

    protected $timezone;

    protected $environment;

    protected $appPath;

    public function __construct()
    {
        $this -> setTimezone();
        $this -> setEnvironment();
        $this -> setAppPath();
    }

	/**
	 * build a moudle class
	 *
	 * @param  class
	 * @return ReflectionClass class
	 */
	public function buildMoudle($class)
	{
		if (!class_exists($class)) {

			throw new NotFoundException("Class ".$class." not found !");

		}

		$reflector = new ReflectionClass($class);

		return $reflector;
	}

    /**
     * do the moudle class action
     *
     * @param  class
     * @param  action
     * @param  array parameters
     * @return string
     */
	public function doAction($class, $action, array $parameters = [])
	{
		$reflector = $this -> buildMoudle($class);

		if(!$reflector -> hasMethod($action)) {

			throw new NotFoundException("Class ".$class." exist ,But the Action ".$action." not found");
		}

		$instanc = $reflector -> newInstanceArgs(array(App::getInstance()));
		$method = $reflector -> getmethod($action);
		return $method -> invokeArgs($instanc, $parameters);

	}

    /**
     * return single class
     *
     * @return core\Group\Container Container
     */
	public static function getInstance(){

		if(!(self::$_instance instanceof self)){

			self::$_instance = new self;
		}

		return self::$_instance;
	}


    /**
     * 设置时区
     *
     */
    public function setTimezone()
    {
        $this -> timezone = \Config::get('app::timezone');
        date_default_timezone_set($this -> getTimezone());
    }


    /**
     * 获取当前时区
     *
     */
    public function getTimezone()
    {
        return $this -> timezone;
    }

    /**
     * 设置环境
     *
     *@return string prod｜dev
     */
    public function getEnvironment()
    {
        return $this -> environment;
    }

    /**
     * 获取当前环境
     *
     */
    public function setEnvironment()
    {
        $this -> environment = \Config::get('app::environment');
    }

    public function setAppPath()
    {
        $this -> appPath = __ROOT__;
    }

    public function getAppPath()
    {
        return $this -> appPath;
    }
}