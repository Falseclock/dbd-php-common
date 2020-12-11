<?php
/***************************************************************************
 *   Copyright (C) 2004-2007 by Sveta A. Smirnova                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 **************************************************************************/

namespace DBD\Common;

abstract class Singleton implements Instantiatable
{
    /**
     * Все вызванные ранее инстансы классов
     *
     * @var array $instances
     */
    private static $instances = [];

    /**
     * Singleton constructor. You can't create me
     */
    protected function __construct()
    {
    }

    /**
     * Функция возвращает массив всех инстанцированных классов
     *
     * @return array
     */
    final public static function getAllInstances(): array
    {
        return self::$instances;
    }

    /**
     * @return Instantiatable|Singleton|static
     */
    public static function me(): Instantiatable
    {
        return self::getInstance(get_called_class());
    }

    /**
     * Функция получения инстанса класса
     *
     * @param string $class
     * @param null $args
     *
     * @return Singleton
     */
    final public static function getInstance(string $class, $args = null /* , ... */): Singleton
    {
        // for Singleton::getInstance('class_name', $arg1, ...) calling
        if (2 < func_num_args()) {
            $args = func_get_args();
            array_shift($args);
        }

        if (!isset(self::$instances[$class])) {
            $object = $args ? new $class($args) : new $class;

            if (!($object instanceof Singleton))
                trigger_error("Class '{$class}' is something not a Singleton's child");

            return self::$instances[$class] = $object;
        } else {
            return self::$instances[$class];
        }
    }

    /**
     * do not clone me
     */
    final private function __clone()
    {
    }
}
